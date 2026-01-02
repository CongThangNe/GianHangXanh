<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Sai tài khoản/mật khẩu
        if (!Auth::attempt($request->only('email', 'password'))) {
            // Nếu là web (form) -> trả về trang trước + lỗi
            if (!$request->expectsJson()) {
                return back()
                    ->withErrors(['email' => 'Sai email hoặc mật khẩu.'])
                    ->withInput();
            }

            // Nếu là API -> giữ như cũ
            throw ValidationException::withMessages([
                'email' => ['Sai email hoặc mật khẩu.'],
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Nếu là web -> regenerate session + redirect theo vai trò
        if (!$request->expectsJson()) {
            $request->session()->regenerate();

            if (in_array($user->role, ['admin', 'staff'], true)) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/');
        }

        // Nếu là API -> trả JSON + token (giữ logic token)
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message'             => 'Đăng nhập thành công',
            'user'                => $user,
            'token'               => $token,
            'expires_in_minutes'  => config('sanctum.expiration', 30),
        ]);
    }
}
