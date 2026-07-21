<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PledgePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pledge_id', 'amount', 'payment_date',
        'payment_method', 'bank_account_id', 'reference', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function pledge()
    {
        return $this->belongsTo(Pledge::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
