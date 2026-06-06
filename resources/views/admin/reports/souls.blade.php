@extends('layouts.admin')
@section('page-title', 'New Souls Report')
@section('content')

    @php
        $statusConfig = [
            'new'         => ['label' => 'New',         'color' => '#2563eb', 'bg' => '#dbeafe', 'emoji' => '✨'],
            'contacted'   => ['label' => 'Contacted',   'color' => '#d97706', 'bg' => '#fef3c7', 'emoji' => '📞'],
            'attending'   => ['label' => 'Attending',   'color' => '#7c3aed', 'bg' => '#ede9fe', 'emoji' => '⛪'],
            'baptised'    => ['label' => 'Baptised',    'color' => '#0891b2', 'bg' => '#cffafe', 'emoji' => '💧'],
            'converted'   => ['label' => 'Converted',   'color' => '#16a34a', 'bg' => '#dcfce7', 'emoji' => '🙌'],
            'backslidden' => ['label' => 'Backslidden', 'color' => '#dc2626', 'bg' => '#fee2e2', 'emoji' => '🙏'],
        ];
    @endphp

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">New Souls Report</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">
                {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
            </p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.reports.souls.pdf') }}?from={{ $from }}&to={{ $to }}"
               style="background:#dc2626;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;display:flex;align-items:center;gap:6px;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export PDF
            </a>
            <a href="{{ route('admin.reports.souls.excel') }}?from={{ $from }}&to={{ $to }}"
               style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;display:flex;align-items:center;gap:6px;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- Date range filter --}}
    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;margin-bottom:1.5rem;flex-wrap:wrap;">
        <div>
            <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">From</label>
            <input type="date" name="from" value="{{ $from }}"
                   style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">To</label>
            <input type="date" name="to" value="{{ $to }}"
                   style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;">
        </div>

        {{-- Quick ranges --}}
        @php
            $ranges = [
                'This month'   => [now()->startOfMonth()->toDateString(), now()->toDateString()],
                'Last month'   => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                'This quarter' => [now()->startOfQuarter()->toDateString(), now()->toDateString()],
                'This year'    => [now()->startOfYear()->toDateString(), now()->toDateString()],
                'All time'     => ['2020-01-01', now()->toDateString()],
            ];
        @endphp
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
            @foreach($ranges as $label => [$f, $t])
                <a href="{{ route('admin.reports.souls') }}?from={{ $f }}&to={{ $t }}"
                   style="font-size:12px;padding:8px 12px;border-radius:8px;text-decoration:none;
                  background:{{ $from === $f && $to === $t ? '#2563eb' : '#f3f4f6' }};
                  color:{{ $from === $f && $to === $t ? 'white' : '#374151' }};
                  border:1px solid {{ $from === $f && $to === $t ? '#2563eb' : '#d1d5db' }};">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <button type="submit"
                style="background:#2563eb;color:white;padding:9px 18px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
            Apply
        </button>
    </form>

    {{-- Summary stats --}}
    <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:14px;margin-bottom:1.5rem;">
        <style>@media(max-width:768px){.souls-summary{grid-template-columns:repeat(2,1fr) !important;}}</style>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;" class="souls-summary">

            <div style="background:linear-gradient(135deg,#2563eb,#4f46e5);border-radius:14px;padding:18px;color:white;text-align:center;">
                <p style="font-size:36px;font-weight:800;line-height:1;">{{ $stats['total'] }}</p>
                <p style="font-size:13px;opacity:0.85;margin-top:6px;">Total souls</p>
            </div>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:18px;text-align:center;">
                <p style="font-size:36px;font-weight:800;color:#16a34a;line-height:1;">{{ $stats['converted'] }}</p>
                <p style="font-size:13px;color:#6b7280;margin-top:6px;">🙌 Converted</p>
            </div>
            <div style="background:#ede9fe;border:1px solid #ddd6fe;border-radius:14px;padding:18px;text-align:center;">
                <p style="font-size:36px;font-weight:800;color:#7c3aed;line-height:1;">{{ $stats['attending'] }}</p>
                <p style="font-size:13px;color:#6b7280;margin-top:6px;">⛪ Attending</p>
            </div>
            <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:14px;padding:18px;text-align:center;">
                <p style="font-size:36px;font-weight:800;color:#d97706;line-height:1;">{{ $stats['followups'] }}</p>
                <p style="font-size:13px;color:#6b7280;margin-top:6px;">📞 Follow-ups</p>
            </div>

        </div>
    </div>

    {{-- Status breakdown --}}
    <div style="display:grid;gap:20px;margin-bottom:1.5rem;">
        <style>@media(min-width:768px){.souls-breakdown{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:20px;" class="souls-breakdown">

            {{-- By status --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;background:#f9fafb;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Breakdown by status</h3>
                </div>
                @foreach($statusConfig as $key => $cfg)
                    <div style="padding:12px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f9fafb;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span style="font-size:18px;">{{ $cfg['emoji'] }}</span>
                            <span style="font-size:13px;font-weight:500;color:#374151;">{{ $cfg['label'] }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:80px;height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden;">
                                <div style="height:100%;background:{{ $cfg['color'] }};border-radius:3px;
                                width:{{ $stats['total'] > 0 ? round(($stats[$key]/$stats['total'])*100) : 0 }}%;"></div>
                            </div>
                            <span style="font-size:14px;font-weight:700;color:{{ $cfg['color'] }};min-width:24px;text-align:right;">
                    {{ $stats[$key] }}
                </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- By winner + by area --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Top soul winners --}}
                @if(count($byWinner) > 0)
                    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                        <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;background:#f9fafb;">
                            <h3 style="font-size:14px;font-weight:600;color:#111827;">Top soul winners</h3>
                        </div>
                        @foreach($byWinner->take(5) as $i => $winner)
                            <div style="padding:11px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f9fafb;">
                                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="width:22px;height:22px;border-radius:50%;background:{{ $i === 0 ? '#fef3c7' : '#f3f4f6' }};
                                 color:{{ $i === 0 ? '#d97706' : '#6b7280' }};
                                 display:flex;align-items:center;justify-content:center;
                                 font-size:11px;font-weight:700;flex-shrink:0;">
                        {{ $i + 1 }}
                    </span>
                                    <span style="font-size:13px;font-weight:500;color:#111827;">{{ $winner['name'] }}</span>
                                </div>
                                <span style="font-size:15px;font-weight:800;color:#2563eb;">{{ $winner['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- By area --}}
                @if(count($byArea) > 0)
                    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                        <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;background:#f9fafb;">
                            <h3 style="font-size:14px;font-weight:600;color:#111827;">By area / location</h3>
                        </div>
                        @foreach($byArea->take(5) as $area)
                            <div style="padding:11px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f9fafb;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span style="font-size:13px;color:#374151;">{{ $area['area'] }}</span>
                                </div>
                                <span style="font-size:15px;font-weight:800;color:#7c3aed;">{{ $area['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Full souls table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:14px;font-weight:600;color:#111827;">All souls ({{ count($souls) }})</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#f9fafb;">
                <tr>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">#</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Name</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Phone</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Area</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Date Won</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Won By</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Status</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Follow-ups</th>
                </tr>
                </thead>
                <tbody>
                @forelse($souls as $i => $soul)
                    @php $cfg = $statusConfig[$soul->status]; @endphp
                    <tr style="border-top:1px solid #f3f4f6;"
                        onmouseenter="this.style.background='#f9fafb'"
                        onmouseleave="this.style.background=''">
                        <td style="padding:10px 16px;color:#9ca3af;">{{ $i + 1 }}</td>
                        <td style="padding:10px 16px;">
                            <p style="font-weight:500;color:#111827;">{{ $soul->full_name }}</p>
                            <p style="font-size:11px;color:#9ca3af;">{{ $soul->email ?? '' }}</p>
                        </td>
                        <td style="padding:10px 16px;color:#374151;">{{ $soul->phone ?? '—' }}</td>
                        <td style="padding:10px 16px;color:#374151;">{{ $soul->area ?? '—' }}</td>
                        <td style="padding:10px 16px;color:#6b7280;">{{ $soul->date_won->format('d M Y') }}</td>
                        <td style="padding:10px 16px;color:#374151;">{{ $soul->wonBy?->full_name ?? '—' }}</td>
                        <td style="padding:10px 16px;">
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;
                                 background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};">
                        {{ $cfg['emoji'] }} {{ $cfg['label'] }}
                    </span>
                        </td>
                        <td style="padding:10px 16px;text-align:center;font-weight:600;color:#2563eb;">
                            {{ $soul->followups->count() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:40px;text-align:center;color:#9ca3af;">
                            No souls found in this date range.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
