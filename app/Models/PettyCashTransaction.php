<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PettyCashTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type', 'amount', 'currency', 'amount_ghs', 'exchange_rate',
        'category', 'description', 'payee', 'custodian_id', 'bank_account_id', 'budget_line_id',
        'receipt_number', 'transaction_date', 'notes',
        'recorded_by', 'expense_record_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'amount_ghs' => 'decimal:2',
    ];

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function custodian()
    {
        return $this->belongsTo(User::class, 'custodian_id');
    }

    public function expenseRecord()
    {
        return $this->belongsTo(ExpenseRecord::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function budgetLine()
    {
        return $this->belongsTo(BudgetLine::class);
    }
}
