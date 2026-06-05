<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomeTemplateExport implements
    FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected array $headers,
        protected array $rows
    ) {}

    public function array(): array { return $this->rows; }

    public function headings(): array { return $this->headers; }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF16A34A']],
            ],
        ];
    }

    public function title(): string { return 'Income Bulk Template'; }
}
