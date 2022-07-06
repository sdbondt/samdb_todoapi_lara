<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function signup () {
        $attributes = request()->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'min:7', 'max:255']
        ]);

        $user = User::create($attributes);
        $token = $user->createToken('authToken')->plainTextToken;

        return ['token' => $token];
    }

    public function login() {
        $attributes = request()->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);
        $user = User::where('email', request('email'))->first();
        if(!$user) {
            return ['msg' => 'Invalid credentials'];
        } elseif(!auth()->attempt($attributes)) {
            return ['msg' => 'Invalid credentials'];
        } else {
            $token = $user->createToken('authToken')->plainTextToken;
            return ['token' => $token];
        }
    }
}
