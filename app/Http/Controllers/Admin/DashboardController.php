<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Nếu là AJAX request → chỉ trả về phần nội dung
        if ($request->ajax()) {
            return view('admin.dashboard');
        }

        // Nếu là truy cập trực tiếp → load trong layout admin
        return view('layouts.admin', [
            'content' => view('admin.dashboard')->render()
        ]);
    }
}
