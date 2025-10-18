<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name'=>'required']);
        Category::create($request->only('name','description'));
        return redirect()->route('admin.categories.index')->with('success','Thêm danh mục thành công');
    }

    // HÀM SỬA
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    //  HÀM CẬP NHẬT
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = Category::findOrFail($id);
        $category->update($request->only('name','description'));
        return redirect()->route('admin.categories.index')->with('success','Cập nhật danh mục thành công');
    }

    // 🟢 HÀM XÓA
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return redirect()->route('admin.categories.index')->with('success','Đã xóa danh mục');
    }
}
