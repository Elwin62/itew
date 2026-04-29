@extends('layouts.app')
@section('title', 'Schedules')
@section('page-title', 'Class Schedules')
@section('page-subtitle', 'Weekly schedule grid for all classes')
@section('content')

<div class="card" style="overflow:hidden;">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
        <div style="font-weight:800;font-size:16px;color:#0f172a;">📅 AY 2023-2024 · 1st Semester Schedule</div>
        <span style="font-size:11px;font-weight:700;background:#f1f5f9;color:#64748b;padding:4px 12px;border-radius:99px;">{{ $schedules->count() }} classes</span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th class="table-th" style="text-align:left;">Subject</th>
                    <th class="table-th" style="text-align:left;">Day</th>
                    <th class="table-th" style="text-align:left;">Time</th>
                    <th class="table-th" style="text-align:left;">Room</th>
                    <th class="table-th" style="text-align:left;">Section</th>
                    <th class="table-th" style="text-align:left;">Type</th>
                    <th class="table-th" style="text-align:left;">Faculty ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $sched)
                @php $isToday = $sched->day === now()->format('l'); @endphp
                <tr style="{{ $isToday ? 'background:#fffbeb;' : '' }}">
                    <td class="table-td">
                        <div style="font-size:13px;font-weight:800;color:#0f172a;">{{ $sched->subject_name }}</div>
                        <div style="font-size:11px;color:#f97316;font-weight:700;">{{ $sched->subject_code }}</div>
                    </td>
                    <td class="table-td">
                        <span style="font-size:13px;font-weight:{{ $isToday ? '800' : '600' }};color:{{ $isToday ? '#f97316' : '#475569' }};">
                            {{ $sched->day }}
                            @if($isToday)<span style="display:inline-block;width:6px;height:6px;background:#f97316;border-radius:50%;margin-left:4px;vertical-align:middle;"></span>@endif
                        </span>
                    </td>
                    <td class="table-td" style="font-size:13px;font-weight:700;color:#0f172a;">{{ $sched->start_time }} – {{ $sched->end_time }}</td>
                    <td class="table-td">
                        <span style="background:#f1f5f9;padding:4px 10px;border-radius:8px;font-size:12px;font-weight:700;color:#475569;">{{ $sched->room }}</span>
                    </td>
                    <td class="table-td" style="font-size:13px;font-weight:700;color:#475569;">{{ $sched->section }}</td>
                    <td class="table-td">
                        <span class="badge {{ $sched->type === 'Laboratory' ? 'badge-purple' : 'badge-blue' }}">{{ $sched->type }}</span>
                    </td>
                    <td class="table-td" style="font-size:12px;color:#64748b;">{{ $sched->faculty_id }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div style="font-size:48px;margin-bottom:12px;">📅</div>
                            <div style="font-weight:700;font-size:16px;color:#475569;">No schedules found</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Weekly View --}}
@if($schedules->count())
<div class="card" style="padding:24px;margin-top:24px;">
    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:20px;">📋 Weekly Overview</div>
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($days as $day)
        @php $dayClasses = $schedules->where('day', $day)->sortBy('start_time'); @endphp
        <div style="display:grid;grid-template-columns:110px 1fr;gap:12px;align-items:start;padding:8px 0;{{ $day === now()->format('l') ? 'background:#fffbeb;border-radius:12px;padding:12px 16px;' : '' }}">
            <div style="font-size:13px;font-weight:800;color:{{ $day === now()->format('l') ? '#f97316' : '#94a3b8' }};">
                {{ $day }}
                @if($day === now()->format('l'))<span style="display:inline-block;width:6px;height:6px;background:#f97316;border-radius:50%;margin-left:4px;vertical-align:middle;"></span>@endif
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @forelse($dayClasses as $c)
                <div style="padding:8px 14px;border-radius:10px;font-size:12px;font-weight:700;background:{{ $day===now()->format('l')?'#fff7ed':'#f8fafc' }};color:{{ $day===now()->format('l')?'#c2410c':'#475569' }};border:1px solid {{ $day===now()->format('l')?'#fed7aa':'#f1f5f9' }};">
                    <div style="font-weight:800;">{{ $c->subject_code }} · {{ $c->section }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $c->start_time }}–{{ $c->end_time }} · {{ $c->room }}</div>
                </div>
                @empty
                <span style="font-size:12px;color:#cbd5e1;padding:8px 0;">No classes</span>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
