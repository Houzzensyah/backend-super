<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $params =  $request->validate([
            'username' => ['required','unique:users,username', 'min:4', 'max:60'],
            'password' => ['required', 'min:5' , 'max:20']
        ]);

        $user = User::create([
            'username' => $params['username'],
            'password' => $params['password']
        ]);

        $token = $user->createToken('token', ['role:user'])->plainTextToken;

        return response()->json([
            "status"=> "success",
            'token' => $token
        ],201);


    }

    public function signin(Request $request)
    {
        $params =  $request->validate([
            'username' => ['required', 'min:4', 'max:60'],
            'password' => ['required', 'min:5' , 'max:20']
        ]);


        $user = User::where('username', $params['username'])->first();
        if($user && Hash::check($params['password'], $user->password)){
            $token = $user->createToken('token', ['role:user'])->plainTextToken;

            return response()->json([
                "status"=> "success",
                'token' => $token,
                'role' => 'user'
            ],200);
        }



        $admin = Administrator::where('username', $params['username'])->first();
        if($admin && Hash::check($params['password'], $admin->password)){
            $token = $admin->createToken('token', ['role:admin'])->plainTextToken;
            return response()->json([
                "status"=> "success",
                'token' => $token,
                'role' => 'admin'
            ],200);
        }

        if(!User::where('username', $params['username'])->exists()) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'User Not Found'
            ],404);
        }

        return response()->json([
            "status"=>"invalid",
            "message"=> "Wrong username or password"
        ],401);


    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            "status"=> "success"
        ],200);
    }

}
