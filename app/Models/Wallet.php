<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionsAsSender()
    {
        return $this->hasMany(Transaction::class, 'sender_wallet_id');
    }

    public function transactionsAsReceiver()
    {
        return $this->hasMany(Transaction::class, 'receiver_wallet_id');
    }
}
