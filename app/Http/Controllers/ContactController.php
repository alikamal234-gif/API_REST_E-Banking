<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::where('guardian_id', auth()->id())
            ->orWhere('user_id', auth()->id())
            ->get();

        return response()->json($contacts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'user_id' => 'required|exists:users,id', // mineur
            'guardian_id' => 'required|exists:users,id'
        ]);

        $contact = Contact::create([
            'account_id' => $request->account_id,
            'user_id' => $request->user_id,
            'guardian_id' => $request->guardian_id,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $contact
        ],201);
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);

        if (
            $contact->guardian_id !== auth()->id() &&
            $contact->user_id !== auth()->id()
        ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($contact);
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);

        if ($contact->guardian_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $contact->delete();

        return response()->json([
            'status' => 'deleted'
        ]);
    }
}
