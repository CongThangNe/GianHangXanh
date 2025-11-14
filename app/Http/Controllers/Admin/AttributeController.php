    <?php
    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Attribute;

    class AttributeController extends Controller
    {
        public function index()
        {
            $attributes = Attribute::orderBy('name')->get();
            return view('admin.attributes.index', compact('attributes'));
        }

        public function create()
        {
            return view('admin.attributes.create');
        }

        public function store(Request $request)
        {
            $data = $request->validate([
                'name' => 'required|string|max:100|unique:attributes,name',
            ]);

            \App\Models\Attribute::create($data);
            return redirect()->route('admin.attributes.index')->with('success', 'Tạo thuộc tính thành công');
        }

        public function edit($id)
        {
            $attribute = Attribute::findOrFail($id);
            return view('admin.attributes.edit', compact('attribute'));
        }

        public function update(Request $request, $id)
        {
            $attribute = Attribute::findOrFail($id);
            $data = $request->validate([
                'name' => 'required|string|max:100|unique:attributes,name,'.$attribute->id,
            ]);
            $attribute->update($data);
            return redirect()->route('admin.attributes.index')->with('success', 'Cập nhật thuộc tính thành công');
        }

        public function destroy($id)
        {
            $attribute = Attribute::findOrFail($id);
            $attribute->delete();
            return redirect()->route('admin.attributes.index')->with('success', 'Xóa thuộc tính thành công');
        }
    }
