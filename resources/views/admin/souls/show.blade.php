@extends('layouts.admin')
@section('page-title', $soul->full_name)
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('admin.souls.index') }}"
               style="color:#9ca3af;text-decoration:none;font-size:13px;">← Back</a>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">{{ $soul->full_name }}</h2>
        </div>
        @if($soul->status !== 'converted')
            <a href="{{ route('admin.souls.convert', $soul) }}"
               style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                🙌 Convert to Member
            </a>
        @endif
    </div>

    @php
        $statusConfig = [
            'new'         => ['label'=>'New',        'color'=>'#2563eb','bg'=>'#dbeafe','emoji'=>'✨'],
            'contacted'   => ['label'=>'Contacted',  'color'=>'#d97706','bg'=>'#fef3c7','emoji'=>'📞'],
            'attending'   => ['label'=>'Attending',  'color'=>'#7c3aed','bg'=>'#ede9fe','emoji'=>'⛪'],
            'baptised'    => ['label'=>'Baptised',   'color'=>'#0891b2','bg'=>'#cffafe','emoji'=>'💧'],
            'converted'   => ['label'=>'Converted',  'color'=>'#16a34a','bg'=>'#dcfce7','emoji'=>'🙌'],
            'backslidden' => ['label'=>'Backslidden','color'=>'#dc2626','bg'=>'#fee2e2','emoji'=>'🙏'],
        ];
    @endphp

    <div style="display:grid;gap:20px;">
        <style>@media(min-width:1024px){.soul-grid{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:20px;" class="soul-grid">

            {{-- Left: Profile + status --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Profile card --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
                        <div style="width:52px;height:52px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:18px;font-weight:700;flex-shrink:0;">
                            {{ strtoupper(substr($soul->first_name,0,1).substr($soul->last_name,0,1)) }}
                        </div>
                        <div>
                            <h3 style="font-size:17px;font-weight:700;color:#111827;">{{ $soul->full_name }}</h3>
                            <span style="padding:3px 12px;border-radius:20px;font-size:11px;font-weight:600;
                                 background:{{ $statusConfig[$soul->status]['bg'] }};
                                 color:{{ $statusConfig[$soul->status]['color'] }};">
                        {{ $statusConfig[$soul->status]['emoji'] }} {{ $statusConfig[$soul->status]['label'] }}
                    </span>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Phone</span>
                            <span style="font-weight:500;color:#111827;">{{ $soul->phone ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Email</span>
                            <span style="font-weight:500;color:#111827;">{{ $soul->email ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Area</span>
                            <span style="font-weight:500;color:#111827;">{{ $soul->area ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Address</span>
                            <span style="font-weight:500;color:#111827;text-align:right;max-width:200px;">{{ $soul->address ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Date won</span>
                            <span style="font-weight:500;color:#111827;">{{ $soul->date_won->format('d M Y') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Won by</span>
                            <span style="font-weight:500;color:#111827;">{{ $soul->wonBy?->full_name ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Church background</span>
                            <span style="font-weight:500;color:#111827;">{{ $soul->church_background ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Assigned to</span>
                            <span style="font-weight:500;color:#111827;">{{ $soul->assignedTo?->name ?? '—' }}</span>
                        </div>
                    </div>

                    @if($soul->notes)
                        <div style="margin-top:14px;background:#f9fafb;border-radius:8px;padding:10px 12px;">
                            <p style="font-size:12px;color:#9ca3af;margin-bottom:4px;">Notes</p>
                            <p style="font-size:13px;color:#374151;">{{ $soul->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Update status --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Update status</h3>
                    <form method="POST" action="{{ route('admin.souls.status', $soul) }}">
                        @csrf
                        <div style="margin-bottom:12px;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">New status</label>
                            <select name="status"
                                    style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                                @foreach($statusConfig as $key => $cfg)
                                    <option value="{{ $key }}" {{ $soul->status === $key ? 'selected' : '' }}>
                                        {{ $cfg['emoji'] }} {{ $cfg['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Assign to</label>
                            <select name="assigned_to"
                                    style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                                <option value="">— Keep current —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $soul->assigned_to == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-bottom:14px;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Update notes</label>
                            <textarea name="notes" rows="2"
                                      style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;resize:none;box-sizing:border-box;">{{ $soul->notes }}</textarea>
                        </div>
                        <button type="submit"
                                style="width:100%;background:#2563eb;color:white;padding:10px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                            Update status
                        </button>
                    </form>
                </div>

            </div>

            {{-- Right: Follow-up log --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Add follow-up --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Log follow-up</h3>
                    <form method="POST" action="{{ route('admin.souls.followup', $soul) }}">
                        @csrf
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Method</label>
                                <select name="method"
                                        style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
                                    <option value="phone">📞 Phone call</option>
                                    <option value="visit">🏠 Home visit</option>
                                    <option value="sms">💬 SMS</option>
                                    <option value="email">✉️ Email</option>
                                    <option value="church">⛪ At church</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Outcome</label>
                                <select name="outcome"
                                        style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
                                    <option value="spoke">✅ Spoke with them</option>
                                    <option value="visited_church">⛪ Visited church</option>
                                    <option value="no_answer">📵 No answer</option>
                                    <option value="not_interested">❌ Not interested</option>
                                    <option value="other">📝 Other</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Date *</label>
                                <input type="date" name="followup_date" value="{{ today()->toDateString() }}" required
                                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box;">
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Next follow-up</label>
                                <input type="date" name="next_followup_date"
                                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box;">
                            </div>
                        </div>
                        <div style="margin-bottom:14px;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Notes</label>
                            <textarea name="notes" rows="2"
                                      style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;resize:none;box-sizing:border-box;"
                                      placeholder="What was discussed? Any prayer requests?"></textarea>
                        </div>
                        <button type="submit"
                                style="width:100%;background:#16a34a;color:white;padding:10px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                            Log follow-up
                        </button>
                    </form>
                </div>

                {{-- Follow-up history --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                    <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                        <h3 style="font-size:14px;font-weight:600;color:#111827;">Follow-up history</h3>
                        <span style="font-size:12px;color:#9ca3af;">{{ $soul->followups->count() }} logs</span>
                    </div>

                    @forelse($soul->followups as $log)
                        @php
                            $methodIcons  = ['phone'=>'📞','visit'=>'🏠','sms'=>'💬','email'=>'✉️','church'=>'⛪'];
                            $outcomeColors = [
                                'spoke'          => ['bg'=>'#dcfce7','color'=>'#15803d','label'=>'Spoke with them'],
                                'visited_church' => ['bg'=>'#ede9fe','color'=>'#7c3aed','label'=>'Visited church'],
                                'no_answer'      => ['bg'=>'#fef3c7','color'=>'#d97706','label'=>'No answer'],
                                'not_interested' => ['bg'=>'#fee2e2','color'=>'#dc2626','label'=>'Not interested'],
                                'other'          => ['bg'=>'#f3f4f6','color'=>'#6b7280','label'=>'Other'],
                            ];
                            $oc = $outcomeColors[$log->outcome] ?? $outcomeColors['other'];
                        @endphp

                        <div style="padding:14px 16px;border-bottom:1px solid #f9fafb;">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="font-size:16px;">{{ $methodIcons[$log->method] ?? '📝' }}</span>
                                    <div>
                                        <p style="font-size:13px;font-weight:600;color:#111827;">
                                            {{ $log->followup_date->format('d M Y') }}
                                        </p>
                                        <p style="font-size:11px;color:#9ca3af;">by {{ $log->user?->name ?? '—' }}</p>
                                    </div>
                                </div>
                                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;
                                 background:{{ $oc['bg'] }};color:{{ $oc['color'] }};">
                        {{ $oc['label'] }}
                    </span>
                            </div>

                            @if($log->notes)
                                <p style="font-size:13px;color:#374151;margin-bottom:4px;">{{ $log->notes }}</p>
                            @endif

                            @if($log->next_followup_date)
                                <p style="font-size:11px;color:#9ca3af;">
                                    Next follow-up: <strong style="color:#2563eb;">{{ $log->next_followup_date->format('d M Y') }}</strong>
                                </p>
                            @endif
                        </div>
                    @empty
                        <div style="padding:32px;text-align:center;color:#9ca3af;font-size:14px;">
                            No follow-ups logged yet.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

@endsection
