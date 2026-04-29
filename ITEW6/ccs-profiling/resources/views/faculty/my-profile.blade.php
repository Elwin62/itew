@extends('layouts.app')
@section('title','My Profile')
@section('page-title','My Profile')
@section('page-subtitle','View and manage your faculty information')
@section('content')

@if($faculty)
<div style="background:linear-gradient(135deg,#7c3aed 0%,#8b5cf6 50%,#a855f7 100%);border-radius:24px;padding:0;margin-bottom:24px;overflow:hidden;box-shadow:0 8px 32px rgba(139,92,246,.3);position:relative;">
    <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(255,255,255,.06);border-radius:50%;"></div>
    <div style="padding:32px;display:flex;align-items:center;gap:24px;position:relative;z-index:1;">
        <img src="{{ $faculty->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($faculty->full_name).'&background=fff&color=8b5cf6&size=200' }}"
             style="width:90px;height:90px;border-radius:20px;object-fit:cover;border:4px solid rgba(255,255,255,.3);flex-shrink:0;" alt="">
        <div style="flex:1;">
            <div style="font-size:24px;font-weight:900;color:#fff;margin-bottom:4px;">{{ $faculty->full_name }}</div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                <span style="background:rgba(255,255,255,.15);color:#e8dbff;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">🪪 {{ $faculty->faculty_id }}</span>
                <span style="background:rgba(255,255,255,.15);color:#e8dbff;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">🏛 {{ $faculty->department }}</span>
                <span style="background:rgba(255,255,255,.15);color:#e8dbff;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">🎓 {{ $faculty->academic_rank }}</span>
                <span style="background:rgba(255,255,255,.15);color:#e8dbff;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">💼 {{ $faculty->employment_status }}</span>
            </div>
        </div>
        <a href="{{ route('faculty.edit-profile') }}" class="btn" style="background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.25);color:#fff;backdrop-filter:blur(4px);">
            ✏️ Edit Profile
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">👤 Personal Information</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            @foreach([
                ['Full Name', $faculty->full_name], ['Faculty ID', $faculty->faculty_id],
                ['Gender', $faculty->gender], ['Birthdate', $faculty->birthdate ?? '—'],
                ['Email', $faculty->email], ['Contact', $faculty->contact_number],
                ['Nationality', $faculty->nationality ?? 'Filipino'], ['Civil Status', $faculty->civil_status ?? '—'],
            ] as [$l,$v])
            <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">{{ $l }}</div><div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $v }}</div></div>
            @endforeach
            <div style="grid-column:span 2;"><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Address</div><div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $faculty->address ?? '—' }}</div></div>
        </div>
    </div>

    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">🏛 Professional Information</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            @foreach([
                ['Department', $faculty->department], ['Academic Rank', $faculty->academic_rank],
                ['Employment Status', $faculty->employment_status], ['Years Experience', $faculty->years_experience.' years'],
            ] as [$l,$v])
            <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">{{ $l }}</div><div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $v }}</div></div>
            @endforeach
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">⭐ Skills & Expertise ({{ $faculty->skills->count() }})</div>
        @forelse($faculty->skills as $sk)
        <div style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #f8fafc;">
            <span style="padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;background:#f3e8ff;color:#7c3aed;">{{ $sk->skill }}</span>
        </div>
        @empty <p style="font-size:13px;color:#94a3b8;">No skills recorded.</p> @endforelse
    </div>

    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">🎓 Education ({{ $faculty->education->count() }})</div>
        @forelse($faculty->education as $edu)
        <div style="padding:8px 0;border-bottom:1px solid #f8fafc;">
            <div style="font-size:13px;font-weight:800;color:#0f172a;">{{ $edu->degree }}</div>
            @if($edu->school)<div style="font-size:11px;color:#64748b;">{{ $edu->school }}</div>@endif
            @if($edu->year_graduated)<div style="font-size:11px;color:#94a3b8;">Graduated {{ $edu->year_graduated }}</div>@endif
        </div>
        @empty <p style="font-size:13px;color:#94a3b8;">No education records.</p> @endforelse
    </div>

    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">📚 Subjects Taught ({{ $faculty->subjects->count() }})</div>
        @forelse($faculty->subjects as $sub)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f8fafc;">
            <span style="font-size:12px;font-weight:700;color:#0f172a;">{{ $sub->subject_name ?? $sub->name ?? 'Subject' }}</span>
            <span style="font-size:11px;color:#f97316;font-weight:700;">{{ $sub->subject_code ?? $sub->code ?? '' }}</span>
        </div>
        @empty <p style="font-size:13px;color:#94a3b8;">No subjects assigned.</p> @endforelse
    </div>
</div>

@else
<div class="card" style="padding:32px;"><div class="empty-state"><div style="font-size:48px;">👤</div><div style="font-size:16px;font-weight:700;color:#475569;margin-top:12px;">No Faculty Record Linked</div><p style="color:#94a3b8;margin-top:8px;">Contact admin to link your account.</p></div></div>
@endif
@endsection
