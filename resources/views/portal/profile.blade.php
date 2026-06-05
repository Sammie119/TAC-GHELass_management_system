<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — Member Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f9ff; min-height: 100vh; }
        .nav {
            background: white; border-bottom: 1px solid #e5e7eb;
            padding: 0 1rem; height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        .nav-back { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #374151; font-size: 14px; font-weight: 500; }
        .nav-title { font-size: 15px; font-weight: 600; color: #111827; }
        .content { max-width: 640px; margin: 0 auto; padding: 1.5rem 1rem; }
        .profile-card {
            background: white; border-radius: 14px; border: 1px solid #e5e7eb;
            padding: 1.5rem; text-align: center; margin-bottom: 1.25rem;
        }
        .avatar {
            width: 72px; height: 72px; border-radius: 50%;
            background: #2563eb;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; font-weight: 700; color: white;
            margin: 0 auto 12px;
        }
        .name   { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .id     { font-size: 13px; color: #2563eb; font-family: monospace; font-weight: 600; margin-bottom: 8px; }
        .status {
            display: inline-block; padding: 4px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 500; background: #dcfce7; color: #15803d;
        }
        .section-card {
            background: white; border-radius: 14px; border: 1px solid #e5e7eb;
            overflow: hidden; margin-bottom: 1.25rem;
        }
        .section-header {
            padding: 14px 16px; border-bottom: 1px solid #f3f4f6;
        }
        .section-title { font-size: 14px; font-weight: 600; color: #111827; }
        .info-row {
            padding: 12px 16px; display: flex; justify-content: space-between;
            border-bottom: 1px solid #f9fafb; font-size: 14px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #9ca3af; font-size: 13px; }
        .info-value { color: #111827; font-weight: 500; }
        label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        input, textarea {
            width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px;
            padding: 11px 14px; font-size: 14px; outline: none; color: #111827;
            transition: border-color 0.15s; margin-bottom: 14px;
        }
        input:focus, textarea:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px #bfdbfe; }
        .btn {
            width: 100%; background: #2563eb; color: white; border: none;
            border-radius: 10px; padding: 12px; font-size: 15px; font-weight: 600;
            cursor: pointer;
        }
        .success {
            background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;
            padding: 10px 14px; border-radius: 10px; font-size: 13px;
            margin-bottom: 16px;
        }
        .form-section { padding: 16px; }
    </style>
</head>
<body>

<nav class="nav">
    <a href="{{ route('portal.dashboard') }}" class="nav-back">
        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back
    </a>
    <span class="nav-title">My Profile</span>
    <span style="width:60px;"></span>
</nav>

<div class="content">

    {{-- Profile card --}}
    <div class="profile-card">
        <div class="avatar">
            {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
        </div>
        <div class="name">{{ $member->full_name }}</div>
        <div class="id">{{ $member->member_id_card }}</div>
        <span class="status">{{ ucfirst($member->status) }} Member</span>
    </div>

    {{-- Read-only details --}}
    <div class="section-card">
        <div class="section-header">
            <span class="section-title">Member details</span>
        </div>
        <div class="info-row">
            <span class="info-label">Gender</span>
            <span class="info-value">{{ ucfirst($member->gender ?? '—') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date of birth</span>
            <span class="info-value">
                {{ $member->date_of_birth
                    ? \Carbon\Carbon::parse($member->date_of_birth)->format('d M Y')
                    : '—' }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">TACMS Number</span>
            <span class="info-value">{{ ucfirst($member->tacms_number ?? '—') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Department</span>
            <span class="info-value">{{ ucfirst($member->department ?? '—') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Member since</span>
            <span class="info-value">{{ $member->created_at->format('d M Y') }}</span>
        </div>
    </div>

    {{-- Editable contact details --}}
    <div class="section-card">
        <div class="section-header">
            <span class="section-title">Update contact details</span>
        </div>
        <div class="form-section">

            @if(session('success'))
                <div class="success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('portal.profile.update') }}">
                @csrf

                <label>Phone number</label>
                <input type="text" name="phone"
                       value="{{ old('phone', $member->phone) }}"
                       placeholder="e.g. 0244000001">

                <label>Email address</label>
                <input type="email" name="email"
                       value="{{ old('email', $member->email) }}"
                       placeholder="e.g. john@example.com">

                <label>Home address</label>
                <textarea name="address" rows="2"
                          placeholder="e.g. 12 Church Street, Accra"
                          style="resize:none;">{{ old('address', $member->address) }}</textarea>

                <label>OTP</label>
                <input type="number" name="otp"
                       placeholder="e.g. 123409">

                <button type="submit" class="btn">Save changes</button>
            </form>
        </div>
    </div>

    {{-- QR download --}}
{{--    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:1.25rem;text-align:center;margin-bottom:1.25rem;">--}}
{{--        <p style="font-size:14px;font-weight:600;color:#15803d;margin-bottom:6px;">Your QR Code</p>--}}
{{--        <p style="font-size:13px;color:#6b7280;margin-bottom:1rem;">--}}
{{--            Download and save this to your phone for fast check-in--}}
{{--        </p>--}}
{{--        <a href="{{ route('portal.qr-download') }}"--}}
{{--           style="display:inline-flex;align-items:center;gap:8px;background:#16a34a;color:white;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;">--}}
{{--            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
{{--                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>--}}
{{--            </svg>--}}
{{--            Download QR Code--}}
{{--        </a>--}}
{{--    </div>--}}

</div>
</body>
</html>
