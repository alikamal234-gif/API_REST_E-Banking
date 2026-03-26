<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transfer;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class TransferService
{
    public function transfer(Account $source, Account $destination, float $amount, int $userId)
    {
        if (!$source->users()->where('user_id', $userId)->exists()) {
            throw new Exception('Unauthorized');
        }

        if ($source->status !== 'active' || $destination->status !== 'active') {
            throw new Exception('Account not active');
        }

        if ($source->balance < $amount) {
            throw new Exception('Insufficient funds');
        }

        return DB::transaction(function () use ($source, $destination, $amount, $userId) {

            $source->balance -= $amount;
            $source->save();

            $destination->balance += $amount;
            $destination->save();

            $transfer = Transfer::create([
                'source_account_id' => $source->id,
                'destination_account_id' => $destination->id,
                'creator_id' => $userId,
                'amount' => $amount,
                'status' => 'completed',
            ]);

            Transaction::create([
                'account_id' => $source->id,
                'creator_id' => $userId,
                'type' => 'WITHDRAWAL',
                'amount' => $amount,
            ]);

            Transaction::create([
                'account_id' => $destination->id,
                'creator_id' => $userId,
                'type' => 'DEPOSIT',
                'amount' => $amount,
            ]);

            return $transfer;
        });
    }
}
