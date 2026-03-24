<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Contact;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $accounts = $user->accounts()->with('users')->get();

        return response()->json([
            'data' => $accounts,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:courant,epargne,mineur'],
        ]);

        $account = Account::create([
            'number' => 'ALI-'.strtoupper(uniqid()),
            'type' => $request->type,
            'balance' => 0,
            'status' => 'active',
        ]);

        $account->users()->attach(auth()->id());

        return response()->json([
            'data' => $account,
        ], 201);
    }


    public function show(string $id)
    {
        $account = Account::with('users')->findOrFail($id);

        return response()->json(['data' => $account], 200);
    }

    public function addCoOwner(Request $request, string $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $account = Account::findOrFail($id);

        $account->users()->attach($request->user_id);

        return response()->json(['message' => 'User added as co-owner']);
    }

    public function removeCoOwner(Request $request, string $id)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $account = Account::with('users')->findOrFail($id);

        if (!$account->users->contains(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($data['user_id'] == auth()->id()) {
            return response()->json(['error' => 'You cannot remove yourself'], 400);
        }

        if (! $account->users->contains($data['user_id'])) {
            return response()->json(['error' => 'User not in this account'], 400);
        }

        $account->users()->detach($data['user_id']);

        return response()->json(['message' => 'User removed']);
    }

    public function assignGuardian(Request $request, string $id)
    {
        $request->validate([
            'guardian_id' => 'required|exists:users,id',
        ]);

        $account = Account::findOrFail($id);

        if ($account->type !== 'mineur') {
            return response()->json(['error' => 'Not a minor account'], 400);
        }

        Contact::create([
            'account_id' => $account->id,
            'user_id' => auth()->id(),
            'guardian_id' => $request->guardian_id,
        ]);

        return response()->json(['message' => 'Guardian assigned']);
    }

    public function convertToCourant(string $id)
    {
        $account = Account::findOrFail($id);

        if ($account->type !== 'mineur') {
            return response()->json(['error' => 'Not a minor account'], 400);
        }

        $account->type = 'courant';
        $account->save();

        return response()->json(['message' => 'Converted to courant']);
    }

    public function requestClosure(string $id)
    {
        $account = Account::findOrFail($id);
        $account->users()->updateExistingPivot(auth()->id(), [
            'accept_closure' => true,
        ]);
        return response()->json(['message' => 'Closure request sent']);
    }
}
