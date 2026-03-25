<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function transfer(Request $request)
    {
        $request->validate([
            'source_id' => 'required|exists:accounts,id',
            'destination_id' => 'required|exists:accounts,id|different:source_id',
            'amount' => 'required|numeric|min:1',
        ]);

        $userId = auth()->id();

        $source = Account::findOrFail($request->source_id);
        $destination = Account::findOrFail($request->destination_id);

        if (!$source->users()->where('user_id', $userId)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($source->status !== 'active' || $destination->status !== 'active') {
            return response()->json(['error' => 'Account not active'], 400);
        }

        if (($source->balance + $source->overdraft_limit) < $request->amount) {
            return response()->json(['error' => 'Insufficient funds'], 400);
        }

        $transfer = DB::transaction(function () use ($source, $destination, $request, $userId) {

            $source->balance -= $request->amount;
            $source->save();

            $destination->balance += $request->amount;
            $destination->save();

            $transfer = Transfer::create([
                'source_account_id' => $source->id,
                'destination_account_id' => $destination->id,
                'creator_id' => $userId,
                'amount' => $request->amount,
                'status' => 'completed',
            ]);

            Transaction::create([
                'account_id' => $source->id,
                'creator_id' => $userId,
                'type' => 'WITHDRAWAL',
                'amount' => $request->amount,
            ]);

            Transaction::create([
                'account_id' => $destination->id,
                'creator_id' => $userId,
                'type' => 'DEPOSIT',
                'amount' => $request->amount,
            ]);

            return $transfer;
        });

        return response()->json([
            'status' => 'success',
            'transfer' => $transfer,
        ]);
    }

    public function history()
    {
        $userId = auth()->id();

        $transfers = Transfer::where('creator_id', $userId)
            ->orWhere('source_account_id', $userId)
            ->orWhere('destination_account_id', $userId)
            ->get();

        return response()->json($transfers);
    }

    public function show($id)
    {
        $transfer = Transfer::findOrFail($id);

        if ($transfer->creator_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($transfer);
    }
}
