<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json([
            'totalElements' => $users->count(),
            'content' => $users->map(function ($user) {
                return [
                    'username' => $user->username,
                    'last_login_at' => $user->last_login_at,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s')
                ];
            })
        ],200);
    }

    public function store(Request $request)
    {
        $params =  $request->validate([
            'username' => ['required', 'min:4', 'max:60'],
            'password' => ['required', 'min:5' , 'max:20']
        ]);

        $user = User::create([
            'username' => $params['username'],
            'password' => $params['password']
        ]);

        if(User::where('username', $params['username'])->exists()){
            return response()->json([
                "status"=> "invalid",
                "message"=>"Username already exists"
            ],400);
        }

        $token = $user->createToken('token', ['role:user'])->plainTextToken;

        return response()->json([
            "status"=> "success",
            'username'  => $user->username
        ],201);

    }

    public function update(Request $request, $id)
    {
        $params = $request->validate([
            'username' => [ 'min:4', 'max:60'],
            'password' => ['min:5', 'max:20']
        ]);

        $user = User::where('id' , $id)->first();

        if(!$user) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'User Not Found'
            ],404);
        }

        if(User::where('username', $request->username)->whereNot('username',$request->username)->exists()){
            return response()->json([
                "status"=> "invalid",
                "message"=>"Username already exists"
            ],400);
        }

        $user->update($params);

        return response()->json([
            "status"=> "success",
            "username"=>$user->username
        ],201);


    }

    public function delete($id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json([
                "status"=> "not-found",
                "message"=> "User Not found"
            ],403);
        }

        $user->delete();

        return response('',204);
    }
}
