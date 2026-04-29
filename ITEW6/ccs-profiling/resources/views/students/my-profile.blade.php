@extends('layouts.app')
@section('title','My Profile')
@section('page-title','My Profile')
@section('page-subtitle','View and manage your personal information')
@section('content')

@if($student)
{{-- Banner --}}
<div style="background:linear-gradient(135deg,#1e40af 0%,#3b82f6 50%,#6366f1 100%);border-radius:24px;padding:0;margin-bottom:24px;overflow:hidden;box-shadow:0 8px 32px rgba(59,130,246,.3);position:relative;">
    <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(255,255,255,.06);border-radius:50%;"></div>
    <div style="padding:32px;display:flex;align-items:center;gap:24px;position:relative;z-index:1;">
        <img src="{{ $student->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=fff&color=3b82f6&size=200' }}"
             style="width:90px;height:90px;border-radius:20px;object-fit:cover;border:4px solid rgba(255,255,255,.3);flex-shrink:0;" alt="">
        <div style="flex:1;">
            <div style="font-size:24px;font-weight:900;color:#fff;margin-bottom:4px;">{{ $student->full_name }}</div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                <span style="background:rgba(255,255,255,.15);color:#e0f2fe;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">🪪 {{ $student->student_id }}</span>
                <span style="background:rgba(255,255,255,.15);color:#e0f2fe;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">🎓 {{ $student->academic_program }}</span>
                <span style="background:rgba(255,255,255,.15);color:#e0f2fe;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">📅 Year {{ $student->year_level }} · {{ $student->section }}</span>
                <span style="background:{{ $student->enrollment_status==='Enrolled'?'rgba(34,197,94,.3)':'rgba(239,68,68,.3)' }};color:#fff;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">{{ $student->enrollment_status }}</span>
                @if($student->is_scholar)<span style="background:rgba(251,191,36,.3);color:#fde68a;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">⭐ Scholar</span>@endif
            </div>
        </div>
        <a href="{{ route('student.edit-profile') }}" class="btn" style="background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.25);color:#fff;backdrop-filter:blur(4px);">
            ✏️ Edit Profile
        </a>
    </div>
</div>

{{-- Personal Info --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">👤 Personal Information</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            @foreach([
                ['Full Name', $student->full_name],
                ['Student ID', $student->student_id],
                ['Gender', $student->gender],
                ['Birthdate', $student->birthdate?->format('M d, Y')],
                ['Civil Status', $student->civil_status],
                ['Nationality', $student->nationality],
                ['Email', $student->email],
                ['Contact', $student->contact_number],
            ] as [$label, $value])
            <div>
                <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">{{ $label }}</div>
                <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $value ?? '—' }}</div>
            </div>
            @endforeach
            <div style="grid-column:span 2;">
                <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Address</div>
                <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $student->address ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">🎓 Academic Information</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            @foreach([
                ['Program', $student->academic_program],
                ['Year Level', 'Year '.$student->year_level],
                ['Section', $student->section],
                ['Status', $student->enrollment_status],
                ['Admission Type', $student->admission_type],
                ['Scholar', $student->is_scholar ? 'Yes ⭐' : 'No'],
                ['Academic Year', $student->academic_year],
                ['Semester', $student->semester],
            ] as [$label, $value])
            <div>
                <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">{{ $label }}</div>
                <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $value ?? '—' }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Skills, Achievements, Organizations --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">⭐ Skills ({{ $student->skills->count() }})</div>
        @forelse($student->skills as $sk)
        @php $pc=$sk->proficiency==='Advanced'?['#dcfce7','#15803d']:($sk->proficiency==='Intermediate'?['#dbeafe','#2563eb']:['#f1f5f9','#475569']); @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f8fafc;">
            <span style="font-size:13px;font-weight:700;color:#0f172a;">{{ $sk->name }}</span>
            <span style="padding:2px 10px;border-radius:99px;font-size:10px;font-weight:700;background:{{ $pc[0] }};color:{{ $pc[1] }};">{{ $sk->proficiency }}</span>
        </div>
        @empty <p style="font-size:13px;color:#94a3b8;">No skills recorded.</p> @endforelse
    </div>

    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">🏆 Achievements ({{ $student->achievements->count() }})</div>
        @forelse($student->achievements as $a)
        <div style="padding:8px 0;border-bottom:1px solid #f8fafc;">
            <div style="font-size:13px;font-weight:800;color:#0f172a;">{{ $a->type }}</div>
            <div style="font-size:11px;color:#94a3b8;">{{ $a->level }} · {{ $a->date_received }}</div>
        </div>
        @empty <p style="font-size:13px;color:#94a3b8;">No achievements yet.</p> @endforelse
    </div>

    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">🏛 Organizations ({{ $student->organizations->count() }})</div>
        @forelse($student->organizations as $org)
        <div style="padding:8px 0;border-bottom:1px solid #f8fafc;">
            <div style="font-size:13px;font-weight:800;color:#0f172a;">{{ $org->name }}</div>
            <div style="font-size:11px;color:#94a3b8;">{{ $org->position }} · <span class="badge {{ $org->status==='Active'?'badge-green':'badge-slate' }}" style="font-size:10px;padding:1px 8px;">{{ $org->status }}</span></div>
        </div>
        @empty <p style="font-size:13px;color:#94a3b8;">No organizations.</p> @endforelse
    </div>
</div>

{{-- Academic Records & Grades --}}
@if($student->academicRecords->count())
<div class="card" style="overflow:hidden;margin-bottom:24px;">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;"><div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;">📚 Academic Records</div></div>
    @foreach($student->academicRecords as $rec)
    <div style="padding:16px 24px;border-bottom:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div><span style="font-weight:800;color:#0f172a;">{{ $rec->academic_year }}</span> · <span style="color:#64748b;">{{ $rec->semester }}</span></div>
            <div style="display:flex;gap:12px;">
                <span style="font-size:20px;font-weight:900;color:#3b82f6;">GPA {{ $rec->gpa }}</span>
                <span class="badge badge-blue">{{ $rec->standing }}</span>
            </div>
        </div>
        @if($rec->grades->count())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;">
            @foreach($rec->grades as $g)
            @php $gc=$g->grade<=1.5?'#22c55e':($g->grade<=2.5?'#3b82f6':($g->grade<=3.0?'#f59e0b':'#ef4444')); @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#f8fafc;border-radius:8px;border-left:3px solid {{ $gc }};">
                <div><div style="font-size:12px;font-weight:800;">{{ $g->code }}</div><div style="font-size:10px;color:#94a3b8;">{{ Str::limit($g->name,20) }}</div></div>
                <div style="font-size:16px;font-weight:900;color:{{ $gc }};">{{ $g->grade }}</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- Guardian & Medical --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    @if($student->guardian)
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">👨‍👩‍👦 Guardian</div>
        @foreach([['Name',$student->guardian->name],['Relationship',$student->guardian->relationship],['Contact',$student->guardian->contact_number],['Email',$student->guardian->email]] as [$l,$v])
        <div style="margin-bottom:8px;"><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;">{{ $l }}</div><div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $v ?? '—' }}</div></div>
        @endforeach
    </div>
    @endif
    @if($student->medical)
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">🏥 Medical Info</div>
        @foreach([['Blood Type',$student->medical->blood_type],['Height',$student->medical->height],['Weight',$student->medical->weight],['Conditions',$student->medical->conditions]] as [$l,$v])
        <div style="margin-bottom:8px;"><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;">{{ $l }}</div><div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $v ?? '—' }}</div></div>
        @endforeach
    </div>
    @endif
</div>

@else
<div class="card" style="padding:32px;"><div class="empty-state"><div style="font-size:48px;">👤</div><div style="font-size:16px;font-weight:700;color:#475569;margin-top:12px;">No Student Record Linked</div><p style="color:#94a3b8;margin-top:8px;">Contact admin to link your account.</p></div></div>
@endif
@endsection
