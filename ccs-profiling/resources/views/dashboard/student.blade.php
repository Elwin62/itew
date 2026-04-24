@extends('layouts.app')
@section('title','My Dashboard')
@section('page-title','My Dashboard')
@section('page-subtitle','Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . auth()->user()->name . '! 👋')

@section('content')

@if($student)
{{-- ═══════════════════ LINKED STUDENT ═══════════════════ --}}
@php
    $rec = $student->academicRecords->first();
    $gpa = $rec?->gpa ?? 'N/A';
    $units = $rec ? $rec->units_passed . '/' . $rec->units_enrolled : 'N/A';
@endphp

{{-- Hero Welcome Banner --}}
<div style="background:linear-gradient(135deg,#1e40af 0%,#3b82f6 50%,#6366f1 100%);border-radius:24px;padding:28px 32px;margin-bottom:24px;display:flex;align-items:center;gap:24px;box-shadow:0 8px 32px rgba(59,130,246,.3);overflow:hidden;position:relative;">
    <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(255,255,255,.06);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-60px;right:100px;width:240px;height:240px;background:rgba(255,255,255,.04);border-radius:50%;"></div>
    <img src="{{ $student->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=fff&color=3b82f6&size=200' }}"
         style="width:80px;height:80px;border-radius:20px;object-fit:cover;border:3px solid rgba(255,255,255,.3);flex-shrink:0;position:relative;z-index:1;" alt="">
    <div style="position:relative;z-index:1;flex:1;">
        <div style="font-size:22px;font-weight:900;color:#fff;margin-bottom:4px;">{{ $student->full_name }}</div>
        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
            <span style="background:rgba(255,255,255,.15);color:#e0f2fe;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">🪪 {{ $student->student_id }}</span>
            <span style="background:rgba(255,255,255,.15);color:#e0f2fe;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">🎓 {{ $student->academic_program }}</span>
            <span style="background:rgba(255,255,255,.15);color:#e0f2fe;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">📅 Year {{ $student->year_level }} · Section {{ $student->section }}</span>
            @if($student->is_scholar)<span style="background:rgba(251,191,36,.25);color:#fde68a;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">⭐ Scholar</span>@endif
        </div>
    </div>
    <a href="{{ route('student.my-profile') }}" style="background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.25);color:#fff;padding:10px 20px;border-radius:12px;font-size:13px;font-weight:700;text-decoration:none;flex-shrink:0;position:relative;z-index:1;backdrop-filter:blur(4px);transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
        View Full Profile →
    </a>
</div>

{{-- Stat Cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    @foreach([
        ['GPA',          $gpa,                           '📊', '#3b82f6', '#dbeafe'],
        ['Units Passed', $units,                         '📚', '#8b5cf6', '#f3e8ff'],
        ['Skills',       $student->skills->count(),      '⭐', '#f97316', '#fff7ed'],
        ['Achievements', $student->achievements->count(),'🏆', '#f59e0b', '#fffbeb'],
    ] as [$l,$v,$ico,$c,$bg])
    <div class="stat-card" style="border-top:4px solid {{ $c }};">
        <div style="width:44px;height:44px;background:{{ $bg }};border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:14px;">{{ $ico }}</div>
        <div style="font-size:28px;font-weight:900;color:#0f172a;">{{ $v }}</div>
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-top:4px;">{{ $l }}</div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    {{-- Academic Standing --}}
    <div class="card" style="overflow:hidden;">
        <div style="height:4px;background:linear-gradient(90deg,#3b82f6,#6366f1);"></div>
        <div style="padding:24px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📚 Academic Standing</div>
            @if($rec)
            <div style="background:linear-gradient(135deg,#eff6ff,#f5f3ff);border-radius:16px;padding:20px;text-align:center;margin-bottom:16px;">
                <div style="font-size:52px;font-weight:900;color:#3b82f6;line-height:1;">{{ $rec->gpa }}</div>
                <div style="font-size:12px;font-weight:700;color:#6366f1;margin-top:4px;text-transform:uppercase;letter-spacing:.06em;">Grade Point Average</div>
                @if($rec->standing)
                <div style="margin-top:8px;display:inline-block;padding:4px 14px;border-radius:99px;background:#3b82f6;color:#fff;font-size:11px;font-weight:800;">{{ $rec->standing }}</div>
                @endif
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;color:#64748b;margin-bottom:10px;">
                <span>Enrolled: <strong style="color:#0f172a;">{{ $rec->units_enrolled }} units</strong></span>
                <span>Passed: <strong style="color:#22c55e;">{{ $rec->units_passed }} units</strong></span>
            </div>
            @php $pct = $rec->units_enrolled > 0 ? round(($rec->units_passed/$rec->units_enrolled)*100) : 0; @endphp
            <div style="background:#f1f5f9;border-radius:8px;height:10px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#3b82f6,#6366f1);border-radius:8px;transition:width .5s;"></div>
            </div>
            <div style="text-align:right;font-size:11px;color:#94a3b8;margin-top:4px;">{{ $pct }}% completion rate</div>
            @else
            <div class="empty-state"><div style="font-size:40px;">📊</div><div style="color:#64748b;font-size:13px;margin-top:8px;">No academic record yet</div></div>
            @endif
        </div>
    </div>

    {{-- Current Subjects & Grades --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📝 Current Subjects & Grades</div>
        @if($rec && $rec->grades->count())
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($rec->grades->take(5) as $g)
            @php $gc = $g->grade <= 1.5 ? '#22c55e' : ($g->grade <= 2.5 ? '#3b82f6' : ($g->grade <= 3.0 ? '#f59e0b' : '#ef4444')); @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#f8fafc;border-radius:10px;border-left:3px solid {{ $gc }};">
                <div>
                    <div style="font-size:12px;font-weight:800;color:#0f172a;">{{ $g->code }}</div>
                    <div style="font-size:11px;color:#64748b;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $g->name }}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:20px;font-weight:900;color:{{ $gc }};">{{ $g->grade }}</div>
                    <div style="font-size:10px;color:#94a3b8;">{{ $g->units }} units</div>
                </div>
            </div>
            @endforeach
            @if($rec->grades->count() > 5)
            <a href="{{ route('student.my-profile') }}?tab=academic" style="font-size:12px;color:#3b82f6;font-weight:700;text-align:center;text-decoration:none;display:block;padding:8px;background:#eff6ff;border-radius:8px;">
                +{{ $rec->grades->count()-5 }} more subjects →
            </a>
            @endif
        </div>
        @else
        <div class="empty-state"><div style="font-size:36px;">📝</div><div style="color:#64748b;font-size:13px;margin-top:8px;">No grades recorded yet</div></div>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px;">

    {{-- Skills --}}
    <div class="card" style="padding:22px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">⭐ My Skills</div>
        @if($student->skills->count())
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach($student->skills as $sk)
            @php $sbg=$sk->proficiency==='Advanced'?'#dcfce7':($sk->proficiency==='Intermediate'?'#dbeafe':'#f1f5f9'); $sco=$sk->proficiency==='Advanced'?'#15803d':($sk->proficiency==='Intermediate'?'#2563eb':'#475569'); @endphp
            <span style="padding:5px 12px;border-radius:99px;font-size:11px;font-weight:700;background:{{ $sbg }};color:{{ $sco }};">{{ $sk->name }}</span>
            @endforeach
        </div>
        @else
        <p style="font-size:13px;color:#94a3b8;">No skills recorded.</p>
        @endif
    </div>

    {{-- Achievements --}}
    <div class="card" style="padding:22px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">🏆 Achievements</div>
        @forelse($student->achievements->take(4) as $a)
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f8fafc;">
            <div style="width:34px;height:34px;background:#fffbeb;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">🏆</div>
            <div><div style="font-size:12px;font-weight:800;color:#0f172a;">{{ $a->type }}</div><div style="font-size:11px;color:#94a3b8;">{{ $a->level }}</div></div>
        </div>
        @empty
        <p style="font-size:13px;color:#94a3b8;">No achievements yet.</p>
        @endforelse
    </div>

    {{-- Organizations --}}
    <div class="card" style="padding:22px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">🏛 Organizations</div>
        @forelse($student->organizations->take(4) as $org)
        <div style="padding:8px 0;border-bottom:1px solid #f8fafc;">
            <div style="font-size:12px;font-weight:800;color:#0f172a;">{{ $org->name }}</div>
            <div style="font-size:11px;color:#94a3b8;">{{ $org->position }} · <span class="badge {{ $org->status==='Active'?'badge-green':'badge-slate' }}" style="font-size:10px;padding:1px 8px;">{{ $org->status }}</span></div>
        </div>
        @empty
        <p style="font-size:13px;color:#94a3b8;">No organizations joined.</p>
        @endforelse
    </div>
</div>

@else
{{-- ═══════════════════ NOT LINKED ═══════════════════ --}}

{{-- Welcome Banner --}}
<div style="background:linear-gradient(135deg,#1e40af 0%,#3b82f6 50%,#6366f1 100%);border-radius:24px;padding:32px;margin-bottom:24px;position:relative;overflow:hidden;box-shadow:0 8px 32px rgba(59,130,246,.3);">
    <div style="position:absolute;top:-50px;right:-50px;width:200px;height:200px;background:rgba(255,255,255,.06);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-70px;right:120px;width:260px;height:260px;background:rgba(255,255,255,.04);border-radius:50%;"></div>
    <div style="position:relative;z-index:1;">
        <div style="font-size:32px;margin-bottom:12px;">🎓</div>
        <div style="font-size:24px;font-weight:900;color:#fff;margin-bottom:8px;">Welcome, {{ auth()->user()->name }}!</div>
        <p style="color:#bfdbfe;font-size:14px;margin:0 0 16px;max-width:520px;line-height:1.6;">Your account is registered but your student record hasn't been linked yet. Contact the registrar office or administrator to link your profile and unlock all features.</p>
        <div style="background:rgba(255,255,255,.1);border-radius:12px;padding:12px 16px;display:inline-block;">
            <span style="font-size:12px;color:#bfdbfe;font-weight:600;">Registered email:</span>
            <span style="font-size:13px;color:#fff;font-weight:800;margin-left:8px;">{{ auth()->user()->email }}</span>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:24px;">
    @foreach([['📋','What to do next','Visit the registrar office with your ID and ask them to link your student account.'],['📧','Contact Admin','Send an email to admin@pnc.edu.ph with your Student ID to request profile linking.'],['⏳','Processing Time','Profile linking usually takes 1–2 business days after your request is submitted.']] as [$ico,$t,$d])
    <div class="card" style="padding:22px;">
        <div style="font-size:28px;margin-bottom:12px;">{{ $ico }}</div>
        <div style="font-size:14px;font-weight:800;color:#0f172a;margin-bottom:6px;">{{ $t }}</div>
        <p style="font-size:12px;color:#64748b;line-height:1.6;margin:0;">{{ $d }}</p>
    </div>
    @endforeach
</div>
@endif

{{-- Upcoming Events (always visible) --}}
<div class="card" style="padding:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;">📅 Upcoming School Events</div>
        <span style="font-size:11px;font-weight:700;background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:99px;">{{ $upcomingEvents->count() }} events</span>
    </div>
    @if($upcomingEvents->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
        @foreach($upcomingEvents as $event)
        @php $ec=['Academic'=>'#3b82f6','Social'=>'#8b5cf6','Sports'=>'#f97316','Workshop'=>'#14b8a6','Holiday'=>'#ec4899'][$event->category]??'#64748b'; @endphp
        <div style="padding:16px;border-radius:14px;border-left:4px solid {{ $ec }};background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
            <span style="display:inline-block;margin-bottom:8px;padding:2px 10px;border-radius:99px;font-size:10px;font-weight:800;background:{{ $ec }};color:#fff;">{{ $event->category }}</span>
            <div style="font-size:13px;font-weight:800;color:#0f172a;margin-bottom:6px;">{{ $event->title }}</div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">📅 {{ $event->date->format('M d, Y') }}</div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">📍 {{ $event->location }}</div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state"><div style="font-size:36px;">📅</div><div style="color:#64748b;font-size:13px;margin-top:8px;">No upcoming events.</div></div>
    @endif
</div>

@endsection
