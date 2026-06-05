<?php

namespace App\Exports;

use App\Models\ExpenseRecord;
use App\Models\IncomeRecord;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FinanceExport implements WithMultipleSheets
{
    public function __construct(
        protected string $from,
        protected string $to
    ) {}

    public function sheets(): array
    {
        return [
            new FinanceIncomeSheet($this->from, $this->to),
            new FinanceExpenseSheet($this->from, $this->to),
        ];
    }
}
