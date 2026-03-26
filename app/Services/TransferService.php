<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transfer;
use App\Models\Transaction;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Exception;

class TransferService
{
    public function transfer(Account $source, Account $destination, float $amount, int $userId)
    {
        if (!$source->users()->where('user_id', $userId)->exists()) {
            throw new Exception('Unauthorized');
        }

        $this->checkAccountStatus($source, $destination);

        $this->checkAccountType($source, $amount, $userId);

        $this->checkWithdrawalLimit($source);

        $this->checkDailyLimit($userId, $amount);

        return DB::transaction(function () use ($source, $destination, $amount, $userId) {

            $transfer = Transfer::create([
                'source_account_id' => $source->id,
                'destination_account_id' => $destination->id,
                'creator_id' => $userId,
                'amount' => $amount,
                'status' => 'pending',
            ]);

            try {

                $source->balance -= $amount;
                $source->save();

                $destination->balance += $amount;
                $destination->save();

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

                $transfer->update(['status' => 'completed']);

            } catch (\Exception $e) {

                $transfer->update(['status' => 'failed']);
                throw $e;
            }

            return $transfer;
        });
    }

    public function checkAccountStatus(Account $source, Account $destination)
    {
        if ($source->status !== 'active') {
            throw new Exception('Source account is not active');
        }

        if ($destination->status !== 'active') {
            throw new Exception('Destination account is not active');
        }
    }

    public function checkAccountType(Account $source, float $amount, int $userId)
    {
        if ($source->type === 'courant') {
            if (($source->balance + $source->overdraft_limit) < $amount) {
                throw new Exception('Insufficient funds (overdraft)');
            }
        } elseif ($source->type === 'epargne') {
            if ($source->balance < $amount) {
                throw new Exception('No overdraft allowed for epargne');
            }
        } elseif ($source->type === 'mineur') {

            $isGuardian = Contact::where('account_id', $source->id)
                ->where('guardian_id', $userId)
                ->exists();

            if (!$isGuardian) {
                throw new Exception('Only guardian can transfer');
            }

            if ($source->balance < $amount) {
                throw new Exception('Insufficient funds');
            }
        }
    }

    public function checkWithdrawalLimit(Account $source)
    {
        if (!in_array($source->type, ['epargne', 'mineur'])) {
            return;
        }

        $count = Transaction::where('account_id', $source->id)
            ->where('type', 'WITHDRAWAL')
            ->whereMonth('created_at', now()->month)
            ->count();

        if ($source->type === 'epargne' && $count >= 3) {
            throw new Exception('Withdrawal limit reached (3/month)');
        }

        if ($source->type === 'mineur' && $count >= 2) {
            throw new Exception('Withdrawal limit reached (2/month)');
        }
    }

    public function checkDailyLimit(int $userId, float $amount)
    {
        $todayTotal = Transfer::where('creator_id', $userId)
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        if (($todayTotal + $amount) > 10000) {
            throw new Exception('Daily transfer limit exceeded (10,000 MAD)');
        }
    }
}
