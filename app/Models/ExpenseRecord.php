<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expense_records';

    protected $fillable = [
        'category', 'description', 'amount', 'currency',
        'amount_ghs', 'exchange_rate', 'expense_date',
        'payment_method', 'payee', 'receipt_number',
        'attachment', 'notes', 'status', 'recorded_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
        'amount_ghs'   => 'decimal:2',
    ];

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
