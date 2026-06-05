@extends('layouts.admin')
@section('page-title', 'Visitor Profile')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">{{ $visitor->full_name }}</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">
                First visited {{ $visitor->visited_at->format('D, d M Y') }}
            </p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.visitors.edit', $visitor) }}"
               style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
                Edit
            </a>
            <a href="{{ route('admin.visitors.index') }}"
               style="border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
                ← Back
            </a>
        </div>
    </div>

    <div style="display:grid;gap:24px;">
        <style>@media(min-width:1024px){.show-grid{grid-template-columns:1fr 2fr !important;}}</style>
        <div style="display:grid;gap:24px;" class="show-grid">

            {{-- Left: Profile --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;text-align:center;">
                    <div style="width:64px;height:64px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;color:#d97706;font-size:22px;font-weight:700;margin:0 auto 12px;">
                        {{ strtoupper(substr($visitor->first_name,0,1).substr($visitor->last_name,0,1)) }}
                    </div>
                    <p style="font-size:16px;font-weight:600;color:#111827;">{{ $visitor->full_name }}</p>
                    <span style="display:inline-block;margin-top:6px;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:500;
                {{ count($previousVisits) > 0 ? 'background:#dbeafe;color:#1d4ed8;' : 'background:#fef3c7;color:#d97706;' }}">
                {{ count($previousVisits) > 0 ? 'Returning visitor' : 'First-time visitor' }}
            </span>
                </div>

                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
                    <h3 style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:14px;">Contact details</h3>
                    <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Phone</span>
                            <span style="font-weight:500;color:#111827;">{{ $visitor->phone ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Email</span>
                            <span style="font-weight:500;color:#111827;font-size:13px;">{{ $visitor->email ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Recorded by</span>
                            <span style="font-weight:500;color:#111827;">{{ $visitor->recordedBy->name ?? 'System' }}</span>
                        </div>
                    </div>
                </div>

                @if($visitor->notes)
                    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
                        <h3 style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">Notes</h3>
                        <p style="font-size:14px;color:#374151;line-height:1.6;">{{ $visitor->notes }}</p>
                    </div>
                @endif

            </div>

            {{-- Right: Visit history --}}
            <div>
                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;">
                    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                        <h3 style="font-size:14px;font-weight:600;color:#111827;">This visit</h3>
                    </div>
                    <div style="padding:20px;">
                        <div style="display:flex;align-items:center;gap:14px;padding:14px;background:#f9fafb;border-radius:10px;">
                            <div style="width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;
                        {{ $visitor->event->type === 'sunday'  ? 'background:#dbeafe;color:#2563eb;' : '' }}
                        {{ $visitor->event->type === 'midweek' ? 'background:#ede9fe;color:#7c3aed;' : '' }}
                        {{ $visitor->event->type === 'special' ? 'background:#fef3c7;color:#d97706;' : '' }}">
                                {{ strtoupper(substr($visitor->event->type,0,2)) }}
                            </div>
                            <div>
                                <p style="font-size:14px;font-weight:500;color:#111827;">{{ $visitor->event->title }}</p>
                                <p style="font-size:12px;color:#9ca3af;">
                                    {{ $visitor->event->event_date->format('D, d M Y') }} ·
                                    {{ $visitor->visited_at->format('h:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Previous visits --}}
                @if(count($previousVisits) > 0)
                    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;margin-top:16px;">
                        <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                            <h3 style="font-size:14px;font-weight:600;color:#111827;">
                                Previous visits
                                <span style="font-size:12px;color:#9ca3af;font-weight:400;margin-left:6px;">{{ count($previousVisits) }} other {{ Str::plural('visit', count($previousVisits)) }}</span>
                            </h3>
                        </div>
                        <div>
                            @foreach($previousVisits as $prev)
                                <div style="padding:14px 20px;border-bottom:1px solid #f9fafb;display:flex;align-items:center;justify-content:space-between;"
                                     onmouseenter="this.style.background='#f9fafb'"
                                     onmouseleave="this.style.background=''">
                                    <div>
                                        <p style="font-size:14px;font-weight:500;color:#111827;">{{ $prev->event->title }}</p>
                                        <p style="font-size:12px;color:#9ca3af;">{{ $prev->event->event_date->format('D, d M Y') }}</p>
                                    </div>
                                    <a href="{{ route('admin.visitors.show', $prev) }}"
                                       style="font-size:12px;color:#2563eb;text-decoration:none;">View →</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Convert to member --}}
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:20px;margin-top:16px;">
                    <p style="font-size:14px;font-weight:600;color:#1e40af;margin-bottom:6px;">Ready to become a member?</p>
                    <p style="font-size:13px;color:#3b82f6;margin-bottom:14px;line-height:1.5;">
                        If this visitor is joining the church, you can register them as a full member.
                    </p>
                    <a href="{{ route('admin.members.create') }}?first_name={{ urlencode($visitor->first_name) }}&last_name={{ urlencode($visitor->last_name) }}&phone={{ urlencode($visitor->phone ?? '') }}&email={{ urlencode($visitor->email ?? '') }}"
                       style="display:inline-block;background:#2563eb;color:white;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">
                        Register as member →
                    </a>
                </div>

            </div>

        </div>
    </div>

@endsection
