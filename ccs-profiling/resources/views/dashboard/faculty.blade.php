@extends('layouts.app')
@section('title','Faculty Dashboard')
@section('page-title','Faculty Dashboard')
@section('page-subtitle','Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . auth()->user()->name . '! 👋')

@section('content')

{{-- ── Stat Cards ── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    @foreach([
        ['Total Students',   number_format($totalStudents),  '👥', '#3b82f6', '#dbeafe'],
        ['My Sections',      $totalSections ?: $schedules->count(), '📋', '#8b5cf6', '#f3e8ff'],
        ['Today\'s Classes', $todaySchedule->count(),        '🗓', '#f97316', '#fff7ed'],
        ['Subjects',         $faculty ? $faculty->subjects->count() : $schedules->pluck('subject_code')->unique()->count(), '📚', '#14b8a6', '#ccfbf1'],
    ] as [$l,$v,$ico,$c,$bg])
    <div class="stat-card" style="border-top:4px solid {{ $c }};">
        <div style="width:44px;height:44px;background:{{ $bg }};border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:14px;">{{ $ico }}</div>
        <div style="font-size:28px;font-weight:900;color:#0f172a;">{{ $v }}</div>
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-top:4px;">{{ $l }}</div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;margin-bottom:20px;">

    {{-- Profile Card --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card" style="overflow:hidden;">
            <div style="height:80px;background:linear-gradient(135deg,#8b5cf6,#a855f7,#d946ef);"></div>
            <div style="padding:0 20px 20px;margin-top:-40px;">
                <img src="{{ $faculty?->profile_photo ?? auth()->user()->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=8b5cf6&color=fff&size=200' }}"
                     style="width:72px;height:72px;border-radius:18px;object-fit:cover;border:4px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.12);" alt="">
                <div style="margin-top:10px;">
                    <div style="font-size:17px;font-weight:900;color:#0f172a;">{{ $faculty?->full_name ?? auth()->user()->name }}</div>
                    <div style="font-size:11px;color:#8b5cf6;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">Faculty Member</div>
                </div>
            </div>
            <div style="padding:0 20px 20px;display:flex;flex-direction:column;gap:8px;">
                @if($faculty)
                @foreach([
                    ['🏛', $faculty->department],
                    ['🎓', $faculty->academic_rank],
                    ['💼', $faculty->employment_status],
                    ['⏳', $faculty->years_experience . ' years experience'],
                    ['📧', $faculty->email],
                    ['📱', $faculty->contact_number],
                ] as [$ico, $val])
                <div style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:#475569;">
                    <span style="flex-shrink:0;">{{ $ico }}</span>
                    <span>{{ $val }}</span>
                </div>
                @endforeach
                @else
                <div style="text-align:center;padding:16px 0;">
                    <p style="font-size:12px;color:#94a3b8;line-height:1.6;">Account not linked to a faculty record.<br>Contact admin to link your profile.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Education --}}
        @if($faculty && $faculty->education->count())
        <div class="card" style="padding:20px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:12px;">🎓 Education</div>
            @foreach($faculty->education as $edu)
            <div style="padding:10px 0;border-bottom:1px solid #f8fafc;">
                <div style="font-size:12px;font-weight:800;color:#0f172a;">{{ $edu->degree }}</div>
                <div style="font-size:11px;color:#64748b;">{{ $edu->school }}</div>
                @if($edu->year_graduated)<div style="font-size:11px;color:#94a3b8;">Graduated {{ $edu->year_graduated }}</div>@endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Expertise --}}
        @if($faculty && $faculty->skills->count())
        <div class="card" style="padding:20px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:12px;">⭐ Expertise</div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                @foreach($faculty->skills as $sk)
                <span style="padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;background:#f3e8ff;color:#7c3aed;">{{ $sk->skill }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Main right column --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Today's Schedule --}}
        <div class="card" style="overflow:hidden;">
            <div style="height:4px;background:linear-gradient(90deg,#8b5cf6,#a855f7);"></div>
            <div style="padding:20px 24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;">🗓 Today's Classes — {{ now()->format('l, F j') }}</div>
                    <span style="padding:4px 12px;border-radius:99px;font-size:11px;font-weight:800;background:{{ $todaySchedule->count() > 0 ? '#dcfce7' : '#f1f5f9' }};color:{{ $todaySchedule->count() > 0 ? '#15803d' : '#64748b' }};">
                        {{ $todaySchedule->count() }} class{{ $todaySchedule->count() !== 1 ? 'es' : '' }} today
                    </span>
                </div>
                @if($todaySchedule->count())
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach($todaySchedule as $class)
                    <div style="display:flex;align-items:center;gap:16px;padding:14px 18px;border-radius:14px;background:linear-gradient(135deg,#faf5ff,#f3e8ff);border:1.5px solid #e9d5ff;">
                        <div style="text-align:center;min-width:60px;">
                            <div style="font-size:14px;font-weight:900;color:#7c3aed;">{{ $class->start_time }}</div>
                            <div style="font-size:10px;color:#a78bfa;font-weight:600;">to {{ $class->end_time }}</div>
                        </div>
                        <div style="width:2px;height:40px;background:#d8b4fe;border-radius:2px;flex-shrink:0;"></div>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:900;color:#0f172a;">{{ $class->subject_code }} — {{ $class->subject_name }}</div>
                            <div style="font-size:11px;color:#64748b;margin-top:2px;display:flex;gap:12px;">
                                <span>📍 {{ $class->room }}</span>
                                <span>👥 Section {{ $class->section }}</span>
                                @php $sc = \App\Models\Student::where('section',$class->section)->count(); @endphp
                                <span>🎓 {{ $sc }} students</span>
                            </div>
                        </div>
                        <span style="padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;background:#ede9fe;color:#7c3aed;">{{ $class->type ?? 'Lecture' }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="text-align:center;padding:32px 0;">
                    <div style="font-size:40px;margin-bottom:10px;">☕</div>
                    <div style="font-size:15px;font-weight:700;color:#475569;">No classes today!</div>
                    <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Enjoy your free day.</div>
                </div>
                @endif
            </div>
        </div>

        {{-- My Classes / Sections --}}
        @if($sectionCounts->count())
        <div class="card" style="overflow:hidden;">
            <div style="height:4px;background:linear-gradient(90deg,#3b82f6,#6366f1);"></div>
            <div style="padding:20px 24px;">
                <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📋 My Classes & Sections</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
                    @foreach($sectionCounts as $sc)
                    <div style="padding:16px;border-radius:14px;background:#eff6ff;border:1.5px solid #bfdbfe;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                            <div style="font-size:11px;font-weight:800;background:#3b82f6;color:#fff;padding:3px 10px;border-radius:99px;">{{ $sc['section'] }}</div>
                            <div style="font-size:22px;font-weight:900;color:#1d4ed8;">{{ $sc['student_count'] }}</div>
                        </div>
                        <div style="font-size:12px;font-weight:800;color:#1e40af;">{{ $sc['subject_code'] }}</div>
                        <div style="font-size:11px;color:#3b82f6;">{{ Str::limit($sc['subject'] ?? '', 30) }}</div>
                        <div style="font-size:10px;color:#93c5fd;margin-top:4px;">📍 {{ $sc['room'] ?? 'TBA' }}</div>
                        <div style="font-size:10px;color:#60a5fa;margin-top:2px;">🎓 {{ $sc['student_count'] }} enrolled students</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Weekly Schedule --}}
        <div class="card" style="overflow:hidden;">
            <div style="height:4px;background:linear-gradient(90deg,#f97316,#fb923c);"></div>
            <div style="padding:20px 24px;">
                <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📅 Weekly Schedule</div>
                @php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday']; @endphp
                <div style="display:flex;flex-direction:column;gap:6px;">
                    @foreach($days as $day)
                    @php $dayClasses = $schedules->where('day', $day)->sortBy('start_time'); @endphp
                    <div style="display:grid;grid-template-columns:90px 1fr;gap:12px;align-items:start;">
                        <div style="padding:8px 0;font-size:12px;font-weight:800;color:{{ $day === now()->format('l') ? '#f97316' : '#94a3b8' }};">
                            {{ substr($day,0,3) }}
                            @if($day === now()->format('l'))<span style="display:inline-block;width:6px;height:6px;background:#f97316;border-radius:50%;margin-left:4px;vertical-align:middle;"></span>@endif
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;padding:4px 0;">
                            @forelse($dayClasses as $c)
                            <span style="padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;background:{{ $day===now()->format('l')?'#fff7ed':'#f8fafc' }};color:{{ $day===now()->format('l')?'#c2410c':'#475569' }};border:1px solid {{ $day===now()->format('l')?'#fed7aa':'#f1f5f9' }};">
                                {{ $c->start_time }}–{{ $c->end_time }} · {{ $c->subject_code }} · {{ $c->section }}
                            </span>
                            @empty
                            <span style="font-size:11px;color:#cbd5e1;padding:5px 0;">No classes</span>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Upcoming Events --}}
<div class="card" style="padding:24px;">
    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📅 Upcoming School Events</div>
    @if($upcomingEvents->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;">
        @foreach($upcomingEvents as $event)
        @php $ec=['Academic'=>'#3b82f6','Social'=>'#8b5cf6','Sports'=>'#f97316','Workshop'=>'#14b8a6','Holiday'=>'#ec4899'][$event->category]??'#64748b'; @endphp
        <div style="padding:16px;border-radius:14px;border-left:4px solid {{ $ec }};background:#f8fafc;">
            <div style="font-size:13px;font-weight:800;color:#0f172a;margin-bottom:6px;">{{ $event->title }}</div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">📅 {{ $event->date->format('M d, Y') }}</div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">📍 {{ $event->location }}</div>
            <span style="display:inline-block;margin-top:8px;padding:2px 10px;border-radius:99px;font-size:10px;font-weight:800;background:{{ $ec }};color:#fff;">{{ $event->category }}</span>
        </div>
        @endforeach
    </div>
    @else
    <p style="font-size:13px;color:#94a3b8;">No upcoming events.</p>
    @endif
</div>

@endsection
