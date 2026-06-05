<?php

namespace App\Exports;

use App\Models\IncomeRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanceIncomeSheet implements
    FromCollection, WithHeadings, WithMapping,
    WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected string $from,
        protected string $to
    ) {}

    public function collection()
    {
        return IncomeRecord::with('member')
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$this->from, $this->to])
            ->latest('payment_date')->get();
    }

    public function headings(): array
    {
        return [
            '#', 'Date', 'Member', 'Member ID', 'TACMS No', 'Category',
            'Amount', 'Currency', 'Amount (GHS)', 'Exchange Rate',
            'Payment Method', 'Reference', 'Notes', 'Recorded By',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $row->payment_date->format('d M Y'),
            $row->member?->full_name ?? 'Anonymous',
            $row->member?->member_id_card ?? '—',
            $row->member?->tacms_number ?? '—',
            ucfirst($row->category),
            number_format($row->amount, 2),
            $row->currency,
            number_format($row->amount_ghs, 2),
            $row->exchange_rate,
            ucwords(str_replace('_', ' ', $row->payment_method)),
            $row->reference ?? '—',
            $row->notes ?? '—',
            $row->recordedBy?->name ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF16A34A']],
            ],
        ];
    }

    public function title(): string { return 'Income'; }
}
