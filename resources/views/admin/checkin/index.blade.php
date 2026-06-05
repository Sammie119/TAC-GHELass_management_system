@extends('layouts.admin')
@section('page-title', 'Check-in')
@section('content')

    {{-- No active event banner --}}
    @if(!$activeEvent)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-yellow-800">No active event</p>
                    <p class="text-xs text-yellow-600">Activate an event first to start check-in.</p>
                </div>
            </div>
            <a href="{{ route('admin.events.index') }}"
               class="text-sm bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                Go to Events
            </a>
        </div>
    @endif

    <div style="display:grid; grid-template-columns: 1fr; gap:1.5rem;"
         class="lg-grid-3">
        <style>
            @media(min-width:1024px){
                .lg-grid-3{ grid-template-columns: 2fr 1fr !important; }
            }
        </style>

        {{-- Left: Check-in panel --}}
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            @if($activeEvent)
                <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span style="width:10px;height:10px;background:#22c55e;border-radius:50%;display:inline-block;animation:pulse 2s infinite;"></span>
                        <div>
                            <p class="text-sm font-medium text-green-800">{{ $activeEvent->title }}</p>
                            <p class="text-xs text-green-600">
                                {{ $activeEvent->event_date->format('D, d M Y') }} ·
                                {{ \Carbon\Carbon::parse($activeEvent->start_time)->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-700" id="total-count">
                            {{ $recentCheckins->count() }}
                        </p>
                        <p class="text-xs text-green-600">checked in</p>
                    </div>
                </div>
            @endif

            {{-- Check-in tabs --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

                <div class="flex border-b border-gray-200">
                    <button onclick="switchTab('search')" id="tab-search"
                            style="flex:1;padding:12px;font-size:14px;font-weight:500;border-bottom:2px solid #2563eb;color:#2563eb;background:none;cursor:pointer;">
                        Name / ID Search
                    </button>
                    <button onclick="switchTab('qr')" id="tab-qr"
                            style="flex:1;padding:12px;font-size:14px;font-weight:500;border-bottom:2px solid transparent;color:#6b7280;background:none;cursor:pointer;">
                        QR Code Scan
                    </button>
                    <button onclick="switchTab('manual')" id="tab-manual"
                            style="flex:1;padding:12px;font-size:14px;font-weight:500;border-bottom:2px solid transparent;color:#6b7280;background:none;cursor:pointer;">
                        Manual Entry
                    </button>
                </div>

                {{-- Name/ID Search tab --}}
                <div id="panel-search" class="p-6">
                    <div style="position:relative;margin-bottom:1rem;">
                        <input type="text" id="search-input"
                               placeholder="Type name or member ID..."
                               autocomplete="off"
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:12px 40px 12px 16px;font-size:14px;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 2px #bfdbfe'"
                               onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                            {{ !$activeEvent ? 'disabled' : '' }}>
                        <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#9ca3af;pointer-events:none;"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div id="search-results" style="display:none;flex-direction:column;gap:8px;"></div>
                </div>

                {{-- QR Code scan tab --}}
                <div id="panel-qr" class="p-6" style="display:none;">
                    <div style="border:2px dashed #e5e7eb;border-radius:12px;padding:2rem;text-align:center;margin-bottom:1rem;">
                        <div id="qr-scanner-area">
                            <svg style="width:48px;height:48px;color:#d1d5db;margin:0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <p style="font-size:14px;color:#9ca3af;margin-bottom:12px;">Point camera at member's QR code</p>
                            <button onclick="startScanner()" id="start-scanner-btn"
                                    style="background:#2563eb;color:white;padding:8px 20px;border-radius:8px;font-size:14px;border:none;cursor:pointer;"
                                {{ !$activeEvent ? 'disabled' : '' }}>
                                Start Camera
                            </button>
                        </div>
                        <video id="qr-video" style="display:none;width:100%;border-radius:8px;" autoplay playsinline></video>
                    </div>
                    <div>
                        <p style="font-size:12px;color:#9ca3af;text-align:center;margin-bottom:8px;">Or paste QR code value manually:</p>
                        <div style="display:flex;gap:8px;">
                            <input type="text" id="qr-manual-input" placeholder="Paste QR code here..."
                                   style="flex:1;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;"
                                {{ !$activeEvent ? 'disabled' : '' }}>
                            <button onclick="processQrManual()"
                                    style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;"
                                {{ !$activeEvent ? 'disabled' : '' }}>
                                Lookup
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Manual / Usher tab --}}
                <div id="panel-manual" class="p-6" style="display:none;">
                    <p style="font-size:14px;color:#6b7280;margin-bottom:1rem;">
                        Select a member below and mark them as present manually.
                    </p>
                    <div style="position:relative;margin-bottom:1rem;">
                        <input type="text" id="manual-search-input"
                               placeholder="Type member name..."
                               autocomplete="off"
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:12px 16px;font-size:14px;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 2px #bfdbfe'"
                               onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                            {{ !$activeEvent ? 'disabled' : '' }}>
                    </div>
                    <div id="manual-search-results" style="display:none;flex-direction:column;gap:8px;"></div>
                </div>

            </div>

            {{-- Result toast --}}
            <div id="checkin-toast"
                 style="display:none;border-radius:12px;padding:16px 20px;border:1px solid;display:none;align-items:center;gap:16px;">
                <div id="toast-icon"
                     style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;flex-shrink:0;">
                </div>
                <div style="flex:1;">
                    <p id="toast-name" style="font-weight:600;font-size:14px;color:#1f2937;"></p>
                    <p id="toast-message" style="font-size:12px;color:#6b7280;margin-top:2px;"></p>
                </div>
                <p id="toast-time" style="font-size:12px;color:#9ca3af;flex-shrink:0;"></p>
            </div>

            {{-- Quick visitor record button --}}
            @if($activeEvent)
                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p style="font-size:14px;font-weight:500;color:#111827;">First-time visitor?</p>
                        <p style="font-size:12px;color:#9ca3af;">Record their details for follow-up</p>
                    </div>
                    <a href="{{ route('admin.visitors.create', ['event_id' => $activeEvent->id]) }}"
                       style="background:#fef3c7;color:#d97706;border:1px solid #fde68a;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">
                        + Record Visitor
                    </a>
                </div>
            @endif

            {{-- Self check-in link --}}
            @if($activeEvent)
                <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-blue-800">Self check-in link</p>
{{--                        <p class="text-xs text-blue-600 font-mono mt-0.5">--}}
{{--                            {{ route('checkin.show', $activeEvent->qr_token) }}--}}
{{--                        </p>--}}
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.events.qr', $activeEvent) }}"
                           class="text-xs bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">
                            Download QR
                        </a>
                        <a href="{{ route('checkin.show', $activeEvent->qr_token) }}" target="_blank"
                           class="text-xs border border-blue-300 text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-100">
                            Preview
                        </a>
                    </div>
                </div>
            @endif

        </div>

        {{-- Right: Recent check-ins feed --}}
        <div>
            <div class="bg-white rounded-xl border border-gray-200">
                <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-weight:600;font-size:14px;color:#1f2937;">Recent check-ins</h3>
                    <span id="feed-count"
                          style="font-size:12px;background:#eff6ff;color:#2563eb;padding:2px 10px;border-radius:20px;font-weight:500;">
                    {{ count($recentCheckins) }}
                </span>
                </div>

                <div id="checkin-feed">
                    @forelse($recentCheckins as $checkin)
                        <div id="feed-row-{{ $checkin->member_id }}"
                             style="padding:12px 16px;display:flex;align-items:center;gap:12px;border-bottom:1px solid #f9fafb;"
                             onmouseenter="this.querySelector('.remove-btn').style.opacity='1'"
                             onmouseleave="this.querySelector('.remove-btn').style.opacity='0'">
                            <div style="width:32px;height:32px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:12px;font-weight:600;flex-shrink:0;">
                                {{ strtoupper(substr($checkin->member->first_name,0,1).substr($checkin->member->last_name,0,1)) }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:14px;font-weight:500;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $checkin->member->full_name }}
                                </p>
                                <p style="font-size:12px;color:#9ca3af;">{{ $checkin->checked_in_at->format('h:i A') }}</p>
                            </div>
                            <button class="remove-btn"
                                    onclick="undoCheckin({{ $checkin->member_id }}, '{{ $checkin->member->full_name }}')"
                                    title="Remove check-in"
                                    style="opacity:0;transition:opacity 0.2s;color:#f87171;background:none;border:none;cursor:pointer;padding:4px;border-radius:4px;">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div id="feed-empty" style="padding:32px 20px;text-align:center;color:#9ca3af;font-size:14px;">
                            No check-ins yet.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Upcoming events --}}
            @if(!$activeEvent && count($upcomingEvents))
                <div class="bg-white rounded-xl border border-gray-200 mt-4">
                    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                        <h3 style="font-weight:600;font-size:14px;color:#1f2937;">Upcoming events</h3>
                    </div>
                    <div>
                        @foreach($upcomingEvents as $event)
                            <div style="padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f9fafb;">
                                <div>
                                    <p style="font-size:14px;font-weight:500;color:#1f2937;">{{ $event->title }}</p>
                                    <p style="font-size:12px;color:#9ca3af;">{{ $event->event_date->format('D, d M') }}</p>
                                </div>
                                <form method="POST" action="{{ route('admin.events.activate', $event) }}">
                                    @csrf
                                    <button type="submit"
                                            style="font-size:12px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;padding:4px 12px;border-radius:8px;cursor:pointer;">
                                        Activate
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>

    <script>
        const ACTIVE_EVENT_ID = {{ $activeEvent ? $activeEvent->id : 'null' }};
        const CSRF_TOKEN = '{{ csrf_token() }}';

        // ── Tab switching ──────────────────────────────────────────
        function switchTab(tab) {
            ['search', 'qr', 'manual'].forEach(t => {
                document.getElementById('panel-' + t).style.display = 'none';
                const btn = document.getElementById('tab-' + t);
                btn.style.borderBottomColor = 'transparent';
                btn.style.color = '#6b7280';
            });
            document.getElementById('panel-' + tab).style.display = 'block';
            const activeBtn = document.getElementById('tab-' + tab);
            activeBtn.style.borderBottomColor = '#2563eb';
            activeBtn.style.color = '#2563eb';
        }

        // ── Name search ────────────────────────────────────────────
        let searchTimer;
        document.getElementById('search-input')?.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const q = this.value.trim();
            const results = document.getElementById('search-results');
            if (q.length < 2) { results.style.display = 'none'; return; }
            searchTimer = setTimeout(() => fetchMembers(q, 'search-results', 'name_search'), 300);
        });

        document.getElementById('manual-search-input')?.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const q = this.value.trim();
            const results = document.getElementById('manual-search-results');
            if (q.length < 2) { results.style.display = 'none'; return; }
            searchTimer = setTimeout(() => fetchMembers(q, 'manual-search-results', 'usher_marked'), 300);
        });

        async function fetchMembers(query, resultsId, method) {
            const res  = await fetch(`{{ route('admin.checkin.search') }}?query=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            renderResults(data, resultsId, method);
        }

        function renderResults(members, resultsId, method) {
            const container = document.getElementById(resultsId);
            container.style.display = 'flex';

            if (!members.length) {
                container.innerHTML = `<p style="font-size:14px;color:#9ca3af;text-align:center;padding:12px;">No members found.</p>`;
                return;
            }

            container.innerHTML = members.map(m => `
        <div onclick="confirmCheckin(${m.id}, '${m.first_name} ${m.last_name}', '${method}', '${getInitials(m.first_name, m.last_name)}')"
             style="display:flex;align-items:center;justify-content:space-between;padding:12px;border-radius:8px;border:1px solid #f3f4f6;cursor:pointer;transition:background 0.15s;"
             onmouseenter="this.style.background='#eff6ff';this.style.borderColor='#bfdbfe'"
             onmouseleave="this.style.background='';this.style.borderColor='#f3f4f6'">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:32px;height:32px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:12px;font-weight:600;">
                    ${getInitials(m.first_name, m.last_name)}
                </div>
                <div>
                    <p style="font-size:14px;font-weight:500;color:#1f2937;">${m.first_name} ${m.last_name}</p>
                    <p style="font-size:12px;color:#9ca3af;font-family:monospace;">${m.member_id_card}</p>
                </div>
            </div>
            <span style="font-size:12px;background:#2563eb;color:white;padding:4px 12px;border-radius:8px;">Check in</span>
        </div>
    `).join('');
        }

        function getInitials(first, last) {
            return (first.charAt(0) + last.charAt(0)).toUpperCase();
        }

        // ── QR manual input ────────────────────────────────────────
        async function processQrManual() {
            const qrCode = document.getElementById('qr-manual-input').value.trim();
            if (!qrCode) return;

            const res  = await fetch('{{ route('admin.checkin.qr') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({ qr_code: qrCode }),
            });
            const data = await res.json();

            if (data.success) {
                confirmCheckin(data.member.id, data.member.name, 'qr_scan', data.member.initials);
            } else {
                showToast(false, 'Not found', data.message, '');
            }
        }

        // ── Camera QR scanner ─────────────────────────────────────
        let scanning = false;
        async function startScanner() {
            if (scanning) return;
            scanning = true;

            const video = document.getElementById('qr-video');
            const area  = document.getElementById('qr-scanner-area');

            area.style.display = 'none';
            video.style.display = 'block';

            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            video.play();

            const canvas = document.createElement('canvas');
            const ctx    = canvas.getContext('2d');

            const script = document.createElement('script');
            script.src   = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
            document.head.appendChild(script);

            script.onload = function () {
                const tick = () => {
                    if (video.readyState === video.HAVE_ENOUGH_DATA) {
                        canvas.width  = video.videoWidth;
                        canvas.height = video.videoHeight;
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height);

                        if (code) {
                            stream.getTracks().forEach(t => t.stop());
                            video.style.display = 'none';
                            area.style.display  = 'block';
                            scanning = false;

                            fetch('{{ route('admin.checkin.qr') }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                                body: JSON.stringify({ qr_code: code.data }),
                            })
                                .then(r => r.json())
                                .then(data => {
                                    if (data.success) {
                                        confirmCheckin(data.member.id, data.member.name, 'qr_scan', data.member.initials);
                                    } else {
                                        showToast(false, 'Not found', data.message, '');
                                    }
                                });
                            return;
                        }
                    }
                    requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            };
        }

        // ── Check-in submission ────────────────────────────────────
        async function confirmCheckin(memberId, memberName, method, initials) {
            if (!ACTIVE_EVENT_ID) {
                showToast(false, 'No active event', 'Please activate an event first.', '');
                return;
            }

            const res  = await fetch('{{ route('admin.checkin.process') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({
                    member_id: memberId,
                    event_id:  ACTIVE_EVENT_ID,
                    method:    method,
                }),
            });

            const data = await res.json();

            if (data.success) {
                showToast(true, data.member.name, data.message, data.time, initials);
                prependToFeed(data.member, data.time);
                updateCount(data.total);
                ['search-input', 'manual-search-input', 'qr-manual-input'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
                ['search-results', 'manual-search-results'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.style.display = 'none';
                });
            } else {
                showToast(false, memberName, data.message, data.time ?? '');
            }
        }

        // ── Toast notification ─────────────────────────────────────
        function showToast(success, name, message, time, initials = '?') {
            const toast = document.getElementById('checkin-toast');
            const icon  = document.getElementById('toast-icon');

            toast.style.display = 'flex';

            if (success) {
                toast.style.background   = '#f0fdf4';
                toast.style.borderColor  = '#bbf7d0';
                icon.style.background    = '#22c55e';
            } else {
                toast.style.background   = '#fef2f2';
                toast.style.borderColor  = '#fecaca';
                icon.style.background    = '#ef4444';
            }

            icon.textContent = success ? initials : '!';
            document.getElementById('toast-name').textContent    = name;
            document.getElementById('toast-message').textContent = message;
            document.getElementById('toast-time').textContent    = time;

            setTimeout(() => { toast.style.display = 'none'; }, 5000);
        }

        // ── Prepend to live feed ───────────────────────────────────
        function prependToFeed(member, time) {
            const feed  = document.getElementById('checkin-feed');
            const empty = document.getElementById('feed-empty');
            if (empty) empty.remove();

            const div = document.createElement('div');
            div.id    = 'feed-row-' + member.id;
            div.style.cssText = 'padding:12px 16px;display:flex;align-items:center;gap:12px;border-bottom:1px solid #f9fafb;background:#f0fdf4;transition:background 0.5s;';
            div.setAttribute('onmouseenter', "this.querySelector('.remove-btn').style.opacity='1'");
            div.setAttribute('onmouseleave', "this.querySelector('.remove-btn').style.opacity='0'");

            div.innerHTML = `
        <div style="width:32px;height:32px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:12px;font-weight:600;flex-shrink:0;">
            ${member.initials}
        </div>
        <div style="flex:1;min-width:0;">
            <p style="font-size:14px;font-weight:500;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${member.name}</p>
            <p style="font-size:12px;color:#9ca3af;">${time}</p>
        </div>
        <button class="remove-btn"
                onclick="undoCheckin(${member.id}, '${member.name}')"
                title="Remove check-in"
                style="opacity:0;transition:opacity 0.2s;color:#f87171;background:none;border:none;cursor:pointer;padding:4px;border-radius:4px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;

            feed.prepend(div);
            setTimeout(() => { div.style.background = ''; }, 2000);

            const rows = feed.querySelectorAll('[id^="feed-row-"]');
            if (rows.length > 10) rows[rows.length - 1].remove();
        }

        // ── Update counts ──────────────────────────────────────────
        function updateCount(total) {
            const el = document.getElementById('total-count');
            const fc = document.getElementById('feed-count');
            if (el) el.textContent = total;
            if (fc) fc.textContent = total;
        }

        // ── Undo / Remove check-in ─────────────────────────────────
        async function undoCheckin(memberId, memberName) {
            if (!confirm(`Remove check-in for ${memberName}?`)) return;

            const res = await fetch('{{ route('admin.checkin.remove') }}', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({ member_id: memberId, event_id: ACTIVE_EVENT_ID }),
            });

            const data = await res.json();

            if (data.success) {
                const row = document.getElementById('feed-row-' + memberId);
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity    = '0';
                    setTimeout(() => row.remove(), 300);
                }
                updateCount(data.total);
                showToast(false, memberName, data.message, '');
            } else {
                alert(data.message);
            }
        }
    </script>

@endsection
