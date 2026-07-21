<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'is_active', 'created_by'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function expenseRecords()
    {
        return $this->hasMany(ExpenseRecord::class);
    }

    public function pettyCashTransactions()
    {
        return $this->hasMany(PettyCashTransaction::class);
    }

    public function financialRequests()
    {
        return $this->hasMany(FinancialRequest::class);
    }
}
