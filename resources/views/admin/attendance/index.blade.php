@extends('layouts.admin')
@section('page-title', 'Attendance')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Attendance</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Headcount summary and manual entry for missed check-ins</p>
        </div>

        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <button type="button" onclick="openAddHeadcountForm()"
                    style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
                + Add headcount
            </button>
            <a href="{{ route('admin.attendance.history') }}"
               style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
                History
            </a>
            <form method="GET" style="display:flex;gap:8px;align-items:center;">
                <label style="font-size:13px;color:#374151;">Date</label>
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                       style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            </form>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:14px;margin-bottom:1.5rem;">
        <style>@media(min-width:768px){.attendance-stats{grid-template-columns:repeat(2,1fr) !important;}}</style>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" class="attendance-stats">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#16a34a;">{{ number_format($dayTotals['digital']) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Digital check-ins</p>
            </div>
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#d97706;">{{ number_format($dayTotals['manual']) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Manual headcount</p>
            </div>
        </div>
    </div>

    {{-- Category breakdown --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;margin-bottom:1.5rem;">
        <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Manual headcount by category</h3>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <style>@media(min-width:768px){.category-stats{grid-template-columns:repeat(5,1fr) !important;}}</style>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;grid-column:1 / -1;" class="category-stats">
                <div style="background:#f9fafb;border-radius:10px;padding:12px;text-align:center;">
                    <p style="font-size:16px;font-weight:700;color:#111827;">{{ number_format($categoryTotals['male']) }}</p>
                    <p style="font-size:11px;color:#9ca3af;margin-top:2px;">Male</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:12px;text-align:center;">
                    <p style="font-size:16px;font-weight:700;color:#111827;">{{ number_format($categoryTotals['female']) }}</p>
                    <p style="font-size:11px;color:#9ca3af;margin-top:2px;">Female</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:12px;text-align:center;">
                    <p style="font-size:16px;font-weight:700;color:#111827;">{{ number_format($categoryTotals['children']) }}</p>
                    <p style="font-size:11px;color:#9ca3af;margin-top:2px;">Children</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:12px;text-align:center;">
                    <p style="font-size:16px;font-weight:700;color:#111827;">{{ number_format($categoryTotals['youth']) }}</p>
                    <p style="font-size:11px;color:#9ca3af;margin-top:2px;">Youth</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:12px;text-align:center;">
                    <p style="font-size:16px;font-weight:700;color:#111827;">{{ number_format($categoryTotals['visitors']) }}</p>
                    <p style="font-size:11px;color:#9ca3af;margin-top:2px;">Visitor</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Headcount entry panel --}}
    <div id="headcount-form-panel" style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 id="headcount-form-title" style="font-size:15px;font-weight:600;color:#111827;margin-bottom:20px;">Add headcount</h3>

        <form id="headcount-form" method="POST" action="{{ route('admin.attendance.headcount.store') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Event *</label>
                <select name="event_id" id="hc-event-id" required
                        style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                    <option value="">— Select event —</option>
                    @foreach($allEvents as $ev)
                        <option value="{{ $ev->id }}">{{ $ev->title }} ({{ $ev->event_date->format('d M Y') }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Male</label>
                    <input type="number" name="male" id="hc-male" min="0" value="0"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Female</label>
                    <input type="number" name="female" id="hc-female" min="0" value="0"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Children</label>
                    <input type="number" name="children" id="hc-children" min="0" value="0"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Youth</label>
                    <input type="number" name="youth" id="hc-youth" min="0" value="0"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Visitor</label>
                    <input type="number" name="visitors" id="hc-visitors" min="0" value="0"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#2563eb;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save
                </button>
                <button type="button" onclick="closeHeadcountForm()"
                        style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:10px 20px;border-radius:8px;font-size:14px;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Per-event breakdown --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Event</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Digital check-ins</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Manual headcount</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($events as $event)
                <tr style="border-top:1px solid #f3f4f6;">
                    <td style="padding:12px 16px;">
                        <p style="font-weight:500;color:#111827;">{{ $event->title }}</p>
                        <p style="font-size:12px;color:#9ca3af;">{{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</p>
                    </td>
                    <td style="padding:12px 16px;color:#374151;">{{ number_format($event->digital_count) }}</td>
                    <td style="padding:12px 16px;color:#374151;">{{ number_format($event->manual_count) }}</td>
                    <td style="padding:12px 16px;">
                        <button type="button"
                                onclick="openHeadcountForm({{ $event->id }}, '{{ addslashes($event->title) }}', {{ $event->headcount->male ?? 0 }}, {{ $event->headcount->female ?? 0 }}, {{ $event->headcount->children ?? 0 }}, {{ $event->headcount->youth ?? 0 }}, {{ $event->headcount->visitors ?? 0 }})"
                                style="color:#2563eb;font-size:13px;font-weight:500;background:none;border:none;cursor:pointer;padding:0;">
                            {{ $event->headcount ? 'Edit headcount' : '+ Add headcount' }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        No events on this date.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function openAddHeadcountForm() {
            document.getElementById('headcount-form').reset();
            document.getElementById('hc-event-id').value = '';
            document.getElementById('headcount-form-title').innerText = 'Add headcount';
            document.getElementById('headcount-form-panel').style.display = 'block';
            document.getElementById('headcount-form-panel').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function openHeadcountForm(eventId, eventTitle, male, female, children, youth, visitors) {
            document.getElementById('hc-event-id').value = eventId;
            document.getElementById('hc-male').value = male;
            document.getElementById('hc-female').value = female;
            document.getElementById('hc-children').value = children;
            document.getElementById('hc-youth').value = youth;
            document.getElementById('hc-visitors').value = visitors;
            document.getElementById('headcount-form-title').innerText = 'Headcount for ' + eventTitle;
            document.getElementById('headcount-form-panel').style.display = 'block';
            document.getElementById('headcount-form-panel').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function closeHeadcountForm() {
            document.getElementById('headcount-form-panel').style.display = 'none';
        }

        @if($errors->any())
            document.getElementById('headcount-form-panel').style.display = 'block';
        @endif
    </script>

@endsection
