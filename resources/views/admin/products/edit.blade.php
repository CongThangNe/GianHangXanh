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
            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Danh mục</label>
            <select name="category_id" class="form-select" required>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                  {{ $cat->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Giá mặc định</label>
            <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" required min="0" step="0.01">
          </div>

          <div class="col-md-6">
            <label class="form-label">Hình ảnh</label>
            <input type="file" name="image" class="form-control">
            @if($product->image)
              <div class="mt-2">
                <img src="{{ asset('storage/'.$product->image) }}" width="100" class="rounded border">
              </div>
            @endif
          </div>

          <div class="col-12">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
          </div>
        </div>

        {{-- --- Biến thể sản phẩm --- --}}
        <div class="card mt-4">
          <div class="card-header">Biến thể sản phẩm</div>
          <div class="card-body">

            {{-- Tick chọn thuộc tính --}}
            <div class="mb-3">
              <label class="form-label d-block">Chọn thuộc tính</label>
              <div id="attrCheckboxes" class="row g-2">
                @foreach($attributes as $attr)
                  <div class="col-md-3">
                    <div class="form-check">
                      <input
                        class="form-check-input js-attr-check"
                        type="checkbox"
                        value="{{ $attr->id }}"
                        id="attr-{{ $attr->id }}"
                        {{ $product->variants->flatMap->values->pluck('attribute_id')->contains($attr->id) ? 'checked' : '' }}>
                      <label class="form-check-label" for="attr-{{ $attr->id }}">{{ $attr->name }}</label>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            <div id="attrValuesContainer" class="row g-3"></div>

            <div class="table-responsive mt-3 {{ $product->variants->count() ? '' : 'd-none' }}" id="variantsTableWrap">
              <table class="table table-bordered align-middle" id="variantsTable">
                <thead>
                  <tr>
                    <th>Biến thể</th>
                    <th>Mã mặt hàng</th>
                    <th width="160">Giá</th>
                    <th width="140">Tồn kho</th>
                    <th width="80">Xóa</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($product->variants as $idx => $variant)
                    @php
                      $valueIds = $variant->values->pluck('id')->toArray();
                      $labels = $variant->values->pluck('value')->toArray();
                    @endphp
                    <tr data-key="{{ implode('-', $valueIds) }}">
                      <td>{{ implode(' / ', $labels) }}
                        <input type="hidden" class="js-value-ids" value="{{ implode(',', $valueIds) }}">
                      </td>
                      <td><input type="text" class="form-control form-control-sm js-sku" name="variants[{{ $idx }}][sku]" value="{{ $variant->sku }}"></td>
                      <td><input type="number" class="form-control form-control-sm js-price" name="variants[{{ $idx }}][price]" value="{{ $variant->price }}"></td>
                      <td><input type="number" class="form-control form-control-sm js-stock" name="variants[{{ $idx }}][stock]" value="{{ $variant->stock }}"></td>
                      <td><button type="button" class="btn btn-sm btn-outline-danger js-del">X</button></td>
                      @foreach($valueIds as $k => $vid)
                        <input type="hidden" name="variants[{{ $idx }}][value_ids][{{ $k }}]" value="{{ $vid }}">
                      @endforeach
                    </tr>
                  @endforeach
                </tbody>
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

{{-- --- PHP prepare data --- --}}
@php
$attrs = $attributes->map(function($a){
    return [
        'id' => $a->id,
        'name' => $a->name,
        'values' => $a->values->map(function($v){
            return [
                'id' => $v->id,
                'value' => $v->value,
            ];
        })->values()
    ];
});

$existingVariants = $product->variants->map(function($v){
    return [
        'sku' => $v->sku,
        'price' => $v->price,
        'stock' => $v->stock,
        'values' => $v->values->map(function($vv){
            return [
                'id' => $vv->id,
                'label' => $vv->value,
            ];
        })->values()
    ];
});
@endphp

<script>
(function(){
  const attributes = @json($attrs);
  const existingVariants = @json($existingVariants);

  const container  = document.getElementById('attrValuesContainer');
  const tableWrap  = document.getElementById('variantsTableWrap');
  const tbody      = document.querySelector('#variantsTable tbody');
  const payloadBox = document.getElementById('variantsPayload');
  const byId = Object.fromEntries(attributes.map(a => [a.id, a]));

  // Tick thuộc tính
  document.querySelectorAll('.js-attr-check').forEach(cb=>{
    cb.addEventListener('change', renderValuePickers);
  });

  function renderValuePickers(){
    container.innerHTML = '';
    const selected = Array.from(document.querySelectorAll('.js-attr-check:checked')).map(cb => parseInt(cb.value));
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
    bindPickerEvents();
  }

  function bindPickerEvents(){
    document.querySelectorAll('.js-attr-values').forEach(sel=>{
      sel.addEventListener('change', buildVariants);
    });
  }

  function cartesian(arrays){
    return arrays.reduce((a,b)=>a.flatMap(d=>b.map(e=>d.concat([e]))), [[]]);
  }

  function buildVariants(){
    const pickers = Array.from(document.querySelectorAll('.js-attr-values'));
    if (!pickers.length) return;
    const groups = pickers.map(sel=>Array.from(sel.selectedOptions).map(o=>({
      id:parseInt(o.value), label:o.textContent.trim()
    }))).filter(g=>g.length);
    if (!groups.length) { tbody.innerHTML=''; tableWrap.classList.add('d-none'); serializePayload(); return; }

    const combos = cartesian(groups);
    const existingKeys = new Set(Array.from(tbody.children).map(tr=>tr.dataset.key));
    const newKeys = new Set();

    combos.forEach(values=>{
      const key = normalizeKey(values.map(v=>v.id));
      newKeys.add(key);
      if (!existingKeys.has(key)){
        addVariantRow(values.map(v=>v.id), values.map(v=>v.label));
      }
    });

    Array.from(tbody.children).forEach(tr=>{
      if (!newKeys.has(tr.dataset.key)) tr.remove();
    });

    if (tbody.children.length) tableWrap.classList.remove('d-none');
    else tableWrap.classList.add('d-none');

    serializePayload();
  }

  function addVariantRow(valueIds, valueLabels){
    const key = normalizeKey(valueIds);
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
      if (!tbody.children.length) tableWrap.classList.add('d-none');
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

  function normalizeKey(arr){ return arr.map(Number).sort((a,b)=>a-b).join('-'); }
  function toSku(labels){
    return labels.map(l=>l.normalize('NFD')
      .replace(/[\u0300-\u036f]/g,'')
      .replace(/[^a-zA-Z0-9]+/g,'-')
      .replace(/^-+|-+$/g,'')
      .toUpperCase()
    ).join('-');
  }

  // render ban đầu
  renderValuePickers();
})();
</script>
@endsection
