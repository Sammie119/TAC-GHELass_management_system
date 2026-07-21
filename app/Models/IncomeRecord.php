<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'income_records';

    protected $fillable = [
        'member_id', 'category', 'amount', 'currency',
        'amount_ghs', 'exchange_rate', 'payment_date',
        'payment_method', 'bank_account_id', 'reference', 'event_id',
        'notes', 'status', 'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'amount_ghs' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class)->withDefault([
            'first_name' => 'Guest',
            'last_name' => '',
            'member_id_card' => 'GUEST',
        ]);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
