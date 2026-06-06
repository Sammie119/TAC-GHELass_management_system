@extends('layouts.admin')
@section('page-title', 'New Souls')
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

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">New Souls</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Track and follow up on newly won souls</p>
        </div>
        <button onclick="document.getElementById('add-soul-form').style.display='block'"
                style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
            + Record New Soul
        </button>
    </div>

    {{-- Status summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:12px;margin-bottom:1.5rem;">
        <style>
            @media(min-width:640px)  { .soul-stats { grid-template-columns: repeat(3,1fr) !important; } }
            @media(min-width:1024px) { .soul-stats { grid-template-columns: repeat(6,1fr) !important; } }
        </style>
        <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:12px;" class="soul-stats">

            <a href="?status=new"
               style="background:{{ request('status') === 'new' ? '#dbeafe' : 'white' }};
              border:{{ request('status') === 'new' ? '2px solid #2563eb' : '1px solid #e5e7eb' }};
              border-radius:14px;padding:18px 12px;text-align:center;text-decoration:none;display:block;
              transition:box-shadow 0.15s;"
               onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
               onmouseleave="this.style.boxShadow='none'">
                <div style="font-size:28px;margin-bottom:8px;">✨</div>
                <div style="font-size:28px;font-weight:800;color:#2563eb;line-height:1;">{{ $stats['new'] }}</div>
                <div style="font-size:12px;color:#6b7280;margin-top:6px;font-weight:500;">New</div>
            </a>

            <a href="?status=contacted"
               style="background:{{ request('status') === 'contacted' ? '#fef3c7' : 'white' }};
              border:{{ request('status') === 'contacted' ? '2px solid #d97706' : '1px solid #e5e7eb' }};
              border-radius:14px;padding:18px 12px;text-align:center;text-decoration:none;display:block;
              transition:box-shadow 0.15s;"
               onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
               onmouseleave="this.style.boxShadow='none'">
                <div style="font-size:28px;margin-bottom:8px;">📞</div>
                <div style="font-size:28px;font-weight:800;color:#d97706;line-height:1;">{{ $stats['contacted'] }}</div>
                <div style="font-size:12px;color:#6b7280;margin-top:6px;font-weight:500;">Contacted</div>
            </a>

            <a href="?status=attending"
               style="background:{{ request('status') === 'attending' ? '#ede9fe' : 'white' }};
              border:{{ request('status') === 'attending' ? '2px solid #7c3aed' : '1px solid #e5e7eb' }};
              border-radius:14px;padding:18px 12px;text-align:center;text-decoration:none;display:block;
              transition:box-shadow 0.15s;"
               onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
               onmouseleave="this.style.boxShadow='none'">
                <div style="font-size:28px;margin-bottom:8px;">⛪</div>
                <div style="font-size:28px;font-weight:800;color:#7c3aed;line-height:1;">{{ $stats['attending'] }}</div>
                <div style="font-size:12px;color:#6b7280;margin-top:6px;font-weight:500;">Attending</div>
            </a>

            <a href="?status=baptised"
               style="background:{{ request('status') === 'baptised' ? '#cffafe' : 'white' }};
              border:{{ request('status') === 'baptised' ? '2px solid #0891b2' : '1px solid #e5e7eb' }};
              border-radius:14px;padding:18px 12px;text-align:center;text-decoration:none;display:block;
              transition:box-shadow 0.15s;"
               onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
               onmouseleave="this.style.boxShadow='none'">
                <div style="font-size:28px;margin-bottom:8px;">💧</div>
                <div style="font-size:28px;font-weight:800;color:#0891b2;line-height:1;">{{ $stats['baptised'] }}</div>
                <div style="font-size:12px;color:#6b7280;margin-top:6px;font-weight:500;">Baptised</div>
            </a>

            <a href="?status=converted"
               style="background:{{ request('status') === 'converted' ? '#dcfce7' : 'white' }};
              border:{{ request('status') === 'converted' ? '2px solid #16a34a' : '1px solid #e5e7eb' }};
              border-radius:14px;padding:18px 12px;text-align:center;text-decoration:none;display:block;
              transition:box-shadow 0.15s;"
               onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
               onmouseleave="this.style.boxShadow='none'">
                <div style="font-size:28px;margin-bottom:8px;">🙌</div>
                <div style="font-size:28px;font-weight:800;color:#16a34a;line-height:1;">{{ $stats['converted'] }}</div>
                <div style="font-size:12px;color:#6b7280;margin-top:6px;font-weight:500;">Converted</div>
            </a>

            <a href="?status=backslidden"
               style="background:{{ request('status') === 'backslidden' ? '#fee2e2' : 'white' }};
              border:{{ request('status') === 'backslidden' ? '2px solid #dc2626' : '1px solid #e5e7eb' }};
              border-radius:14px;padding:18px 12px;text-align:center;text-decoration:none;display:block;
              transition:box-shadow 0.15s;"
               onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
               onmouseleave="this.style.boxShadow='none'">
                <div style="font-size:28px;margin-bottom:8px;">🙏</div>
                <div style="font-size:28px;font-weight:800;color:#dc2626;line-height:1;">{{ $stats['backslidden'] }}</div>
                <div style="font-size:12px;color:#6b7280;margin-top:6px;font-weight:500;">Backslidden</div>
            </a>

        </div>
    </div>

    {{-- Total banner --}}
    <div style="background:linear-gradient(135deg,#2563eb,#4f46e5);border-radius:12px;padding:14px 20px;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <svg style="width:22px;height:22px;" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <div>
                <p style="font-size:14px;font-weight:600;color:white;">
                    {{ $stats['total'] }} soul{{ $stats['total'] !== 1 ? 's' : '' }} recorded in total
                </p>
                <p style="font-size:12px;color:rgba(255,255,255,0.75);">
                    {{ $stats['converted'] }} converted · {{ $stats['attending'] }} attending · {{ $stats['new'] }} new
                </p>
            </div>
        </div>
        @if(request('status'))
            <a href="{{ route('admin.souls.index') }}"
               style="background:rgba(255,255,255,0.2);color:white;padding:6px 14px;border-radius:8px;font-size:12px;text-decoration:none;">
                Show all
            </a>
        @endif
    </div>

    {{-- Add soul form --}}
    <div id="add-soul-form"
         style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 style="font-size:15px;font-weight:600;color:#111827;margin-bottom:20px;">Record new soul</h3>

        <form method="POST" action="{{ route('admin.souls.store') }}">
            @csrf

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">First name *</label>
                    <input type="text" name="first_name" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Last name *</label>
                    <input type="text" name="last_name" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Phone</label>
                    <input type="text" name="phone"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Email</label>
                    <input type="email" name="email"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Area / Location</label>
                    <input type="text" name="area" placeholder="e.g. East Legon, Madina"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Date won *</label>
                    <input type="date" name="date_won" value="{{ today()->toDateString() }}" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                {{-- Won by — live search --}}
                <div style="position:relative;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Won by (member)</label>
                    <input type="text" id="won-by-search" placeholder="Search member..."
                           autocomplete="off"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                           oninput="searchWonBy(this.value)"
                           onblur="setTimeout(()=>document.getElementById('won-by-results').style.display='none',200)">
                    <input type="hidden" name="won_by" id="won-by-value">
                    <div id="won-by-results"
                         style="display:none;position:absolute;top:100%;left:0;right:0;background:white;border:1px solid #d1d5db;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);z-index:50;max-height:180px;overflow-y:auto;"></div>
                    <p id="won-by-selected" style="font-size:12px;color:#2563eb;margin-top:4px;display:none;"></p>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Assign follow-up to</label>
                    <select name="assigned_to"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        <option value="">— Select staff —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Church background</label>
                    <input type="text" name="church_background" placeholder="e.g. Catholic, Pentecostal, None"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Address</label>
                <input type="text" name="address" placeholder="Home address"
                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Notes</label>
                <textarea name="notes" rows="2"
                          style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;resize:none;box-sizing:border-box;"
                          placeholder="Any additional details..."></textarea>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#2563eb;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save record
                </button>
                <button type="button" onclick="document.getElementById('add-soul-form').style.display='none'"
                        style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:10px 20px;border-radius:8px;font-size:14px;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name, phone, area..."
               style="flex:1;min-width:200px;border:1px solid #d1d5db;border-radius:8px;padding:8px 14px;font-size:14px;outline:none;">
        <select name="status"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">All statuses</option>
            @foreach($statusConfig as $key => $cfg)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                    {{ $cfg['emoji'] }} {{ $cfg['label'] }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
            Filter
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.souls.index') }}"
               style="border:1px solid #d1d5db;color:#6b7280;padding:8px 14px;border-radius:8px;font-size:13px;text-decoration:none;">
                Clear
            </a>
        @endif
    </form>

    {{-- Souls table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Name</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Contact</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Area</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Date Won</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Won By</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Status</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Follow-ups</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($souls as $soul)
                @php $cfg = $statusConfig[$soul->status]; @endphp
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">

                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:12px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($soul->first_name,0,1).substr($soul->last_name,0,1)) }}
                            </div>
                            <div>
                                <p style="font-weight:600;color:#111827;">{{ $soul->full_name }}</p>
                                <p style="font-size:11px;color:#9ca3af;">Added {{ $soul->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </td>

                    <td style="padding:12px 16px;">
                        <p style="color:#374151;">{{ $soul->phone ?? '—' }}</p>
                        <p style="font-size:11px;color:#9ca3af;">{{ $soul->email ?? '' }}</p>
                    </td>

                    <td style="padding:12px 16px;color:#6b7280;">
                        {{ $soul->area ?? '—' }}
                    </td>

                    <td style="padding:12px 16px;color:#6b7280;">
                        {{ $soul->date_won->format('d M Y') }}
                    </td>

                    <td style="padding:12px 16px;">
                        <p style="font-size:13px;color:#374151;">{{ $soul->wonBy?->full_name ?? '—' }}</p>
                    </td>

                    <td style="padding:12px 16px;" nowrap>
                        <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                                     background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};">
                            {{ $cfg['emoji'] }} {{ $cfg['label'] }}
                        </span>
                    </td>

                    <td style="padding:12px 16px;text-align:center;">
                        <span style="font-size:16px;font-weight:700;color:#{{ $soul->followups->count() > 0 ? '2563eb' : '9ca3af' }};">
                            {{ $soul->followups->count() }}
                        </span>
                    </td>

                    <td style="padding:12px 16px;">
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('admin.souls.show', $soul) }}"
                               style="color:#2563eb;font-size:13px;text-decoration:none;font-weight:500;">
                                View
                            </a>
                            @if(!in_array($soul->status, ['converted']))
                                <a href="{{ route('admin.souls.convert', $soul) }}"
                                   style="color:#16a34a;font-size:13px;text-decoration:none;font-weight:500;">
                                    Convert
                                </a>
                            @endif
                            <form method="POST" action="{{ route('admin.souls.destroy', $soul) }}"
                                  onsubmit="return confirm('Delete this record?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="color:#f87171;font-size:13px;background:none;border:none;cursor:pointer;">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding:48px;text-align:center;">
                        <div style="font-size:40px;margin-bottom:12px;">✨</div>
                        <p style="font-size:14px;color:#9ca3af;">No souls recorded yet. Click "Record New Soul" to get started.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $souls->links() }}</div>

    <script>
        // ── Won by live search ──────────────────────────────────
        let wonByTimer;
        async function searchWonBy(query) {
            clearTimeout(wonByTimer);
            const results = document.getElementById('won-by-results');
            if (query.length < 2) { results.style.display = 'none'; return; }

            wonByTimer = setTimeout(async () => {
                const res  = await fetch(`{{ route('admin.checkin.search') }}?query=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (!data.length) {
                    results.innerHTML = `<div style="padding:10px 14px;font-size:13px;color:#9ca3af;">No members found.</div>`;
                    results.style.display = 'block';
                    return;
                }

                results.innerHTML = data.map(m => `
            <div onclick="pickWonBy(${m.id}, '${m.first_name} ${m.last_name}')"
                 style="padding:9px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f3f4f6;"
                 onmouseenter="this.style.background='#eff6ff'"
                 onmouseleave="this.style.background=''">
                <span style="font-weight:500;color:#111827;">${m.first_name} ${m.last_name}</span>
                <span style="color:#9ca3af;font-family:monospace;font-size:11px;margin-left:8px;">${m.member_id_card}</span>
            </div>
        `).join('');
                results.style.display = 'block';
            }, 300);
        }

        function pickWonBy(id, name) {
            document.getElementById('won-by-value').value    = id;
            document.getElementById('won-by-search').value   = name;
            document.getElementById('won-by-results').style.display = 'none';
            const sel = document.getElementById('won-by-selected');
            sel.textContent = '✓ ' + name + ' selected';
            sel.style.display = 'block';
        }
    </script>

@endsection
