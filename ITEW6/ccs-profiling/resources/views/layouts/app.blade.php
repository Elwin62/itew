<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CCS Comprehensive Profiling System">
    <title>@yield('title', 'Dashboard') — CCS Profiling System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: '#f97316' },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .sidebar-link { display:flex; align-items:center; gap:12px; padding:10px 16px; border-radius:12px; font-size:14px; font-weight:600; color:#94a3b8; text-decoration:none; transition:all .2s; }
        .sidebar-link:hover { background:rgba(255,255,255,.08); color:#fff; }
        .sidebar-link.active { background:#f97316; color:#fff; box-shadow:0 4px 14px rgba(249,115,22,.35); }
        .card { background:#fff; border-radius:20px; border:1px solid #f1f5f9; box-shadow:0 1px 3px rgba(0,0,0,.04); }
        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
        .badge-green { background:#dcfce7; color:#16a34a; }
        .badge-orange { background:#ffedd5; color:#ea580c; }
        .badge-blue { background:#dbeafe; color:#2563eb; }
        .badge-slate { background:#f1f5f9; color:#64748b; }
        .badge-purple { background:#f3e8ff; color:#9333ea; }
        .badge-red { background:#fee2e2; color:#dc2626; }
        .badge-teal { background:#ccfbf1; color:#0d9488; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:12px; font-size:14px; font-weight:700; cursor:pointer; transition:all .2s; border:none; text-decoration:none; }
        .btn-primary { background:#f97316; color:#fff; box-shadow:0 4px 14px rgba(249,115,22,.3); }
        .btn-primary:hover { background:#ea6c0a; transform:translateY(-1px); }
        .btn-secondary { background:#f8fafc; color:#475569; border:1px solid #e2e8f0; }
        .btn-secondary:hover { background:#f1f5f9; }
        .btn-blue { background:#3b82f6; color:#fff; box-shadow:0 4px 14px rgba(59,130,246,.3); }
        .btn-blue:hover { background:#2563eb; }
        .btn-danger { background:#ef4444; color:#fff; }
        .btn-danger:hover { background:#dc2626; }
        .form-input { width:100%; background:#f8fafc; border:1.5px solid #e2e8f0; color:#1e293b; font-size:14px; border-radius:12px; padding:11px 16px; outline:none; transition:all .2s; }
        .form-input:focus { border-color:#f97316; box-shadow:0 0 0 4px rgba(249,115,22,.12); background:#fff; }
        .form-label { display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.08em; margin-bottom:8px; }
        .table-th { padding:12px 24px; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; background:#f8fafc; border-bottom:1px solid #f1f5f9; }
        .table-td { padding:16px 24px; border-bottom:1px solid #f8fafc; }
        tr:hover td { background:#fafbfc; }
        .stat-card { padding:28px; border-radius:20px; border:1px solid #f1f5f9; background:#fff; }
        .avatar { border-radius:12px; object-fit:cover; }
        .tab-btn { display:flex; align-items:center; gap:8px; padding:8px 16px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; transition:all .15s; color:#64748b; }
        .tab-btn.active { background:#f97316; color:#fff; box-shadow:0 4px 14px rgba(249,115,22,.25); }
        .tab-btn:not(.active):hover { background:#f1f5f9; }
        .progress-bar { height:6px; background:#f1f5f9; border-radius:99px; overflow:hidden; }
        .progress-fill { height:100%; background:linear-gradient(90deg,#f97316,#fb923c); border-radius:99px; transition:width .5s; }
        .profile-banner { height:88px; background:linear-gradient(135deg,#f97316 0%,#fb923c 50%,#fbbf24 100%); }
        .notification-dot { width:8px; height:8px; background:#f97316; border-radius:50%; border:2px solid #fff; }
        select.form-input { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; background-size:16px; padding-right:36px; }
        textarea.form-input { resize:vertical; }
        .empty-state { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:64px 24px; color:#94a3b8; }
        .skeleton { background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:shimmer 1.5s infinite; border-radius:8px; }
        @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
        .overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:40; }
        @media(max-width:1024px){#sidebar{transform:translateX(-100%)}.sidebar-open #sidebar{transform:translateX(0)}}

        /* Print styles */
        @media print {
            #sidebar, header, footer, .no-print { display:none !important; }
            main { padding:0 !important; }
            .card { box-shadow:none !important; border:1px solid #e2e8f0 !important; break-inside:avoid; }
            body { background:#fff !important; }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen" style="color:#1e293b;">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside id="sidebar" style="width:260px;min-width:260px;background:#0f172a;display:flex;flex-direction:column;position:relative;z-index:50;transition:transform .3s;">
        {{-- Logo --}}
        <div style="padding:20px 20px 16px;border-bottom:1px solid rgba(255,255,255,.07);">
            <div style="display:flex;align-items:center;gap:12px;">
                <img src="{{ asset('images/pnc-logo.jpg') }}" alt="PNC Logo"
                     style="width:42px;height:42px;border-radius:12px;object-fit:cover;flex-shrink:0;border:2px solid rgba(249,115,22,.4);box-shadow:0 4px 12px rgba(249,115,22,.3);">
                <div>
                    <div style="font-weight:900;color:#fff;font-size:15px;line-height:1.2;">CCS Profiling</div>
                    <div style="font-size:10px;color:#f97316;font-weight:700;text-transform:uppercase;letter-spacing:.1em;">System v1.0</div>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav style="flex:1;padding:16px 12px;overflow-y:auto;display:flex;flex-direction:column;gap:2px;">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            @if(auth()->user()->role === 'Admin')
            {{-- ADMIN NAVIGATION --}}
            <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Students
            </a>
            <a href="{{ route('faculty.index') }}" class="sidebar-link {{ request()->routeIs('faculty.*') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Faculty
            </a>

            <a href="{{ route('admin.index') }}" class="sidebar-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">⚙️ System Settings</a>

            <div class="mt-6 mb-2 px-3 text-xs font-black text-slate-400 uppercase tracking-widest">Academics</div>
            <a href="{{ route('instruction.index') }}" class="sidebar-link {{ request()->routeIs('instruction.*') ? 'active' : '' }}">📚 Instruction (Curriculum)</a>
            <a href="{{ route('schedules.index') }}" class="sidebar-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}">🗓 Schedules</a>
            <a href="{{ route('events.index') }}" class="sidebar-link {{ request()->routeIs('events.*') ? 'active' : '' }}">📅 Events</a>

            <div class="mt-6 mb-2 px-3 text-xs font-black text-slate-400 uppercase tracking-widest">Queries & Reports</div>
            <a href="{{ route('queries.advanced') }}" class="sidebar-link {{ request()->routeIs('queries.advanced') ? 'active' : '' }}">🔍 Advanced Profiling</a>
            <a href="{{ route('queries.basketball') }}" class="sidebar-link {{ request()->routeIs('queries.basketball') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M4.93 4.93l14.14 14.14M4.93 19.07L19.07 4.93"/></svg>
                Basketball Skills
            </a>
            <a href="{{ route('queries.programming') }}" class="sidebar-link {{ request()->is('queries/programming') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                Programming Skills
            </a>
            <a href="{{ route('reports.admin') }}" class="sidebar-link {{ request()->routeIs('reports.admin') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generate Reports
            </a>


            @elseif(auth()->user()->role === 'Faculty')
            {{-- FACULTY NAVIGATION --}}
            <div style="margin:16px 4px 8px;font-size:10px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:.1em;">My Portal</div>
            <a href="{{ route('faculty.my-profile') }}" class="sidebar-link {{ request()->routeIs('faculty.*profile') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My Profile
            </a>
            <a href="{{ route('schedules.my') }}" class="sidebar-link {{ request()->routeIs('schedules.my') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                My Schedules
            </a>

            <div style="margin:16px 4px 8px;font-size:10px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:.1em;">Reports</div>
            <a href="{{ route('reports.faculty') }}" class="sidebar-link {{ request()->routeIs('reports.faculty') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                My Reports
            </a>

            @elseif(auth()->user()->role === 'Student')
            {{-- STUDENT NAVIGATION --}}
            <div style="margin:16px 4px 8px;font-size:10px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:.1em;">My Portal</div>
            <a href="{{ route('student.my-profile') }}" class="sidebar-link {{ request()->routeIs('student.*profile') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My Profile
            </a>

            <div style="margin:16px 4px 8px;font-size:10px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:.1em;">Reports</div>
            <a href="{{ route('reports.student') }}" class="sidebar-link {{ request()->routeIs('reports.student') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                My Reports
            </a>
            @endif
        </nav>

        {{-- User Footer --}}
        <div style="padding:16px 12px;border-top:1px solid rgba(255,255,255,.07);">
            <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:rgba(255,255,255,.05);">
                <img src="{{ auth()->user()->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=f97316&color=fff&size=200' }}"
                     style="width:36px;height:36px;border-radius:10px;object-fit:cover;flex-shrink:0;" alt="">
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;color:#fff;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <div style="font-size:10px;color:#f97316;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">{{ auth()->user()->role }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:#475569;padding:4px;" title="Logout">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;">

        {{-- Header --}}
        <header style="background:#fff;border-bottom:1px solid #f1f5f9;padding:0 28px;height:64px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div>
                <div style="font-weight:900;font-size:20px;color:#0f172a;">@yield('page-title','Dashboard')</div>
                <div style="font-size:12px;color:#94a3b8;font-weight:500;">@yield('page-subtitle','CCS Comprehensive Profiling System')</div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="font-size:12px;font-weight:700;color:#94a3b8;">{{ now()->format('M d, Y') }}</div>
                <div style="width:40px;height:40px;border-radius:12px;background:#fff7ed;border:2px solid #fed7aa;overflow:hidden;">
                    <img src="{{ auth()->user()->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=f97316&color=fff&size=200' }}"
                         style="width:100%;height:100%;object-fit:cover;" alt="">
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div style="margin:20px 28px 0;padding:14px 18px;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:12px;color:#15803d;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div style="margin:20px 28px 0;padding:14px 18px;background:#fef2f2;border:1.5px solid #fecaca;border-radius:12px;color:#dc2626;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Page content --}}
        <main style="flex:1;overflow-y:auto;padding:28px;">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer style="padding:16px 28px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <p style="font-size:12px;color:#94a3b8;font-weight:600;">© {{ date('Y') }} Pamantasan ng Cabuyao — CCS Comprehensive Profiling System</p>
            <div style="display:flex;gap:16px;font-size:12px;font-weight:700;color:#cbd5e1;text-transform:uppercase;letter-spacing:.06em;">
                <span>Privacy Policy</span><span>Terms</span><span>Support</span>
            </div>
        </footer>
    </div>
</div>

</body>
</html>
