<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function me()
    {
        return response()->json([
            'user' => auth()->user()
        ]);
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'min:8'],
            'new_password' => ['required', 'min:8'],
            'confirme_new_password' => ['required', 'min:8']
        ]);

        if ($data['new_password'] !== $data['confirme_new_password']) {
            return response()->json([
                'message' => "les password est different"
            ], 400);
        }
        $user = User::findOrFail(auth()->id());
        if (!Hash::check($data['new_password'], $user->password)) {
            $user->update([
                'password' => $data['new_password']
            ]);

            return response()->json([
                'message' => 'la modufication est successfull'
            ], 201);
        }

        return response()->json([
            'message' => "password not true"
        ], 400);
    }


    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'min:0'],
            'email' => ['required', 'email']
        ]);

        $user = auth('api')->user();
        if ($user) {
            $user->update($data);
            return response()->json([
                'data' => $user,
                'message' => "les modufication est success"
            ], 201);
        }
        return response()->json([
            'message' => "les modufication not success"
        ], 400);

    }

    public function deleteCompte()
    {
        $user = auth('api')->user();
        if ($user) {
            $user->delete();
            return response()->json([
                'data' => $user,
                'message' => "le compte est supprime"
            ], 200);
        }
        return response()->json([
            'message' => "un probleme dans la supprision"
        ], 400);


    }
}
