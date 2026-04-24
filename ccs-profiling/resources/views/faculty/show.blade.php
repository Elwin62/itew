@extends('layouts.app')
@section('title', $faculty ? $faculty->full_name : 'My Profile')
@section('page-title', 'Faculty Profile')
@section('page-subtitle', $faculty ? $faculty->faculty_id . ' · ' . $faculty->department : 'Profile not yet linked')

@section('content')

@php $isAdmin = auth()->user()->role === 'Admin'; @endphp

@if(!$faculty)
{{-- Not linked state --}}
<div style="max-width:520px;margin:60px auto;text-align:center;">
    <div style="font-size:64px;margin-bottom:20px;">👨‍🏫</div>
    <h2 style="font-size:22px;font-weight:900;color:#0f172a;margin:0 0 10px;">Profile Not Linked</h2>
    <p style="color:#64748b;font-size:14px;margin:0 0 8px;line-height:1.6;">Your account email <strong>{{ auth()->user()->email }}</strong> doesn't match any faculty record.</p>
    <p style="color:#94a3b8;font-size:13px;margin:0 0 24px;">Contact the administrator to link your profile.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">← Back to Dashboard</a>
</div>
@else

@if($isAdmin)
<div style="margin-bottom:16px;">
    <a href="{{ route('faculty.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;font-size:13px;font-weight:700;text-decoration:none;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#64748b'">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Faculty List
    </a>
</div>
@endif

<div style="display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start;">

    {{-- Left Column --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Profile Card --}}
        <div class="card" style="overflow:hidden;">
            <div style="height:80px;background:linear-gradient(135deg,#8b5cf6,#a855f7,#d946ef);"></div>
            <div style="padding:0 24px 24px;margin-top:-44px;">
                <img src="{{ $faculty->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($faculty->full_name).'&background=8b5cf6&color=fff&size=200' }}"
                     style="width:80px;height:80px;border-radius:20px;object-fit:cover;border:4px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.12);" alt="">
                <div style="margin-top:12px;">
                    <h2 style="font-size:19px;font-weight:900;color:#0f172a;margin:0 0 4px;">{{ $faculty->full_name }}</h2>
                    <div style="font-size:12px;color:#8b5cf6;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">{{ $faculty->faculty_id }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;">
                        <span style="background:#faf5ff;color:#7c3aed;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;">{{ $faculty->academic_rank }}</span>
                        <span style="background:#f1f5f9;color:#475569;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;">{{ $faculty->employment_status }}</span>
                    </div>
                </div>
            </div>
            <div style="padding:0 24px 24px;border-top:1px solid #f8fafc;padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                @foreach([['📧',$faculty->email],['📱',$faculty->contact_number],['🏛',$faculty->department],['📍',$faculty->address]] as [$icon,$val])
                <div style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:#475569;">
                    <span style="flex-shrink:0;">{{ $icon }}</span><span style="flex:1;">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Employment Stats --}}
        <div style="background:linear-gradient(135deg,#8b5cf6,#a855f7);border-radius:20px;padding:20px;color:#fff;box-shadow:0 8px 24px rgba(139,92,246,.3);">
            <div style="font-size:10px;font-weight:700;opacity:.75;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">Employment Info</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                @foreach([['Experience',$faculty->years_experience.' yrs'],['Date Hired',$faculty->date_hired?->format('M Y')],['Status',$faculty->employment_status],['Subjects',$faculty->subjects->count().' taught']] as [$l,$v])
                <div>
                    <div style="font-size:10px;font-weight:700;opacity:.7;margin-bottom:2px;">{{ $l }}</div>
                    <div style="font-size:15px;font-weight:900;">{{ $v }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Admin actions --}}
        @if($isAdmin)
        <div style="display:flex;gap:10px;">
            <a href="{{ route('faculty.edit', $faculty) }}" class="btn btn-primary" style="flex:1;justify-content:center;">✏️ Edit</a>
            <form method="POST" action="{{ route('faculty.destroy', $faculty) }}" onsubmit="return confirm('Delete this faculty member?')" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">🗑 Delete</button>
            </form>
        </div>
        @endif
    </div>

    {{-- Right Column --}}
    <div>
        @php
            $activeTab = request('tab', 'personal');
            $tabUrl = function($tid) use ($isAdmin, $faculty) {
                return $isAdmin
                    ? route('faculty.show', [$faculty, 'tab' => $tid])
                    : route('faculty.my-profile') . '?tab=' . $tid;
            };
        @endphp

        {{-- Tab Nav --}}
        <div class="card" style="padding:8px;display:flex;flex-wrap:wrap;gap:4px;margin-bottom:16px;">
            @foreach([['personal','👤','Personal'],['academic','🎓','Education & Skills'],['subjects','📚','Subjects']] as [$id,$em,$lbl])
            <a href="{{ $tabUrl($id) }}" class="tab-btn {{ $activeTab===$id?'active':'' }}">{{ $em }} {{ $lbl }}</a>
            @endforeach
        </div>

        {{-- Tab Content --}}
        <div class="card" style="padding:28px;min-height:400px;">
            @if($activeTab === 'personal')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                @foreach([['Full Name',$faculty->full_name],['Gender',$faculty->gender],['Birthdate',$faculty->birthdate?->format('M d, Y')],['Civil Status',$faculty->civil_status],['Nationality',$faculty->nationality],['Department',$faculty->department],['Academic Rank',$faculty->academic_rank],['Date Hired',$faculty->date_hired?->format('M d, Y')],['Contract End',$faculty->contract_end_date?->format('M d, Y') ?? 'Permanent']] as [$l,$v])
                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">{{ $l }}</div>
                    <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $v ?? 'N/A' }}</div>
                </div>
                @endforeach
            </div>

            @elseif($activeTab === 'academic')
            <div style="margin-bottom:28px;">
                <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:16px;">Educational Background</div>
                @forelse($faculty->education as $edu)
                <div style="padding:14px 16px;border-radius:12px;border:1px solid #f1f5f9;display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                    <div style="width:40px;height:40px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;">🎓</div>
                    <div>
                        <div style="font-weight:700;font-size:13px;color:#0f172a;">{{ $edu->degree }}</div>
                        <div style="font-size:12px;color:#64748b;">{{ $edu->school }}</div>
                        @if($edu->year_graduated)<div style="font-size:11px;color:#94a3b8;">Graduated {{ $edu->year_graduated }}</div>@endif
                    </div>
                </div>
                @empty
                <p style="font-size:13px;color:#94a3b8;">No education records.</p>
                @endforelse
            </div>
            <div>
                <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:16px;">Skills & Expertise</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @forelse($faculty->skills as $skill)
                    <span style="padding:6px 14px;border-radius:99px;font-size:12px;font-weight:700;background:#f3e8ff;color:#7c3aed;">{{ $skill->skill }}</span>
                    @empty
                    <p style="font-size:13px;color:#94a3b8;">No skills recorded.</p>
                    @endforelse
                </div>
            </div>

            @elseif($activeTab === 'subjects')
            <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:16px;">Subjects Handled</div>
            @if($faculty->subjects->count())
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                @foreach($faculty->subjects as $subj)
                <div style="padding:16px;border-radius:14px;border:1.5px solid #e9d5ff;background:#faf5ff;display:flex;align-items:center;gap:12px;">
                    <div style="width:44px;height:44px;background:#8b5cf6;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;color:#fff;flex-shrink:0;">{{ substr($subj->subject_code,0,4) }}</div>
                    <div>
                        <div style="font-weight:800;font-size:13px;color:#0f172a;">{{ $subj->subject_code }}</div>
                        @if($subj->subject_name)<div style="font-size:12px;color:#64748b;">{{ $subj->subject_name }}</div>@endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state"><div style="font-size:40px;">📚</div><div style="color:#64748b;font-size:13px;margin-top:8px;">No subjects recorded.</div></div>
            @endif
            @endif
        </div>
    </div>
</div>
@endif
@endsection
