<?php

namespace App\Exports;

use App\Models\Visitor;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitorsExport implements
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
        $query = Visitor::with(['event', 'recordedBy']);

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        if ($this->from) {
            $query->whereDate('visited_at', '>=', $this->from);
        }

        if ($this->to) {
            $query->whereDate('visited_at', '<=', $this->to);
        }

        return $query->latest('visited_at');
    }

    public function headings(): array
    {
        return [
            '#', 'Full Name', 'Phone', 'Email',
            'Event', 'Event Date', 'Visited At', 'Recorded By', 'Notes',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row->full_name,
            $row->phone ?? '—',
            $row->email ?? '—',
            $row->event->title ?? '—',
            $row->event->event_date->format('d M Y') ?? '—',
            $row->visited_at->format('d M Y h:i A'),
            $row->recordedBy->name ?? 'System',
            $row->notes ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFEA580C']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Visitors';
    }
}
