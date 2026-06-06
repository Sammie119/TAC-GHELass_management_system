<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give — {{ config('app.name') }}</title>
    {{-- Favicons --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon.svg">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 60%, #7c3aed 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: white; border-radius: 24px;
            width: 100%; max-width: 460px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg,#2563eb,#4f46e5);
            padding: 24px; text-align: center; color: white;
        }
        .header-icon {
            width: 56px; height: 56px;
            background: rgba(255,255,255,0.2);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
        }
        .card-body { padding: 24px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px;
            padding: 12px 14px; font-size: 15px; outline: none;
            color: #111827; transition: border-color 0.15s;
            margin-bottom: 14px;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }
        input::placeholder { color: #9ca3af; }

        .admin-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 13px;
            color: #9ca3af;
        }
        .admin-link a {
            color: #2563eb;
            text-decoration: none;
        }

        .btn {
            width: 100%; background: linear-gradient(135deg,#2563eb,#4f46e5);
            color: white; border: none; border-radius: 12px;
            padding: 14px; font-size: 16px; font-weight: 700;
            cursor: pointer; margin-top: 4px;
            transition: opacity 0.15s;
        }
        .btn:hover { opacity: 0.92; }

        .back-link {
            display: block; text-align: center;
            margin-top: 14px; font-size: 13px;
            color: rgba(255,255,255,0.7); text-decoration: none;
        }
        .back-link:hover { color: white; }

        .error {
            background: #fef2f2; border: 1px solid #fecaca;
            color: #dc2626; padding: 10px 14px; border-radius: 8px;
            font-size: 13px; margin-bottom: 14px;
        }

        .divider {
            display: flex; align-items: center; gap: 10px;
            margin: 16px 0; color: #9ca3af; font-size: 12px;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #e5e7eb;
        }

        .momo-info {
            background: #fef3c7; border: 1px solid #fde68a;
            border-radius: 10px; padding: 12px 14px;
            font-size: 12px; color: #92400e; margin-bottom: 16px;
            display: flex; gap: 8px; align-items: flex-start;
        }
    </style>
</head>
<body>

<div style="width:100%;max-width:460px;">
    <div class="card">

        {{-- Header --}}
        <div class="card-header">
            <div class="header-icon">
                <svg style="width:28px;height:28px;" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h1 style="font-size:20px;font-weight:800;margin-bottom:4px;">Give to {{ config('app.name') }}</h1>
            <p style="font-size:13px;opacity:0.85;">Your giving supports the work of God's kingdom</p>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('give.submit') }}">
                @csrf

                {{-- Personal details --}}
                <label>Your full name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="full_name"
                       value="{{ old('full_name') }}"
                       placeholder="e.g. John Mensah" required>

{{--                <div class="amount-grid" style="margin-bottom:0;">--}}
                    <div>
                        <label>Phone <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="phone"
                               value="{{ old('phone') }}"
                               placeholder="0244000001" required>
                    </div>
                    <div>
                        <label>Email <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="you@example.com" required>
                    </div>
{{--                </div>--}}

                <div class="divider">Payment details</div>

                {{-- Category --}}
                <label>Type of giving <span style="color:#ef4444;">*</span></label>
                <select name="category">
                    @foreach(config('finance.online_categories') as $key => $label)
                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <input type="number" name="amount" id="amount-input"
                       value="{{ old('amount') }}"
                       placeholder="Enter amount" step="0.01" min="1" required>

                {{-- Notes --}}
                <label>Message <span style="font-weight:400;color:#9ca3af;">(optional)</span></label>
                <textarea name="notes" rows="2"
                          placeholder="e.g. For Project fund...."
                          style="resize:none;">{{ old('notes') }}</textarea>

                <button type="submit" class="btn">
                    🙏 Submit
                </button>

            </form>

            <div class="admin-link">
                ← Go Back to <a href="{{ url('/') }}">Welcome Page</a>
            </div>
        </div>
    </div>

</div>

</body>
</html>
