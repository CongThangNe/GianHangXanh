<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function edit(User $user)
    {
        $roles = User::roles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $roles = array_keys(User::roles());

        $validated = $request->validate([
            'role' => ['required', 'in:' . implode(',', $roles)],
        ]);

        $user->update([
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Đã cập nhật vai trò cho tài khoản.');
    }

    /**
     * Xóa tài khoản người dùng.
     * - Không cho xóa chính tài khoản đang đăng nhập
     * - Không cho xóa tài khoản Admin (tránh mất quyền quản trị)
     */
    public function destroy(Request $request, User $user)
    {
        // Không cho xóa chính mình
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập.');
        }

        // Không cho xóa tài khoản admin
        if (($user->role ?? null) === User::ROLE_ADMIN) {
            return back()->with('error', 'Không thể xóa tài khoản Admin.');
        }

        try {
            $user->delete();
            return back()->with('success', 'Đã xóa tài khoản.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Xóa tài khoản thất bại. Vui lòng thử lại.');
        }
    }
}
