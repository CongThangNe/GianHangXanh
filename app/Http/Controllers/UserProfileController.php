<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Trang hồ sơ người dùng.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return view('profile.show', compact('user'));
    }

    /**
     * Cập nhật thông tin hồ sơ (không bao gồm mật khẩu).
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'    => ['required', 'string', 'min:3'],
            'email'   => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'avatar'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        // Upload avatar nếu có
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu tồn tại
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_path'] = $path;
        }

        unset($validated['avatar']); // Không lưu file input vào DB

        $user->update($validated);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Cập nhật hồ sơ thành công!');
    }

    /**
     * Hiển thị trang đổi mật khẩu.
     */
    public function editPassword()
    {
        return view('profile.change-password');
    }

    /**
     * Xử lý đổi mật khẩu.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:6', 'confirmed'],
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])
                ->withInput();
        }

        // Lưu mật khẩu mới
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}

