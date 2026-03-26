<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function block(Request $request, $id)
    {


        $request->validate([
            'reason' => 'required|string'
        ]);

        $account = Account::findOrFail($id);

        $account->update([
            'status' => 'blocked',
            'blocked_reason' => $request->reason
        ]);

        return response()->json([
            'status' => 'blocked'
        ]);
    }

    public function unblock($id)
    {


        $account = Account::findOrFail($id);

        $account->update([
            'status' => 'active',
            'blocked_reason' => null
        ]);

        return response()->json([
            'status' => 'active'
        ]);
    }

    public function close($id)
    {


        $account = Account::findOrFail($id);

        if ($account->balance != 0) {
            return response()->json([
                'error' => 'Balance must be 0'
            ], 400);
        }

        $account->update([
            'status' => 'closed'
        ]);

        return response()->json([
            'status' => 'closed'
        ]);
    }
}
