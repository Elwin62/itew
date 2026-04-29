<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — CCS Profiling System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #0f172a; min-height: 100vh; display: flex; }
        .glow { position: absolute; border-radius: 50%; filter: blur(80px); opacity: .35; pointer-events: none; }
        .form-input {
            width: 100%; background: rgba(255,255,255,.06); border: 1.5px solid rgba(255,255,255,.1);
            color: #f1f5f9; font-size: 14px; border-radius: 12px; padding: 13px 16px;
            outline: none; transition: all .2s; font-family: inherit;
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus { border-color: #f97316; background: rgba(249,115,22,.08); box-shadow: 0 0 0 4px rgba(249,115,22,.15); }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
        .btn-submit {
            width: 100%; padding: 14px; border-radius: 12px; font-size: 15px; font-weight: 800;
            background: linear-gradient(135deg, #f97316, #fb923c); color: #fff; border: none; cursor: pointer;
            transition: all .2s; box-shadow: 0 8px 24px rgba(249,115,22,.4); letter-spacing: .01em;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(249,115,22,.5); }
        .btn-submit:active { transform: translateY(0); }
        .error-msg { background: rgba(239,68,68,.1); border: 1.5px solid rgba(239,68,68,.3); color: #f87171; padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
        .divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.08); }
        .divider span { font-size: 12px; color: #475569; font-weight: 600; white-space: nowrap; }
        select.form-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 40px; }
        select.form-input option { background: #1e293b; color: #f1f5f9; }
    </style>
</head>
<body>
    {{-- Background glows --}}
    <div class="glow" style="width:600px;height:600px;background:#f97316;top:-200px;left:-200px;"></div>
    <div class="glow" style="width:500px;height:500px;background:#3b82f6;bottom:-200px;right:-100px;"></div>
    <div class="glow" style="width:300px;height:300px;background:#8b5cf6;top:40%;left:40%;"></div>

    <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:24px;position:relative;z-index:1;">
        <div style="width:100%;max-width:440px;">

            {{-- Logo --}}
            <div style="text-align:center;margin-bottom:40px;">
                <img src="{{ asset('images/pnc-logo.jpg') }}" alt="PNC Logo"
                     style="width:80px;height:80px;border-radius:20px;object-fit:cover;margin:0 auto 16px;display:block;box-shadow:0 8px 32px rgba(249,115,22,.4);border:3px solid rgba(249,115,22,.3);">
                <h1 style="font-size:28px;font-weight:900;color:#fff;margin:0 0 6px;">CCS Profiling System</h1>
                <p style="color:#64748b;font-size:14px;font-weight:500;margin:0;">Pamantasan ng Cabuyao — College of Computing</p>
            </div>

            {{-- Card --}}
            <div style="background:rgba(255,255,255,.04);backdrop-filter:blur(20px);border:1.5px solid rgba(255,255,255,.08);border-radius:24px;padding:36px;box-shadow:0 24px 64px rgba(0,0,0,.4);">
                <h2 style="font-size:22px;font-weight:900;color:#f1f5f9;margin:0 0 4px;"></h2>
                <p style="font-size:13px;color:#64748b;font-weight:500;margin:0 0 28px;">Sign in to access your account</p>

                @if ($errors->any())
                <div class="error-msg">
                    @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                    @endforeach
                </div>
                @endif

                {{-- Quick login hints --}}
                <div style="background:rgba(249,115,22,.08);border:1.5px solid rgba(249,115,22,.2);border-radius:12px;padding:14px 16px;margin-bottom:24px;">
                    <p style="font-size:11px;font-weight:700;color:#f97316;text-transform:uppercase;letter-spacing:.08em;margin:0 0 8px;"></p>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;">
                        @foreach([['Admin','admin@pnc.edu.ph'],['Student','student@pnc.edu.ph'],['Faculty','faculty@pnc.edu.ph']] as [$role,$email])
                        <button type="button" onclick="document.getElementById('email').value='{{$email}}';document.getElementById('password').value='password123';"
                            style="padding:6px 8px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#94a3b8;font-size:11px;font-weight:700;cursor:pointer;transition:all .15s;"
                            onmouseover="this.style.background='rgba(249,115,22,.15)';this.style.color='#fb923c'"
                            onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.color='#94a3b8'">
                            {{ $role }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div style="margin-bottom:18px;">
                        <label class="form-label" for="email">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="you@pnc.edu.ph" required autofocus>
                    </div>
                    <div style="margin-bottom:24px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                            <label class="form-label" for="password" style="margin:0;">Password</label>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="font-size:12px;color:#f97316;font-weight:600;text-decoration:none;">Forgot password?</a>
                            @endif
                        </div>
                        <div style="position:relative;">
                            <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required style="padding-right:44px;">
                            <button type="button" onclick="var p=document.getElementById('password');p.type=p.type==='password'?'text':'password';"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#475569;padding:4px;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;">
                        <input type="checkbox" name="remember" id="remember" style="width:16px;height:16px;accent-color:#f97316;cursor:pointer;">
                        <label for="remember" style="font-size:13px;color:#64748b;font-weight:500;cursor:pointer;">Keep me signed in</label>
                    </div>
                    <button type="submit" class="btn-submit">Sign In →</button>
                </form>

                <div class="divider"><span>New to the system?</span></div>

                <a href="{{ route('register') }}"
                   style="display:block;width:100%;padding:13px;border-radius:12px;border:1.5px solid rgba(255,255,255,.1);color:#94a3b8;font-size:14px;font-weight:700;text-align:center;text-decoration:none;transition:all .2s;background:rgba(255,255,255,.03);"
                   onmouseover="this.style.borderColor='#f97316';this.style.color='#f97316';"
                   onmouseout="this.style.borderColor='rgba(255,255,255,.1)';this.style.color='#94a3b8';">
                    Create an Account
                </a>
            </div>

            <p style="text-align:center;font-size:12px;color:#334155;margin-top:24px;font-weight:500;">
                © {{ date('Y') }} Pamantasan ng Cabuyao · CCS Comprehensive Profiling System
            </p>
        </div>
    </div>
</body>
</html>
