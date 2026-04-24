<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — CCS Profiling System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #0f172a; min-height: 100vh; display: flex; }
        .glow { position: absolute; border-radius: 50%; filter: blur(80px); opacity: .3; pointer-events: none; }
        .form-input {
            width: 100%; background: rgba(255,255,255,.06); border: 1.5px solid rgba(255,255,255,.1);
            color: #f1f5f9; font-size: 14px; border-radius: 12px; padding: 13px 16px;
            outline: none; transition: all .2s; font-family: inherit;
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus { border-color: #f97316; background: rgba(249,115,22,.08); box-shadow: 0 0 0 4px rgba(249,115,22,.15); }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
        .form-error { font-size: 12px; color: #f87171; font-weight: 600; margin-top: 5px; }
        .btn-submit {
            width: 100%; padding: 14px; border-radius: 12px; font-size: 15px; font-weight: 800;
            background: linear-gradient(135deg, #f97316, #fb923c); color: #fff; border: none; cursor: pointer;
            transition: all .2s; box-shadow: 0 8px 24px rgba(249,115,22,.4);
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(249,115,22,.5); }
        .role-card {
            padding: 14px 12px; border-radius: 14px; border: 2px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.04); cursor: pointer; transition: all .2s; text-align: center;
        }
        .role-card:hover { border-color: rgba(249,115,22,.4); background: rgba(249,115,22,.06); }
        .role-card input[type=radio] { display: none; }
        .role-card input[type=radio]:checked ~ * { color: #f97316; }
        .role-card.selected { border-color: #f97316; background: rgba(249,115,22,.1); box-shadow: 0 0 0 4px rgba(249,115,22,.15); }
        select.form-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 40px; }
        select.form-input option { background: #1e293b; color: #f1f5f9; }
        .step-indicator { display: flex; align-items: center; gap: 8px; margin-bottom: 28px; }
        .step { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; flex-shrink: 0; }
        .step.active { background: #f97316; color: #fff; box-shadow: 0 4px 12px rgba(249,115,22,.4); }
        .step.done { background: #22c55e; color: #fff; }
        .step.inactive { background: rgba(255,255,255,.08); color: #475569; }
        .step-line { flex: 1; height: 2px; background: rgba(255,255,255,.08); border-radius: 2px; }
        .step-line.done { background: #22c55e; }
    </style>
</head>
<body>
    <div class="glow" style="width:600px;height:600px;background:#8b5cf6;top:-200px;right:-200px;"></div>
    <div class="glow" style="width:500px;height:500px;background:#f97316;bottom:-200px;left:-100px;"></div>
    <div class="glow" style="width:300px;height:300px;background:#3b82f6;top:30%;left:30%;"></div>

    <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:24px;position:relative;z-index:1;">
        <div style="width:100%;max-width:520px;">

            {{-- Logo --}}
            <div style="text-align:center;margin-bottom:32px;">
                <img src="{{ asset('images/pnc-logo.jpg') }}" alt="PNC Logo"
                     style="width:72px;height:72px;border-radius:18px;object-fit:cover;margin:0 auto 14px;display:block;box-shadow:0 8px 32px rgba(249,115,22,.4);border:3px solid rgba(249,115,22,.3);">
                <h1 style="font-size:26px;font-weight:900;color:#fff;margin:0 0 4px;">Create Your Account</h1>
                <p style="color:#64748b;font-size:13px;font-weight:500;margin:0;">Join the CCS Profiling System</p>
            </div>

            {{-- Card --}}
            <div style="background:rgba(255,255,255,.04);backdrop-filter:blur(20px);border:1.5px solid rgba(255,255,255,.08);border-radius:24px;padding:36px;box-shadow:0 24px 64px rgba(0,0,0,.4);">

                @if ($errors->any())
                <div style="background:rgba(239,68,68,.1);border:1.5px solid rgba(239,68,68,.3);color:#f87171;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:20px;">
                    @foreach ($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Role Selection --}}
                    <div style="margin-bottom:24px;">
                        <label class="form-label" style="margin-bottom:12px;">I am a...</label>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;" id="roleCards">
                            @foreach([['Admin','🛡️','Full system access'],['Student','🎓','Student portal'],['Faculty','👨‍🏫','Faculty portal']] as [$role,$icon,$desc])
                            <label class="role-card {{ old('role')===$role?'selected':($role==='Student'&&!old('role')?'selected':'') }}"
                                   id="roleCard{{ $role }}" onclick="selectRole('{{ $role }}')">
                                <input type="radio" name="role" value="{{ $role }}" {{ old('role')===$role?'checked':($role==='Student'&&!old('role')?'checked':'') }}>
                                <div style="font-size:24px;margin-bottom:6px;">{{ $icon }}</div>
                                <div style="font-size:13px;font-weight:800;color:#f1f5f9;">{{ $role }}</div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $desc }}</div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Name & Email --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                        <div>
                            <label class="form-label" for="name">Full Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="Juan Dela Cruz" required>
                            @error('name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="email">Email Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="juan@pnc.edu.ph" required>
                            @error('email')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                        <div>
                            <label class="form-label" for="password">Password</label>
                            <div style="position:relative;">
                                <input id="password" type="password" name="password" class="form-input" placeholder="Min. 8 characters" required style="padding-right:44px;">
                                <button type="button" onclick="togglePass('password')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#475569;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            @error('password')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <div style="position:relative;">
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" required style="padding-right:44px;">
                                <button type="button" onclick="togglePass('password_confirmation')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#475569;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Terms --}}
                    <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:24px;">
                        <input type="checkbox" id="terms" required style="width:16px;height:16px;accent-color:#f97316;cursor:pointer;margin-top:2px;flex-shrink:0;">
                        <label for="terms" style="font-size:13px;color:#64748b;font-weight:500;cursor:pointer;line-height:1.5;">
                            I agree to the <span style="color:#f97316;font-weight:700;">Terms of Service</span> and <span style="color:#f97316;font-weight:700;">Privacy Policy</span> of Pamantasan ng Cabuyao.
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">Create Account →</button>
                </form>

                <div style="display:flex;align-items:center;gap:12px;margin:20px 0;">
                    <div style="flex:1;height:1px;background:rgba(255,255,255,.08);"></div>
                    <span style="font-size:12px;color:#475569;font-weight:600;">Already have an account?</span>
                    <div style="flex:1;height:1px;background:rgba(255,255,255,.08);"></div>
                </div>

                <a href="{{ route('login') }}"
                   style="display:block;width:100%;padding:13px;border-radius:12px;border:1.5px solid rgba(255,255,255,.1);color:#94a3b8;font-size:14px;font-weight:700;text-align:center;text-decoration:none;transition:all .2s;background:rgba(255,255,255,.03);"
                   onmouseover="this.style.borderColor='#f97316';this.style.color='#f97316';"
                   onmouseout="this.style.borderColor='rgba(255,255,255,.1)';this.style.color='#94a3b8';">
                    Sign In Instead
                </a>
            </div>

            <p style="text-align:center;font-size:12px;color:#334155;margin-top:20px;font-weight:500;">
                © {{ date('Y') }} Pamantasan ng Cabuyao · CCS Comprehensive Profiling System
            </p>
        </div>
    </div>

    <script>
    function selectRole(role) {
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
        const card = document.getElementById('roleCard' + role);
        if (card) {
            card.classList.add('selected');
            card.querySelector('input[type=radio]').checked = true;
        }
    }
    function togglePass(id) {
        const el = document.getElementById(id);
        el.type = el.type === 'password' ? 'text' : 'password';
    }
    // Init default selection
    document.addEventListener('DOMContentLoaded', () => {
        const checked = document.querySelector('input[name=role]:checked');
        if (checked) {
            const label = checked.closest('.role-card');
            if (label) label.classList.add('selected');
        }
    });
    </script>
</body>
</html>
