<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('creator_id', auth()->id())
            ->latest()
            ->get();

        return response()->json($transactions);
    }


    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->creator_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->creator_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $transaction->delete();

        return response()->json([
            'status' => 'deleted'
        ]);
    }
}
