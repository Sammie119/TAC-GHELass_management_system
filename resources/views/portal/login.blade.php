<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Portal — {{ config('app.name', 'Church Management System') }}</title>

    {{-- Favicons --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon.svg">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 1rem;
        }
        .card {
            background: white; border-radius: 24px;
            padding: 2.5rem 2rem; width: 100%; max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .logo {
            width: 60px; height: 60px; background: #2563eb; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }
        h1 { font-size: 22px; font-weight: 700; color: #111827; text-align: center; margin-bottom: 4px; }
        .subtitle { font-size: 14px; color: #6b7280; text-align: center; margin-bottom: 2rem; }
        label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        input {
            width: 100%; border: 1.5px solid #e5e7eb; border-radius: 12px;
            padding: 13px 16px; font-size: 15px; outline: none;
            transition: border-color 0.15s, box-shadow 0.15s; color: #111827;
        }
        input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px #bfdbfe; }
        input::placeholder { color: #9ca3af; }
        .hint { font-size: 12px; color: #9ca3af; margin-top: 6px; margin-bottom: 1.25rem; }
        .btn {
            width: 100%; background: #2563eb; color: white; border: none;
            border-radius: 12px; padding: 14px; font-size: 16px; font-weight: 600;
            cursor: pointer; transition: background 0.15s;
        }
        .btn:hover { background: #1d4ed8; }
        .error {
            background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
            padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 16px;
        }
        .admin-link {
            text-align: center; margin-top: 1.5rem;
            font-size: 13px; color: #9ca3af;
        }
        .admin-link a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <svg style="width:30px;height:30px;" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </div>

    <h1>Member Portal</h1>
    <p class="subtitle">Sign in to view your attendance and profile</p>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('portal.lookup') }}">
        @csrf
        <label>Member ID, Phone, or TACMS No.</label>
        <input type="text" name="identifier"
               value="{{ old('identifier') }}"
               placeholder="e.g. EL-00001, 0244000001, or TAC00ABC010101"
               autocomplete="off" autocorrect="off" autocapitalize="off"
               required>
        <p class="hint">Your member ID is printed on your ID card.</p>
        <button type="submit" class="btn">Continue →</button>
    </form>

    <div class="admin-link">
        ← Go Back to <a href="{{ url('/') }}">Welcome Page</a>
    </div>
</div>
</body>
</html>
