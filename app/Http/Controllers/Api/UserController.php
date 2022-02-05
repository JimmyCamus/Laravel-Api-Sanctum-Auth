<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required',],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed']
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => 1,
            'message' => 'Register successful'
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!isset($user->id)) {
            return response()->json([
                'status' => 0,
                'message' => 'The given data was invalid',
                'errors' =>  (object)['email' => ['The email doesnt exist']]
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'The given data was invalid',
                'errors' =>  (object)['password' => ['The password doesnt match']]
            ], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Login Successful',
            'data' => (object)['user_token' => $token]
        ]);
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 1,
            'message' => 'logout successful',
        ]);
    }
    public function userProfile()
    {
        return response()->json([
            'status' => 1,
            'message' => 'user data',
            'data' => auth()->user(),
        ]);
    }
}
