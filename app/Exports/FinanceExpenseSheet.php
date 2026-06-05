<?php

namespace App\Exports;

use App\Models\ExpenseRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanceExpenseSheet implements
    FromCollection, WithHeadings, WithMapping,
    WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected string $from,
        protected string $to
    ) {}

    public function collection()
    {
        return ExpenseRecord::with('recordedBy')
            ->where('status', 'approved')
            ->whereBetween('expense_date', [$this->from, $this->to])
            ->latest('expense_date')->get();
    }

    public function headings(): array
    {
        return [
            '#', 'Date', 'Category', 'Description', 'Payee',
            'Amount', 'Currency', 'Amount (GHS)', 'Exchange Rate',
            'Payment Method', 'Receipt No.', 'Notes', 'Recorded By',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $row->expense_date->format('d M Y'),
            ucfirst($row->category),
            $row->description,
            $row->payee ?? '—',
            number_format($row->amount, 2),
            $row->currency,
            number_format($row->amount_ghs, 2),
            $row->exchange_rate,
            ucwords(str_replace('_', ' ', $row->payment_method)),
            $row->receipt_number ?? '—',
            $row->notes ?? '—',
            $row->recordedBy?->name ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFDC2626']],
            ],
        ];
    }

    public function title(): string { return 'Expenses'; }
}
