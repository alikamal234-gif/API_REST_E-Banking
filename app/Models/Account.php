<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'number',
        'type',
        'balance',
        'overdraft_limit', // lmablagh li t9der thbet mno te7t 0
        'interest_rate', // ch7al lfa2ida sanawiya
        'status',
        'blocked_reason',
        'monthly_withdrawals' // ch7al mn mra jbedti floss fchher
    ];

    protected $casts = [
        'balance' => 'float',
        'overdraft_limit' => 'float',
        'interest_rate' => 'float'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('accept_closure')
            ->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(Transfer::class, 'source_account_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(Transfer::class, 'destination_account_id');
    }

    public function contact()
    {
        return $this->hasOne(Contact::class);
    }
}
