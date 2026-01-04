<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // Chỉ Admin mới được phép đổi role
        if ((auth()->user()->role ?? null) !== User::ROLE_ADMIN) {
            abort(403, 'Bạn không có quyền thay đổi vai trò.');
        }

        $roles = array_keys(User::roles());

        $validated = $request->validate([
            'role' => ['required', 'in:' . implode(',', $roles)],
        ]);

        // Nếu có thay đổi role thì bắt buộc phải xác nhận (hidden input + JS confirm)
        if (($user->role ?? null) !== $validated['role']) {
            $request->validate([
                'confirm_role_change' => ['accepted'],
            ], [
                'confirm_role_change.accepted' => 'Vui lòng xác nhận thay đổi vai trò trước khi lưu.',
            ]);
        }

        // Không cho phép hạ quyền admin cuối cùng (tránh mất quyền quản trị hệ thống)
        if (($user->role ?? null) === User::ROLE_ADMIN && $validated['role'] !== User::ROLE_ADMIN) {
            $adminCount = User::query()->where('role', User::ROLE_ADMIN)->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['role' => 'Hệ thống phải có ít nhất 1 Admin. Không thể hạ quyền admin cuối cùng.'])->withInput();
            }
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        // Nếu admin tự hạ quyền (sang staff/customer) thì đăng xuất luôn để tránh còn session admin.
        if (Auth::id() === $user->id && $validated['role'] !== User::ROLE_ADMIN) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Bạn đã thay đổi vai trò của chính mình. Vui lòng đăng nhập lại để tiếp tục.');
        }

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
