<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transfer;
use App\Services\TransferService;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    protected $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'source_id' => 'required|exists:accounts,id',
            'destination_id' => 'required|exists:accounts,id|different:source_id',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $transfer = $this->transferService->transfer(
                Account::findOrFail($request->source_id),
                Account::findOrFail($request->destination_id),
                $request->amount,
                auth()->id()
            );

            return response()->json([
                'status' => 'success',
                'transfer' => $transfer,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
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
