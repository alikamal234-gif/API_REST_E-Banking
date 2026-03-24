<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'source_account_id',
        'destination_account_id',
        'creator_id',
        'status',
        'amount'
    ];

    protected $casts = [
        'amount' => 'float'
    ];

    public function source()
    {
        return $this->belongsTo(Account::class, 'source_account_id');
    }

    public function destination()
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}