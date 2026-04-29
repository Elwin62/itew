@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-subtitle','Welcome back — here\'s your system overview')

@section('content')

{{-- Stats Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:28px;">
@php
$statCards = [
  ['Total Students','🎓',number_format($stats['total_students']),'#3b82f6','#dbeafe','#1d4ed8'],
  ['Enrolled','✅',number_format($stats['enrolled']),'#22c55e','#dcfce7','#15803d'],
  ['Faculty Members','👨‍🏫',number_format($stats['total_faculty']),'#8b5cf6','#f3e8ff','#6d28d9'],
  ['Scholars','🏆',number_format($stats['scholars']),'#f97316','#ffedd5','#c2410c'],
];
@endphp
@foreach($statCards as [$label,$icon,$value,$color,$bg,$dark])
<div class="stat-card" style="border-top:4px solid {{$color}};">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div style="width:48px;height:48px;background:{{$bg}};border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;">{{$icon}}</div>
    </div>
    <div style="font-size:32px;font-weight:900;color:#0f172a;line-height:1;">{{$value}}</div>
    <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-top:6px;">{{$label}}</div>
</div>
@endforeach
</div>

{{-- Quick Query Buttons --}}
<div class="card" style="padding:24px;margin-bottom:28px;">
    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">⚡ Quick Queries</div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;">
        <a href="{{ route('queries.basketball') }}" class="btn btn-primary">
            🏀 Basketball Skills
            <span style="background:rgba(255,255,255,.25);padding:2px 8px;border-radius:6px;font-size:12px;">{{ number_format($stats['basketball_count']) }}</span>
        </a>
        <a href="{{ route('queries.programming') }}" class="btn btn-blue">
            💻 Programming Skills
            <span style="background:rgba(255,255,255,.25);padding:2px 8px;border-radius:6px;font-size:12px;">{{ number_format($stats['programming_count']) }}</span>
        </a>
        <a href="{{ route('students.create') }}" class="btn" style="background:#22c55e;color:#fff;box-shadow:0 4px 14px rgba(34,197,94,.3);">➕ Add Student</a>
        <a href="{{ route('faculty.create') }}" class="btn" style="background:#8b5cf6;color:#fff;box-shadow:0 4px 14px rgba(139,92,246,.3);">➕ Add Faculty</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    {{-- Program Distribution --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:20px;">Program Distribution</div>
        <div style="display:flex;flex-direction:column;gap:14px;">
            @foreach($programDist as $prog)
            @php $pct = $stats['total_students'] > 0 ? round(($prog->count/$stats['total_students'])*100) : 0; @endphp
            <div>
                <div style="display:flex;justify-content:space-between;font-size:12px;font-weight:700;color:#475569;margin-bottom:5px;">
                    <span>{{ $prog->academic_program }}</span>
                    <span style="color:#f97316;">{{ $prog->count }} ({{ $pct }}%)</span>
                </div>
                <div class="progress-bar"><div class="progress-fill" style="width:{{$pct}}%;"></div></div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Reports Summary Card --}}
    <div class="card" style="overflow:hidden;">
        <div style="height:4px;background:linear-gradient(90deg,#f97316,#f59e0b);"></div>
        <div style="padding:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;">📊 Reports Summary</div>
                <a href="{{ route('reports.admin') }}" style="font-size:12px;color:#f97316;font-weight:700;text-decoration:none;">View Full Reports →</a>
            </div>

            {{-- Gender Distribution --}}
            <div style="margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:8px;">Gender Distribution</div>
                @foreach($reportData['genderDist'] as $g)
                @php $gpct = $stats['total_students'] > 0 ? round(($g->count/$stats['total_students'])*100) : 0; @endphp
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <span style="font-size:11px;font-weight:700;color:#475569;width:55px;">{{ $g->gender }}</span>
                    <div style="flex:1;height:8px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                        <div style="height:100%;width:{{ $gpct }}%;background:{{ $g->gender==='Male'?'#3b82f6':'#ec4899' }};border-radius:99px;"></div>
                    </div>
                    <span style="font-size:11px;font-weight:800;color:#0f172a;width:32px;text-align:right;">{{ $gpct }}%</span>
                </div>
                @endforeach
            </div>

            {{-- Year Level --}}
            <div style="margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:8px;">Year Level Breakdown</div>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;">
                    @foreach($reportData['yearLevelDist'] as $yl)
                    <div style="text-align:center;padding:10px;background:#f8fafc;border-radius:12px;">
                        <div style="font-size:20px;font-weight:900;color:#3b82f6;">{{ $yl->count }}</div>
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;">Year {{ $yl->year_level }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Download buttons --}}
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                <a href="{{ route('reports.admin.download', ['type'=>'students']) }}" class="btn" style="background:#eff6ff;color:#2563eb;font-size:12px;padding:8px 14px;">
                    📥 Students CSV
                </a>
                <a href="{{ route('reports.admin.download', ['type'=>'faculty']) }}" class="btn" style="background:#f5f3ff;color:#7c3aed;font-size:12px;padding:8px 14px;">
                    📥 Faculty CSV
                </a>
                <a href="{{ route('reports.admin.download', ['type'=>'enrollment']) }}" class="btn" style="background:#f0fdf4;color:#16a34a;font-size:12px;padding:8px 14px;">
                    📥 Enrollment CSV
                </a>
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    {{-- Recent Activity --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:20px;">Recent Activity</div>
        <div style="display:flex;flex-direction:column;gap:14px;">
            @forelse($recentActivities as $log)
            <div style="display:flex;align-items:flex-start;gap:10px;">
                <div style="width:34px;height:34px;border-radius:10px;background:#fff7ed;color:#f97316;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:13px;flex-shrink:0;">{{ strtoupper(substr($log->user_name,0,1)) }}</div>
                <div style="min-width:0;">
                    <div style="font-size:12px;font-weight:600;color:#334155;">{{ $log->action }} <span style="color:#f97316;">{{ Str::limit($log->target,20) }}</span></div>
                    <div style="font-size:11px;color:#94a3b8;">{{ $log->created_at->diffForHumans() }} · {{ $log->module }}</div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:32px 0;">
                <div style="font-size:32px;margin-bottom:8px;">📋</div>
                <div style="font-size:13px;font-weight:600;color:#94a3b8;">No activity yet</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Upcoming Events --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:20px;">Upcoming Events</div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            @forelse($upcomingEvents as $event)
            <div style="padding:14px;border-radius:14px;background:#f8fafc;border:1.5px solid #f1f5f9;">
                <div style="font-weight:700;font-size:13px;color:#0f172a;margin-bottom:4px;">{{ $event->title }}</div>
                <div style="font-size:11px;color:#64748b;">📅 {{ $event->date->format('M d, Y') }} · {{ $event->location }}</div>
                <div style="margin-top:8px;">
                    @if($event->category==='Academic')<span class="badge badge-blue">{{ $event->category }}</span>
                    @elseif($event->category==='Sports')<span class="badge badge-orange">{{ $event->category }}</span>
                    @elseif($event->category==='Workshop')<span class="badge badge-teal">{{ $event->category }}</span>
                    @else<span class="badge badge-purple">{{ $event->category }}</span>@endif
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:32px 0;">
                <div style="font-size:32px;margin-bottom:8px;">📅</div>
                <div style="font-size:13px;font-weight:600;color:#94a3b8;">No upcoming events</div>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
