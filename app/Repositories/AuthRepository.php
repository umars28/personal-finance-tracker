<?php

namespace App\Repositories;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthRepository implements AuthRepositoryInterface
{
    public function register($data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    
    }

    // login, logout, me akan diisi nanti
    public function attemptLogin(Request $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }
    
        return Auth::user();    
    }
    public function logoutCurrentToken($user) {
        $user->currentAccessToken()->delete();
    }
    public function getUserById($userId) {
        return User::find($userId);
    }
}
