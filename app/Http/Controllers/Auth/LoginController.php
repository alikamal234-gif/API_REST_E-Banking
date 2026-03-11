<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8']
        ]);
        if ($token = Auth::attempt($data)) {
          
            return response()->json(
                [
                    'message' => "login wax successfull",
                    'token' => $token
                ],
                201

            );
        }
        return response()->json([
            'error' => 'authentification failed'
        ], 401);
    }

    public function logout(Request $request)
    {
        auth()->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }


}
