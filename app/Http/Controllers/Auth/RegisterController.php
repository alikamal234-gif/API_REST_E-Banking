<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
   

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','min:0'],
            'email' => ['required','email'],
            'password' => ['required','min:8']
        ]);

        $user = User::create($data);

        return response()->json([
            [
                'status' => 'seccuss',
                'data' => $user
            ]
        ],
            201);
    }

   
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
