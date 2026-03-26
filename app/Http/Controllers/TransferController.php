<?php

namespace App\Http\Controllers;

use App\Models\Account;
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

            $source = Account::findOrFail($request->source_id);
            $destination = Account::findOrFail($request->destination_id);

            $transfer = $this->transferService->transfer(
                $source,
                $destination,
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
}
