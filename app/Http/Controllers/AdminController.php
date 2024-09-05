<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Administrator::all();

        return response()->json([
            'totalElements' => $admins->count(),
            'content' => $admins->map(function ($admin) {
                return [
                  'username' => $admin->username,
                  'last_login_at' => $admin->last_login_at,
                    'created_at' => $admin->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $admin->updated_at->format('Y-m-d H:i:s')
                ];
            })
        ],200);
    }
}
