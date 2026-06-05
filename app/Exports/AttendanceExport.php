<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements
    FromQuery, WithHeadings, WithMapping,
    WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected ?int $eventId = null,
        protected ?string $from = null,
        protected ?string $to = null,
    ) {}

    public function query()
    {
        $query = Attendance::with(['member', 'event']);

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        if ($this->from) {
            $query->whereDate('checked_in_at', '>=', $this->from);
        }

        if ($this->to) {
            $query->whereDate('checked_in_at', '<=', $this->to);
        }

        return $query->latest('checked_in_at');
    }

    public function headings(): array
    {
        return [
            '#',
            'Member Name',
            'Member ID',
            'TACMS No.',
            'Event',
            'Event Type',
            'Event Date',
            'Check-in Time',
            'Check-in Method',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row->member->full_name ?? '—',
            $row->member->member_id_card ?? '—',
            $row->member->tacms_number ?? '—',
            $row->event->title ?? '—',
            ucfirst($row->event->type ?? '—'),
            $row->event->event_date->format('d M Y') ?? '—',
            $row->checked_in_at->format('h:i A'),
            ucwords(str_replace('_', ' ', $row->checkin_method)),
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
        return 'Attendance';
    }
}
