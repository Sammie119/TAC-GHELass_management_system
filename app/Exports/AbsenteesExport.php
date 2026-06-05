<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsenteesExport implements
    FromCollection, WithHeadings, WithMapping,
    WithStyles, ShouldAutoSize, WithTitle, WithProperties
{
    public function __construct(
        protected Collection $absentees,
        protected Event $event,
    ) {}

    public function collection(): Collection
    {
        return $this->absentees;
    }

    public function properties(): array
    {
        return [
            'title'   => 'Absentees Report — ' . $this->event->title,
            'creator' => config('app.name', 'Church Management System'),
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Member ID',
            'Full Name',
            'Phone',
            'Email',
            'Gender',
            'Total Attendance (All Time)',
            'Last Seen',
        ];
    }

    public function map($member): array
    {
        static $i = 0;
        $i++;

        $lastAttendance = $member->attendance()->latest('checked_in_at')->first();

        return [
            $i,
            $member->member_id_card,
            $member->full_name,
            $member->phone  ?? '—',
            $member->email  ?? '—',
            ucfirst($member->gender ?? '—'),
            $member->attendance()->count(),
            $lastAttendance
                ? $lastAttendance->checked_in_at->format('d M Y')
                : 'Never attended',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFDC2626']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Absentees';
    }
}
