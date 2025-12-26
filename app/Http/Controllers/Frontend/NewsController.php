<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    // Trang tổng hợp tin tức
    public function index()
    {
        $news = News::where('status', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return view('frontend.news.index', compact('news'));
        
    }

    // Trang chi tiết tin tức
    public function show($id)
    {
        $news = News::where('status', 1)->findOrFail($id);

        return view('frontend.news.show', compact('news'));
    }
}
