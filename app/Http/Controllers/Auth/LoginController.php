<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        if (Auth::attempt($request->only('email', 'password'))) {
            /** @var \App\Models\User $user */
            $user  = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message'           => 'Đăng nhập thành công',
                'user'              => $user,
                'token'             => $token,
                'expires_in_minutes' => config('sanctum.expiration', 30)
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Sai email hoặc mật khẩu.'],
        ]);
    }
}