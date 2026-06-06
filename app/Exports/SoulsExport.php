<?php

namespace App\Exports;

use App\Models\NewSoul;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SoulsExport implements
    FromCollection, WithHeadings, WithMapping,
    WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected string $from,
        protected string $to
    ) {}

    public function collection()
    {
        return NewSoul::with(['wonBy', 'assignedTo', 'followups'])
            ->whereBetween('date_won', [$this->from, $this->to])
            ->latest('date_won')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#', 'First Name', 'Last Name', 'Phone', 'Email',
            'Area', 'Address', 'Date Won', 'Won By',
            'Status', 'Church Background', 'Assigned To',
            'Follow-ups', 'Notes', 'Recorded On',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        $statusLabels = [
            'new'         => 'New',
            'contacted'   => 'Contacted',
            'attending'   => 'Attending',
            'baptised'    => 'Baptised',
            'converted'   => 'Converted',
            'backslidden' => 'Backslidden',
        ];

        return [
            $i,
            $row->first_name,
            $row->last_name,
            $row->phone         ?? '—',
            $row->email         ?? '—',
            $row->area          ?? '—',
            $row->address       ?? '—',
            $row->date_won->format('d M Y'),
            $row->wonBy?->full_name ?? '—',
            $statusLabels[$row->status] ?? ucfirst($row->status),
            $row->church_background ?? '—',
            $row->assignedTo?->name ?? '—',
            $row->followups->count(),
            $row->notes         ?? '—',
            $row->created_at->format('d M Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF2563EB']],
            ],
        ];
    }

    public function title(): string { return 'New Souls Report'; }
}
