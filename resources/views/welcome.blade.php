<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Church Management System') }}</title>

    {{-- Favicons --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon.svg">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #7c3aed 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
        }
        .wrapper { width: 100%; max-width: 420px; text-align: center; }

        /* Logo */
        .logo {
            width: 72px; height: 72px; background: white; border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }

        /* Title */
        h1 {
            font-size: 28px; font-weight: 800; color: white;
            margin-bottom: 8px; letter-spacing: -0.5px;
        }
        .tagline {
            font-size: 15px; color: rgba(255,255,255,0.75);
            margin-bottom: 2.5rem; line-height: 1.6;
        }

        /* Cards */
        .cards { display: flex; flex-direction: column; gap: 14px; }

        .portal-card {
            background: white; border-radius: 18px; padding: 1.5rem;
            text-align: left; text-decoration: none; color: inherit;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transition: transform 0.15s, box-shadow 0.15s;
            display: block;
        }

        .portal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        }

        .card-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 12px;
        }

        .card-title { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .card-desc  { font-size: 13px; color: #6b7280; line-height: 1.5; }

        .card-arrow {
            float: right; margin-top: -36px;
            font-size: 20px; color: #d1d5db;
        }

        /* Footer */
        .footer {
            margin-top: 2rem;
            font-size: 12px; color: rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Logo --}}
    <div class="logo">
        <svg style="width:38px;height:38px;" fill="none" stroke="#2563eb" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </div>

    <h1>{{ config('app.name', 'Church Management System') }}</h1>
    <p class="tagline">Stay connected with your church.<br>
        The Apostolic Church-Ghana East Legon Assembly

    </p>

    <div class="cards">

        {{-- Member portal --}}
        <a href="{{ route('portal.login') }}" class="portal-card">
            <div class="card-title">I'm a Member</div>
            <div class="card-desc">
                Login as a Member.
            </div>
            <div class="card-arrow">
                <svg style="width:18px;height:18px;color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        {{-- Admin login --}}
        <a href="{{ route('login') }}" class="portal-card"
           style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:14px;font-weight:600;color:white;">Administrator Login</div>
                    <div style="font-size:12px;color:rgba(255,255,255,0.6);margin-top:2px;">
                        For Church Worker and Administrators
                    </div>
                </div>
                <svg style="width:18px;height:18px;color:rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        {{-- Give / Offering card --}}
        <a href="{{ route('give.show') }}" class="portal-card">
            <div class="card-icon" style="background:#fef3c7;">
                <svg style="width:22px;height:22px;" fill="none" stroke="#d97706" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div class="card-title">Give Offering</div>
            <div class="card-desc">
                Give your tithe, offering or project securely online.
            </div>
            <div class="card-arrow">
                <svg style="width:18px;height:18px;color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

    </div>

    <div class="footer">
        © {{ now()->year }} {{ config('app.name', 'Church Management System') }}
    </div>

</div>
</body>
</html>
