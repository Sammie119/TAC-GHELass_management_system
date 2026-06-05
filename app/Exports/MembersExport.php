<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersExport implements
    FromQuery, WithHeadings, WithMapping,
    WithStyles, ShouldAutoSize, WithTitle
{
    public function query()
    {
        return Member::withCount('attendance')->where('status', 'active');
    }

    public function headings(): array
    {
        return [
            '#', 'Member ID', 'TACMS Number', 'Full Name', 'Phone',
            'Email', 'Gender', 'Department', 'Date of Birth',
            'Address', 'Total Attendance', 'Joined',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row->member_id_card,
            $row->tacms_number ?? '—',
            $row->full_name,
            $row->phone ?? '—',
            $row->email ?? '—',
            ucfirst($row->gender ?? '—'),
            $row->department ?? '—',
            $row->date_of_birth
                ? \Carbon\Carbon::parse($row->date_of_birth)->format('d M Y')
                : '—',
            $row->address ?? '—',
            $row->attendance_count,
            $row->created_at->format('d M Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF2563EB']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Members';
    }
}
