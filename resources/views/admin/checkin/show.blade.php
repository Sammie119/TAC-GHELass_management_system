<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in — {{ $event->title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f9ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .church-logo {
            width: 56px; height: 56px;
            background: #2563eb;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .church-logo svg { width: 28px; height: 28px; color: white; }
        .event-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 0.5rem;
            background: {{ $event->type === 'sunday' ? '#eff6ff' : ($event->type === 'midweek' ? '#f5f3ff' : '#fffbeb') }};
            color: {{ $event->type === 'sunday' ? '#2563eb' : ($event->type === 'midweek' ? '#7c3aed' : '#d97706') }};
        }
        h1 { font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 4px; text-align: center; }
        .event-time { font-size: 13px; color: #6b7280; text-align: center; margin-bottom: 1.5rem; }
        .divider { height: 1px; background: #f3f4f6; margin: 1.5rem 0; }
        label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            color: #111827;
        }
        input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px #bfdbfe; }
        input::placeholder { color: #9ca3af; }
        .hint { font-size: 12px; color: #9ca3af; margin-top: 6px; }
        .btn-primary {
            width: 100%;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.15s;
        }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-primary:disabled { background: #93c5fd; cursor: not-allowed; }
        .btn-secondary {
            width: 100%;
            background: #f9fafb;
            color: #374151;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.15s;
        }
        .btn-secondary:hover { background: #f3f4f6; }

        /* Member confirm card */
        .member-card {
            display: none;
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            align-items: center;
            gap: 12px;
        }
        .member-card.show { display: flex; }
        .avatar {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: #dcfce7;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 700;
            color: #16a34a;
            flex-shrink: 0;
        }
        .member-name { font-size: 16px; font-weight: 600; color: #15803d; }
        .member-id { font-size: 12px; color: #6b7280; font-family: monospace; }

        /* Success screen */
        .success-screen {
            display: none;
            text-align: center;
            padding: 1rem 0;
        }
        .success-screen.show { display: block; }
        .success-icon {
            width: 72px; height: 72px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .success-icon svg { width: 36px; height: 36px; color: #16a34a; }
        .success-title { font-size: 22px; font-weight: 700; color: #15803d; margin-bottom: 6px; }
        .success-sub { font-size: 14px; color: #6b7280; }
        .success-time { font-size: 13px; color: #9ca3af; margin-top: 4px; }

        /* Error */
        .error-box {
            display: none;
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #dc2626;
            margin-top: 1rem;
        }
        .error-box.show { display: block; }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: middle;
            margin-right: 6px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.5} }
    </style>
</head>
<body>

<div class="card">

    {{-- Church branding --}}
    <div class="church-logo">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
    </div>

    <div style="text-align:center; margin-bottom: 1.5rem;">
        <span class="event-badge">{{ ucfirst($event->type) }} service</span>
        <h1>{{ $event->title }}</h1>
        <p class="event-time">
            {{ $event->event_date->format('l, d F Y') }} ·
            {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}
        </p>
    </div>

    {{-- Lookup form --}}
    <div id="lookup-form">
        <label>Enter your member ID, phone, or TACMS No.</label>
        <input type="text"
               id="identifier"
               placeholder="e.g. EL-00001, 0244000001, or TAC00ABC010101"
               autocomplete="off"
               autocorrect="off"
               autocapitalize="off">
        <p class="hint">Your member ID is printed on your membership card.</p>

        <div class="error-box" id="error-box"></div>

        {{-- Member confirm card --}}
        <div class="member-card" id="member-card">
            <div class="avatar" id="member-initials"></div>
            <div>
                <p class="member-name" id="member-name"></p>
                <p class="member-id" id="member-id-card"></p>
            </div>
        </div>

        <button class="btn-primary" id="lookup-btn" onclick="handleLookup()">
            Find my record
        </button>

        <button class="btn-secondary" id="confirm-btn"
                style="display:none;" onclick="handleConfirm()">
            Yes, check me in ✓
        </button>

        <button class="btn-secondary" id="wrong-btn"
                style="display:none; color:#6b7280;" onclick="resetForm()">
            That's not me
        </button>
    </div>

    {{-- Success screen --}}
    <div class="success-screen" id="success-screen">
        <div class="success-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <p class="success-title" id="success-name"></p>
        <p class="success-sub">You're checked in!</p>
        <p class="success-time" id="success-time"></p>
        <div class="divider"></div>
        <p style="font-size:13px;color:#9ca3af;">{{ $event->title }}</p>
        <button class="btn-secondary" style="margin-top:1rem;" onclick="resetAll()">
            Check in another member
        </button>
    </div>

</div>

<script>
    const TOKEN      = '{{ $event->qr_token }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';
    let foundMember  = null;
    let phase        = 'lookup'; // 'lookup' | 'confirm'

    // Allow Enter key to trigger lookup
    document.getElementById('identifier').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            if (phase === 'lookup') handleLookup();
            else if (phase === 'confirm') handleConfirm();
        }
    });

    async function handleLookup() {
        const identifier = document.getElementById('identifier').value.trim();
        if (!identifier) return;

        setLookupBtn(true);
        hideError();
        hideMemberCard();

        const res  = await fetch(`/checkin/${TOKEN}/lookup`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ identifier }),
        });

        const data = await res.json();
        setLookupBtn(false);

        if (!data.success) {
            showError(data.message);
            return;
        }

        if (data.already_checked_in) {
            showError(`${data.member.name} is already checked in to this event.`);
            return;
        }

        foundMember = data.member;
        showMemberCard(data.member);
        phase = 'confirm';
    }

    async function handleConfirm() {
        if (!foundMember) return;

        setConfirmBtn(true);

        const res  = await fetch(`/checkin/${TOKEN}/confirm`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ member_id: foundMember.id }),
        });

        const data = await res.json();
        setConfirmBtn(false);

        if (!data.success) {
            showError(data.message);
            return;
        }

        showSuccess(data.member.name, data.time);
    }

    function showMemberCard(member) {
        document.getElementById('member-initials').textContent = member.initials;
        document.getElementById('member-name').textContent     = member.name;
        document.getElementById('member-id-card').textContent  = member.member_id_card;
        document.getElementById('member-card').classList.add('show');
        document.getElementById('lookup-btn').style.display    = 'none';
        document.getElementById('confirm-btn').style.display   = 'block';
        document.getElementById('wrong-btn').style.display     = 'block';
        document.getElementById('identifier').disabled         = true;
    }

    function hideMemberCard() {
        document.getElementById('member-card').classList.remove('show');
        document.getElementById('lookup-btn').style.display  = 'block';
        document.getElementById('confirm-btn').style.display = 'none';
        document.getElementById('wrong-btn').style.display   = 'none';
    }

    function showSuccess(name, time) {
        document.getElementById('lookup-form').style.display    = 'none';
        document.getElementById('success-screen').classList.add('show');
        document.getElementById('success-name').textContent     = 'Welcome, ' + name.split(' ')[0] + '!';
        document.getElementById('success-time').textContent     = 'Checked in at ' + time;
    }

    function showError(msg) {
        const box = document.getElementById('error-box');
        box.textContent = msg;
        box.classList.add('show');
    }

    function hideError() {
        document.getElementById('error-box').classList.remove('show');
    }

    function resetForm() {
        foundMember = null;
        phase = 'lookup';
        hideMemberCard();
        hideError();
        document.getElementById('identifier').disabled = false;
        document.getElementById('identifier').value    = '';
        document.getElementById('identifier').focus();
    }

    function resetAll() {
        document.getElementById('success-screen').classList.remove('show');
        document.getElementById('lookup-form').style.display = 'block';
        resetForm();
    }

    function setLookupBtn(loading) {
        const btn = document.getElementById('lookup-btn');
        btn.disabled   = loading;
        btn.innerHTML  = loading
            ? '<span class="spinner"></span> Looking up...'
            : 'Find my record';
    }

    function setConfirmBtn(loading) {
        const btn = document.getElementById('confirm-btn');
        btn.disabled   = loading;
        btn.innerHTML  = loading
            ? '<span class="spinner"></span> Checking in...'
            : 'Yes, check me in ✓';
    }
</script>

</body>
</html>
