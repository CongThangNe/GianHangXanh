<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // 1. Danh sách tin
    public function index()
    {
        $news = News::orderBy('id', 'desc')->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    // 2. Form thêm
    public function create()
    {
        return view('admin.news.create');
    }

    // 3. Xử lý thêm
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'content' => 'required',
        ]);

        $data = $request->only('title','summary','content','status');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        News::create($data);

        return redirect()->route('admin.news.index')
            ->with('success','Thêm tin tức thành công');
    }

    // 4. Form sửa
    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    // 5. Xử lý sửa
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $data = $request->only('title','summary','content','status');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $news->update($data);

        return redirect()->route('admin.news.index')
            ->with('success','Cập nhật thành công');
    }

    // 6. Xóa
    public function destroy($id)
    {
        News::destroy($id);

        return redirect()->route('admin.news.index')
            ->with('success','Xóa thành công');
    }
}

