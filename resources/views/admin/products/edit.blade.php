@extends('layouts.admin')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Chỉnh sửa sản phẩm</h5>
      <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-secondary">Quay lại</a>
    </div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Tên sản phẩm</label>
            <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Danh mục</label>
            <select name="category_id" class="form-select">
              <option value="">-- Chọn danh mục --</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected($product->category_id == $cat->id)>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Giá mặc định</label>
            <input type="number" name="price" value="{{ $product->price }}" class="form-control" required min="0" step="0.01">
          </div>

          <div class="col-md-6">
            <label class="form-label">Hình ảnh</label>
            <input type="file" name="image" class="form-control">
            @if ($product->image)
              <img src="{{ asset('storage/'.$product->image) }}" class="mt-2 rounded" width="120">
            @endif
          </div>

          <div class="col-12">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-header">Biến thể sản phẩm</div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Chọn thuộc tính</label>
              <select id="attrSelect" class="form-select" multiple>
                @foreach($attributes as $attr)
                  <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                @endforeach
              </select>
            </div>

            <div id="attrValuesContainer" class="row g-3"></div>

            <div class="d-flex gap-2 mt-2">
              <button type="button" id="btnBuildVariants" class="btn btn-outline-primary">Tạo biến thể</button>
              <button type="button" id="btnClearVariants" class="btn btn-outline-danger">Xóa tất cả dòng</button>
            </div>

            <div class="table-responsive mt-3" id="variantsTableWrap">
              <table class="table table-bordered align-middle" id="variantsTable">
                <thead>
                  <tr>
                    <th>Biến thể</th>
                    <th>SKU</th>
                    <th width="160">Giá</th>
                    <th width="140">Tồn kho</th>
                    <th width="80">Xóa</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div id="variantsPayload"></div>
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-success">Cập nhật sản phẩm</button>
        </div>
      </form>
    </div>
  </div>
</div>

@php
$attrs = $attributes->map(function($a){
    return [
        'id'=>$a->id,
        'name'=>$a->name,
        'values'=>$a->values->map(function($v){
            return ['id'=>$v->id,'value'=>$v->value];
        })->values()
    ];
});

$preset = $product->variants->map(function($v){
    return [
        'id'=>$v->id,
        'sku'=>$v->sku,
        'price'=>(float)$v->price,
        'stock'=>(int)$v->stock,
        'value_ids'=>$v->values->pluck('id')->values(),
        'label'=>$v->values->map(fn($vv)=>$vv->attribute->name.': '.$vv->value)->implode(' / ')
    ];
});
@endphp

<script>
(function(){
  const attributes = @json($attrs);
  const preset = @json($preset);

  const attrSelect = document.getElementById('attrSelect');
  const container  = document.getElementById('attrValuesContainer');
  const btnBuild   = document.getElementById('btnBuildVariants');
  const btnClear   = document.getElementById('btnClearVariants');
  const tableWrap  = document.getElementById('variantsTableWrap');
  const tbody      = document.querySelector('#variantsTable tbody');
  const payloadBox = document.getElementById('variantsPayload');
  const byId = Object.fromEntries(attributes.map(a => [a.id, a]));

  function renderValuePickers() {
    container.innerHTML = '';
    const selected = Array.from(attrSelect.selectedOptions).map(o => parseInt(o.value));
    selected.forEach(attrId => {
      const attr = byId[attrId];
      const col = document.createElement('div');
      col.className = 'col-md-4';
      col.innerHTML = `
        <label class="form-label">${attr.name} - Giá trị</label>
        <select class="form-select js-attr-values" data-attr="${attr.id}" multiple>
          ${attr.values.map(v => `<option value="${v.id}">${v.value}</option>`).join('')}
        </select>
      `;
      container.appendChild(col);
    });
  }

  attrSelect.addEventListener('change', renderValuePickers);

  function cartesian(arrays) {
    return arrays.reduce((a, b) => a.flatMap(d => b.map(e => d.concat([e]))), [[]]);
  }

  btnBuild.addEventListener('click', () => {
    const pickers = Array.from(document.querySelectorAll('.js-attr-values'));
    if (!pickers.length) return;
    const groups = pickers.map(sel => Array.from(sel.selectedOptions).map(o => ({
      id: parseInt(o.value), label: o.textContent.trim()
    }))).filter(g => g.length);
    if (!groups.length) return;
    const combos = cartesian(groups);
    tableWrap.classList.remove('d-none');
    combos.forEach(values => addVariantRowMerge(values.map(v => v.id), values.map(v => v.label)));
    serializePayload();
  });

  btnClear.addEventListener('click', () => {
    tbody.innerHTML = '';
    serializePayload();
  });

  function addVariantRowMerge(valueIds, valueLabels){
    const key = normalizeKey(valueIds);
    if (document.querySelector(`tr[data-key="${key}"]`)) return;
    const tr = document.createElement('tr');
    const label = valueLabels.join(' / ');
    const skuDefault = toSku(valueLabels);
    tr.dataset.key = key;
    tr.innerHTML = `
      <td>${label}<input type="hidden" class="js-value-ids" value="${valueIds.join(',')}"></td>
      <td><input type="text" class="form-control form-control-sm js-sku" placeholder="${skuDefault}"></td>
      <td><input type="number" step="0.01" min="0" class="form-control form-control-sm js-price" value="0"></td>
      <td><input type="number" step="1" min="0" class="form-control form-control-sm js-stock" value="0"></td>
      <td><button type="button" class="btn btn-sm btn-outline-danger js-del">X</button></td>
    `;
    tr.querySelector('.js-del').addEventListener('click', () => {
      tr.remove();
      serializePayload();
    });
    ['input','change'].forEach(ev => tr.addEventListener(ev, serializePayload, {capture:true}));
    tbody.appendChild(tr);
  }

  function addVariantRowFromPreset(v){
    const tr = document.createElement('tr');
    const key = normalizeKey(v.value_ids);
    tr.dataset.key = key;
    tr.innerHTML = `
      <td>${v.label}<input type="hidden" class="js-value-ids" value="${v.value_ids.join(',')}"></td>
      <td><input type="text" class="form-control form-control-sm js-sku" value="${v.sku||''}"></td>
      <td><input type="number" step="0.01" min="0" class="form-control form-control-sm js-price" value="${v.price}"></td>
      <td><input type="number" step="1" min="0" class="form-control form-control-sm js-stock" value="${v.stock}"></td>
      <td><button type="button" class="btn btn-sm btn-outline-danger js-del">X</button></td>
    `;
    tr.querySelector('.js-del').addEventListener('click', () => {
      tr.remove();
      serializePayload();
    });
    ['input','change'].forEach(ev => tr.addEventListener(ev, serializePayload, {capture:true}));
    tbody.appendChild(tr);
  }

  function serializePayload(){
    payloadBox.innerHTML = '';
    Array.from(tbody.children).forEach((tr, idx) => {
      const price = tr.querySelector('.js-price').value || '0';
      const stock = tr.querySelector('.js-stock').value || '0';
      const sku   = tr.querySelector('.js-sku').value || '';
      const valueIds = tr.querySelector('.js-value-ids').value.split(',').map(s=>parseInt(s));
      appendHidden(`variants[${idx}][price]`, price);
      appendHidden(`variants[${idx}][stock]`, stock);
      appendHidden(`variants[${idx}][sku]`, sku);
      valueIds.forEach((vid, k) => appendHidden(`variants[${idx}][value_ids][${k}]`, vid));
    });
  }

  function appendHidden(name, value){
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    payloadBox.appendChild(input);
  }

  function normalizeKey(arr){
    const ids = arr.map(Number).sort((a,b)=>a-b);
    return ids.join('-');
  }

  function toSku(labels){
    const slug = labels.map(l => l.normalize('NFD')
      .replace(/[\u0300-\u036f]/g,'')
      .replace(/[^a-zA-Z0-9]+/g,'-')
      .replace(/^-+|-+$/g,'')
      .toUpperCase()
    ).join('-');
    return slug;
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (preset && preset.length) {
      preset.forEach(addVariantRowFromPreset);
      serializePayload();
    }
  });
})();
</script>
@endsection
