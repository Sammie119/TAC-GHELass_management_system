@extends('layouts.admin')
@section('page-title', 'My Profile')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">My Profile</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Manage your account details and password</p>
        </div>
    </div>

    {{-- Success messages --}}
{{--    @if(session('success'))--}}
{{--        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;display:flex;justify-content:space-between;align-items:center;">--}}
{{--            {{ session('success') }}--}}
{{--            <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#15803d;cursor:pointer;">✕</button>--}}
{{--        </div>--}}
{{--    @endif--}}

    @if(session('password_success'))
        <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;display:flex;justify-content:space-between;align-items:center;">
            {{ session('password_success') }}
            <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#1d4ed8;cursor:pointer;">✕</button>
        </div>
    @endif

    <div style="display:grid;gap:24px;">
        <style>@media(min-width:1024px){.profile-grid{grid-template-columns:300px 1fr !important;}}</style>
        <div style="display:grid;gap:24px;" class="profile-grid">

            {{-- Left: Photo + info card --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Photo card --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;text-align:center;">

                    {{-- Avatar --}}
                    <div style="position:relative;display:inline-block;margin-bottom:16px;">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ Storage::url(auth()->user()->profile_photo) }}"
                                 id="photo-preview"
                                 style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;">
                        @else
                            <div id="photo-preview-initials"
                                 style="width:88px;height:88px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;color:white;font-size:28px;font-weight:700;margin:0 auto;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        @endif

                        {{-- Camera overlay --}}
                        <label for="photo-input"
                               style="position:absolute;bottom:0;right:0;width:28px;height:28px;background:#2563eb;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid white;">
                            <svg style="width:14px;height:14px;" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </label>
                    </div>

                    <p style="font-size:16px;font-weight:700;color:#111827;margin-bottom:4px;">{{ auth()->user()->name }}</p>
                    <p style="font-size:13px;color:#9ca3af;margin-bottom:8px;">{{ auth()->user()->email }}</p>

                    {{-- Role badge --}}
                    <span style="display:inline-block;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;
                {{ $stats['role'] === 'Admin'      ? 'background:#dbeafe;color:#1d4ed8;' : '' }}
                {{ $stats['role'] === 'Finance'    ? 'background:#dcfce7;color:#15803d;' : '' }}
                {{ $stats['role'] === 'Usher'      ? 'background:#fef3c7;color:#d97706;' : '' }}
                {{ $stats['role'] === 'Membership' ? 'background:#ede9fe;color:#7c3aed;' : '' }}
                {{ !in_array($stats['role'], ['Admin','Finance','Usher','Membership']) ? 'background:#f3f4f6;color:#6b7280;' : '' }}">
                {{ $stats['role'] }}
            </span>

                    {{-- Photo upload form (hidden, triggered by camera icon) --}}
                    <form method="POST" action="{{ route('admin.profile.photo') }}"
                          enctype="multipart/form-data" id="photo-form">
                        @csrf
                        <input type="file" id="photo-input" name="photo"
                               accept="image/*" style="display:none;"
                               onchange="previewAndUpload(this)">
                    </form>

                    @if($errors->has('photo'))
                        <p style="font-size:12px;color:#dc2626;margin-top:8px;">{{ $errors->first('photo') }}</p>
                    @endif
                </div>

                {{-- Account info card --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                    <h3 style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:14px;">Account info</h3>
                    <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;">
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Member since</span>
                            <span style="font-weight:500;color:#111827;">{{ $stats['joined'] }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Last login</span>
                            <span style="font-weight:500;color:#111827;">{{ $stats['last_login'] }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Role</span>
                            <span style="font-weight:500;color:#111827;">{{ $stats['role'] }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right: Edit forms --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Update profile details --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;">
                    <h3 style="font-size:15px;font-weight:600;color:#111827;margin-bottom:20px;">
                        Personal details
                    </h3>

                    @if($errors->has('name') || $errors->has('email'))
                        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                            {{ $errors->first('name') ?? $errors->first('email') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf

                        <div style="margin-bottom:16px;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                Full name <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="name"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 14px;font-size:15px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px #bfdbfe'"
                                   onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                   required>
                        </div>

                        <div style="margin-bottom:20px;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                Email address <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="email" name="email"
                                   value="{{ old('email', auth()->user()->email) }}"
                                   style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 14px;font-size:15px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px #bfdbfe'"
                                   onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                   required>
                        </div>

                        <button type="submit"
                                style="background:#2563eb;color:white;padding:11px 24px;border-radius:10px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                            Save changes
                        </button>
                    </form>
                </div>

                {{-- Change password --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;">
                    <h3 style="font-size:15px;font-weight:600;color:#111827;margin-bottom:6px;">Change password</h3>
                    <p style="font-size:13px;color:#9ca3af;margin-bottom:20px;">
                        Use a strong password with at least 8 characters.
                    </p>

                    @if($errors->has('current_password') || $errors->has('password'))
                        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                            {{ $errors->first('current_password') ?? $errors->first('password') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.profile.password') }}">
                        @csrf

                        <div style="margin-bottom:14px;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                Current password <span style="color:#ef4444;">*</span>
                            </label>
                            <div style="position:relative;">
                                <input type="password" name="current_password"
                                       id="current-pwd"
                                       placeholder="Enter your current password"
                                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 44px 11px 14px;font-size:15px;outline:none;box-sizing:border-box;"
                                       onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px #bfdbfe'"
                                       onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                       required>
                                <button type="button" onclick="togglePwd('current-pwd', this)"
                                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
                                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                            <div>
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                    New password <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="position:relative;">
                                    <input type="password" name="password"
                                           id="new-pwd"
                                           placeholder="Min. 8 characters"
                                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 44px 11px 14px;font-size:15px;outline:none;box-sizing:border-box;"
                                           onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px #bfdbfe'"
                                           onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                           oninput="checkStrength(this.value)"
                                           required>
                                    <button type="button" onclick="togglePwd('new-pwd', this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
                                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                {{-- Strength bar --}}
                                <div style="margin-top:6px;height:4px;background:#e5e7eb;border-radius:2px;overflow:hidden;">
                                    <div id="strength-bar"
                                         style="height:100%;width:0%;border-radius:2px;transition:width 0.3s,background 0.3s;"></div>
                                </div>
                                <p id="strength-text" style="font-size:11px;color:#9ca3af;margin-top:3px;"></p>
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                    Confirm new password <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="position:relative;">
                                    <input type="password" name="password_confirmation"
                                           id="confirm-pwd"
                                           placeholder="Repeat new password"
                                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 44px 11px 14px;font-size:15px;outline:none;box-sizing:border-box;"
                                           onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px #bfdbfe'"
                                           onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                           oninput="checkMatch()"
                                           required>
                                    <button type="button" onclick="togglePwd('confirm-pwd', this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
                                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <p id="match-text" style="font-size:11px;margin-top:3px;"></p>
                            </div>
                        </div>

                        <button type="submit"
                                style="background:#7c3aed;color:white;padding:11px 24px;border-radius:10px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                            Change password
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        // ── Show/hide password ─────────────────────────────────
        function togglePwd(inputId, btn) {
            const input = document.getElementById(inputId);
            input.type  = input.type === 'password' ? 'text' : 'password';
            btn.style.color = input.type === 'text' ? '#2563eb' : '#9ca3af';
        }

        // ── Password strength ──────────────────────────────────
        function checkStrength(val) {
            const bar  = document.getElementById('strength-bar');
            const text = document.getElementById('strength-text');
            let strength = 0;

            if (val.length >= 8)                      strength++;
            if (/[A-Z]/.test(val))                    strength++;
            if (/[0-9]/.test(val))                    strength++;
            if (/[^A-Za-z0-9]/.test(val))            strength++;

            const configs = [
                { width: '0%',   color: '#e5e7eb', label: '' },
                { width: '25%',  color: '#ef4444', label: 'Weak' },
                { width: '50%',  color: '#f97316', label: 'Fair' },
                { width: '75%',  color: '#eab308', label: 'Good' },
                { width: '100%', color: '#22c55e', label: 'Strong' },
            ];

            const cfg       = configs[strength] || configs[0];
            bar.style.width      = cfg.width;
            bar.style.background = cfg.color;
            text.textContent     = cfg.label;
            text.style.color     = cfg.color;
        }

        // ── Password match ─────────────────────────────────────
        function checkMatch() {
            const newPwd     = document.getElementById('new-pwd').value;
            const confirmPwd = document.getElementById('confirm-pwd').value;
            const text       = document.getElementById('match-text');

            if (!confirmPwd) { text.textContent = ''; return; }

            if (newPwd === confirmPwd) {
                text.textContent = '✓ Passwords match';
                text.style.color = '#16a34a';
            } else {
                text.textContent = '✗ Passwords do not match';
                text.style.color = '#dc2626';
            }
        }

        // ── Photo preview & auto-submit ────────────────────────
        function previewAndUpload(input) {
            if (!input.files || !input.files[0]) return;

            const file   = input.files[0];
            const reader = new FileReader();

            reader.onload = e => {
                // Replace preview
                const container = input.closest('form').previousElementSibling;
                const existing  = document.getElementById('photo-preview');
                const initials  = document.getElementById('photo-preview-initials');

                if (existing) {
                    existing.src = e.target.result;
                } else if (initials) {
                    const img = document.createElement('img');
                    img.src   = e.target.result;
                    img.id    = 'photo-preview';
                    img.style.cssText = 'width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;';
                    initials.replaceWith(img);
                }
            };

            reader.readAsDataURL(file);

            // Auto submit the photo form
            document.getElementById('photo-form').submit();
        }
    </script>

@endsection
