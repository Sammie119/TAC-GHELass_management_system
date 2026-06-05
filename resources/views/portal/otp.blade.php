<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Verify OTP — Member Portal</title>
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
        .avatar {
            width: 64px; height: 64px; border-radius: 50%;
            background: #dbeafe;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 700; color: #2563eb;
            margin: 0 auto 1rem;
        }
        h1 { font-size: 20px; font-weight: 700; color: #111827; text-align: center; margin-bottom: 4px; }
        .subtitle { font-size: 14px; color: #6b7280; text-align: center; margin-bottom: 1.5rem; line-height: 1.6; }

        /* Dev mode OTP display */
        .otp-dev {
            background: #f0fdf4; border: 1.5px dashed #86efac;
            border-radius: 12px; padding: 14px; text-align: center;
            margin-bottom: 1.5rem;
        }
        .otp-dev p { font-size: 12px; color: #6b7280; margin-bottom: 6px; }
        .otp-code { font-size: 32px; font-weight: 800; color: #16a34a; letter-spacing: 8px; }

        .otp-inputs {
            display: flex; gap: 8px; justify-content: center; margin-bottom: 1.5rem;
        }
        .otp-input {
            width: 48px; height: 56px;
            border: 1.5px solid #e5e7eb; border-radius: 12px;
            font-size: 22px; font-weight: 700; text-align: center;
            outline: none; transition: border-color 0.15s, box-shadow 0.15s;
            color: #111827;
        }
        .otp-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px #bfdbfe; }

        .btn {
            width: 100%; background: #2563eb; color: white; border: none;
            border-radius: 12px; padding: 14px; font-size: 16px; font-weight: 600;
            cursor: pointer;
        }
        .back-link {
            display: block; text-align: center; margin-top: 1rem;
            font-size: 13px; color: #9ca3af; text-decoration: none;
        }
        .error {
            background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
            padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 16px;
        }
    </style>
</head>
<body>
<div class="card">

    <div class="avatar">
        {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
    </div>

    <h1>Hi, {{ $member->first_name }}!</h1>
    <p class="subtitle">Enter the 6-digit verification code to access your portal.</p>

    {{-- DEV MODE: Show OTP on screen --}}
    @if(is_null($member->otp))
        <div class="otp-dev">
            <p>🔐 Your OTP is:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p style="margin-top:6px;font-size:11px;color:#9ca3af;">Change OTP after a successful login</p>
        </div>
    @endif

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('portal.verify-otp') }}" id="otp-form">
        @csrf
        <input type="hidden" name="otp" id="otp-hidden">

        <div class="otp-inputs">
            @for($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" class="otp-input"
                       inputmode="numeric" pattern="[0-9]"
                       autocomplete="off">
            @endfor
        </div>

        <button type="submit" class="btn">Verify & Sign In</button>
    </form>

    <a href="{{ route('portal.login') }}" class="back-link">← Use a different ID</a>
</div>

<script>
    const inputs = document.querySelectorAll('.otp-input');

    inputs.forEach((input, i) => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
            if (this.value && i < inputs.length - 1) {
                inputs[i + 1].focus();
            }
            updateHidden();
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && i > 0) {
                inputs[i - 1].focus();
            }
        });

        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').slice(0, 6);
            paste.split('').forEach((char, j) => {
                if (inputs[j]) inputs[j].value = char;
            });
            if (inputs[paste.length - 1]) inputs[paste.length - 1].focus();
            updateHidden();
        });
    });

    function updateHidden() {
        document.getElementById('otp-hidden').value =
            Array.from(inputs).map(i => i.value).join('');
    }

    // Auto-submit when all 6 digits entered
    inputs[inputs.length - 1].addEventListener('input', function () {
        if (this.value) {
            updateHidden();
            document.getElementById('otp-form').submit();
        }
    });

    // Focus first input
    inputs[0].focus();
</script>
</body>
</html>
