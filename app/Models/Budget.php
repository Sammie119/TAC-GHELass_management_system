<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_year', 'budget_line_id', 'month', 'amount', 'notes', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function budgetLine()
    {
        return $this->belongsTo(BudgetLine::class);
    }
}
