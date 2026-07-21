<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'requested_by', 'category', 'description', 'payee',
        'amount', 'currency', 'amount_ghs', 'exchange_rate', 'payment_method', 'bank_account_id', 'budget_line_id',
        'request_date', 'status',
        'pastor_approved_at', 'pastor_approved_by',
        'super_admin_approved_at', 'super_admin_approved_by',
        'rejected_at', 'rejected_by', 'rejection_reason',
        'pv_number', 'pv_generated_at', 'pv_generated_by',
        'expense_record_id', 'notes',
    ];

    protected $casts = [
        'request_date' => 'date',
        'amount' => 'decimal:2',
        'amount_ghs' => 'decimal:2',
        'pastor_approved_at' => 'datetime',
        'super_admin_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'pv_generated_at' => 'datetime',
    ];

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function pastorApprovedBy()
    {
        return $this->belongsTo(User::class, 'pastor_approved_by');
    }

    public function superAdminApprovedBy()
    {
        return $this->belongsTo(User::class, 'super_admin_approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function pvGeneratedBy()
    {
        return $this->belongsTo(User::class, 'pv_generated_by');
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
