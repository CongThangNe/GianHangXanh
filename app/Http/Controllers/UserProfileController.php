<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Hiển thị trang hồ sơ người dùng hiện tại.
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
            'name'   => ['required', 'string', 'min:3'],
            'email'  => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone'  => ['nullable', 'string', 'max:20'],
            'address'=> ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        // Xử lý upload avatar nếu có
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu tồn tại
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_path'] = $path;
        }

        // Không cho phép cập nhật mật khẩu tại đây
        unset($validated['avatar']);

        $user->update($validated);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Cập nhật hồ sơ thành công!');
    }

    /**
     * Cập nhật mật khẩu người dùng.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()
            ->route('profile.show')
            ->with('success_password', 'Đổi mật khẩu thành công!');
    }
}
