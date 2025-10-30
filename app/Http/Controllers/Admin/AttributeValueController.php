<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Validation\Rule;

class AttributeValueController extends Controller
{
    public function index()
    {
        $values = AttributeValue::with('attribute')->orderBy('attribute_id')->orderBy('value')->get();
        return view('admin.attribute_values.index', compact('values'));
    }

    public function create()
    {
        $attributes = Attribute::orderBy('name')->get();
        return view('admin.attribute_values.create', compact('attributes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => ['required','string','max:100',
                Rule::unique('attribute_values')->where(function($query) use ($request) {
                    return $query->where('attribute_id', $request->attribute_id);
                })
            ],
        ]);

        AttributeValue::create($data);
        return redirect()->route('admin.attribute_values.index')->with('success','Tạo giá trị thuộc tính thành công');
    }

    public function edit($id)
    {
        $value = AttributeValue::findOrFail($id);
        $attributes = Attribute::orderBy('name')->get();
        return view('admin.attribute_values.edit', compact('value','attributes'));
    }

    public function update(Request $request, $id)
    {
        $value = AttributeValue::findOrFail($id);
        $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => ['required','string','max:100',
                Rule::unique('attribute_values')->where(function($query) use ($request,$id) {
                    return $query->where('attribute_id', $request->attribute_id)->where('id','<>',$id);
                })
            ],
        ]);
        $value->update($data);
        return redirect()->route('admin.attribute_values.index')->with('success','Cập nhật giá trị thuộc tính thành công');
    }

    public function destroy($id)
    {
        $value = AttributeValue::findOrFail($id);
        $value->delete();
        return redirect()->route('admin.attribute_values.index')->with('success','Xóa giá trị thuộc tính thành công');
    }
}
