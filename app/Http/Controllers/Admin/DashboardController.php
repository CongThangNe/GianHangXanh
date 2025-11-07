<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang Dashboard của Admin.
     */
    public function index(Request $request)
    {
        // Nếu là AJAX request → chỉ trả về phần nội dung (phục vụ load động)
        if ($request->ajax()) {
            return view('admin.dashboard');
        }

        // Nếu truy cập trực tiếp → trả về layout admin, kèm nội dung dashboard
        return view('layouts.admin', [
            'content' => view('admin.dashboard')->render(),
        ]);
    }
}
