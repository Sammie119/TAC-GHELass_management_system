<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELMAS — Login</title>
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
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .logo {
            width: 56px; height: 56px;
            background: #2563eb;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .logo svg { width: 28px; height: 28px; }
        h1 {
            font-size: 22px; font-weight: 700;
            color: #111827; text-align: center;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 14px; color: #6b7280;
            text-align: center; margin-bottom: 2rem;
        }
        label {
            display: block; font-size: 13px;
            font-weight: 500; color: #374151;
            margin-bottom: 6px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            color: #111827;
            margin-bottom: 16px;
        }
        input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px #bfdbfe;
        }
        .checkbox-row {
            display: flex; align-items: center;
            gap: 8px; margin-bottom: 20px;
            font-size: 13px; color: #6b7280;
        }
        .btn {
            width: 100%;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn:hover { background: #1d4ed8; }
        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
<div class="card">

    <div class="logo">
        <svg fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
    </div>

    <h1>{{ config('app.name', 'Church Management System') }}</h1>
    <p class="subtitle">Sign in to your admin account</p>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Session status --}}
    @if(session('status'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label>Email address</label>
        <input type="email" name="email"
               value="{{ old('email') }}"
               placeholder="admin@church.com"
               required autofocus autocomplete="username">

        <label>Password</label>
        <input type="password" name="password"
               placeholder="••••••••"
               required autocomplete="current-password">

        <button type="submit" class="btn">Sign in</button>

    </form>
</div>
</body>
</html>
