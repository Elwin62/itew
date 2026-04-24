@extends('layouts.app')
@section('title', $student ? $student->full_name : 'Student Profile')
@section('page-title', $student ? 'Student Profile' : 'My Profile')
@section('page-subtitle', $student ? $student->student_id . ' · ' . $student->academic_program : 'Account not linked to a student record')

@section('content')

@if(!$student)
{{-- Not linked state --}}
<div style="max-width:500px;margin:80px auto;text-align:center;">
    <div style="font-size:64px;margin-bottom:20px;">🎓</div>
    <h2 style="font-size:22px;font-weight:900;color:#0f172a;margin:0 0 10px;">Profile Not Linked</h2>
    <p style="color:#64748b;font-size:14px;margin:0 0 24px;line-height:1.6;">Your account email (<strong>{{ auth()->user()->email }}</strong>) does not match any student record in the system. Please contact the administrator to link your profile.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">← Back to Dashboard</a>
</div>
@else

@php $isAdmin = auth()->user()->role === 'Admin'; @endphp

@if($isAdmin)
<div style="margin-bottom:16px;">
    <a href="{{ route('students.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;font-size:13px;font-weight:700;text-decoration:none;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#64748b'">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Directory
    </a>
</div>
@endif

<div style="display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start;">

    {{-- Left Column --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        {{-- Profile Card --}}
        <div class="card" style="overflow:hidden;">
            <div style="height:80px;background:linear-gradient(135deg,#f97316,#fb923c,#fbbf24);"></div>
            <div style="padding:0 24px 24px;margin-top:-44px;">
                <img src="{{ $student->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=f97316&color=fff&size=200' }}"
                     style="width:80px;height:80px;border-radius:20px;object-fit:cover;border:4px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.12);" alt="">
                <div style="margin-top:12px;">
                    <h2 style="font-size:20px;font-weight:900;color:#0f172a;margin:0 0 4px;">{{ $student->full_name }}</h2>
                    <div style="font-size:12px;color:#f97316;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">{{ $student->student_id }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;">
                        <span style="background:#f1f5f9;color:#475569;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;">{{ $student->academic_program }}</span>
                        <span style="background:#fff7ed;color:#f97316;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;">Year {{ $student->year_level }}</span>
                    </div>
                </div>
            </div>
            <div style="padding:0 24px 24px;border-top:1px solid #f8fafc;padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                @foreach([['📧',$student->email],['📱',$student->contact_number],['📍',$student->address]] as [$icon,$val])
                <div style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#475569;">
                    <span>{{ $icon }}</span><span style="flex:1;">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stats --}}
        <div style="background:linear-gradient(135deg,#f97316,#fb923c);border-radius:20px;padding:20px;color:#fff;box-shadow:0 8px 24px rgba(249,115,22,.3);">
            <div style="font-size:10px;font-weight:700;opacity:.75;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">Quick Stats</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                @php $gpa = $student->academicRecords->first()?->gpa ?? 'N/A'; @endphp
                @foreach([['GPA',$gpa],['Scholar',$student->is_scholar?'YES':'NO'],['Skills',$student->skills->count()],['Status',$student->enrollment_status]] as [$l,$v])
                <div><div style="font-size:10px;font-weight:700;opacity:.7;margin-bottom:2px;">{{ $l }}</div><div style="font-size:18px;font-weight:900;">{{ $v }}</div></div>
                @endforeach
            </div>
        </div>

        {{-- Admin actions only --}}
        @if($isAdmin)
        <div style="display:flex;gap:10px;">
            <a href="{{ route('students.edit',$student) }}" class="btn btn-primary" style="flex:1;justify-content:center;">✏️ Edit</a>
            <form method="POST" action="{{ route('students.destroy',$student) }}" onsubmit="return confirm('Delete this student?')" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">🗑 Delete</button>
            </form>
        </div>
        @endif
    </div>

    {{-- Right Column --}}
    <div>
        {{-- Tab Nav --}}
        @php $activeTab = request('tab','personal'); @endphp
        @php $showRoute = $isAdmin ? 'students.show' : 'student.my-profile'; @endphp
        <div class="card" style="padding:8px;display:flex;flex-wrap:wrap;gap:4px;margin-bottom:16px;">
            @foreach([['personal','👤','Personal'],['academic','📚','Academic'],['skills','⭐','Skills'],['extra','🏛','Organizations'],['medical','❤️','Medical'],['violations','⚠️','Violations']] as [$id,$em,$lbl])
            @php
                $tabUrl = $isAdmin
                    ? route('students.show', [$student, 'tab' => $id])
                    : route('student.my-profile') . '?tab=' . $id;
            @endphp
            <a href="{{ $tabUrl }}" class="tab-btn {{ $activeTab===$id?'active':'' }}">{{ $em }} {{ $lbl }}</a>
            @endforeach
        </div>

        {{-- Tab Content --}}
        <div class="card" style="padding:28px;min-height:400px;">
            @if($activeTab==='personal')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                @foreach([['Full Name',$student->full_name],['Gender',$student->gender],['Birthdate',$student->birthdate?->format('M d, Y')],['Civil Status',$student->civil_status],['Nationality',$student->nationality],['Admission Type',$student->admission_type],['Date Enrolled',$student->date_enrolled?->format('M d, Y')],['Section',$student->section],['Academic Year',$student->academic_year],['Semester',$student->semester]] as [$l,$v])
                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">{{ $l }}</div>
                    <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $v ?? 'N/A' }}</div>
                </div>
                @endforeach
            </div>

            @elseif($activeTab==='academic')
            @forelse($student->academicRecords as $rec)
            <div style="margin-bottom:28px;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:12px;border-bottom:1px solid #f1f5f9;margin-bottom:16px;">
                    <div style="font-weight:900;font-size:16px;color:#0f172a;">{{ $rec->academic_year }} — {{ $rec->semester }}</div>
                    <div style="display:flex;gap:16px;font-size:12px;font-weight:700;color:#64748b;">
                        <span>GPA: <span style="color:#22c55e;">{{ $rec->gpa }}</span></span>
                        <span>Units: {{ $rec->units_passed }}/{{ $rec->units_enrolled }}</span>
                    </div>
                </div>
                <table style="width:100%;border-collapse:collapse;border-radius:12px;overflow:hidden;border:1px solid #f1f5f9;">
                    <thead><tr>
                        @foreach(['Code','Subject','Grade','Units'] as $h)
                        <th style="padding:10px 16px;background:#f8fafc;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;text-align:left;">{{ $h }}</th>
                        @endforeach
                    </tr></thead>
                    <tbody>
                        @foreach($rec->grades as $g)
                        <tr style="border-top:1px solid #f8fafc;">
                            <td style="padding:10px 16px;font-size:13px;font-weight:700;color:#334155;">{{ $g->code }}</td>
                            <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $g->name }}</td>
                            <td style="padding:10px 16px;font-size:13px;font-weight:900;color:#f97316;">{{ $g->grade }}</td>
                            <td style="padding:10px 16px;font-size:13px;color:#64748b;">{{ $g->units }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @empty<div class="empty-state"><div style="font-size:40px;">📚</div><div style="font-weight:700;color:#475569;margin-top:8px;">No academic records found</div></div>@endforelse

            @elseif($activeTab==='skills')
            <div style="margin-bottom:24px;">
                <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:14px;">Skills</div>
                <div style="display:flex;flex-direction:column;gap:0;border:1px solid #f1f5f9;border-radius:12px;overflow:hidden;">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;background:#f8fafc;padding:10px 16px;">
                        @foreach(['Skill','Category','Proficiency'] as $h)<div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;">{{ $h }}</div>@endforeach
                    </div>
                    @forelse($student->skills as $sk)
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;padding:12px 16px;border-top:1px solid #f8fafc;">
                        <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $sk->name }}</div>
                        <div style="font-size:13px;color:#64748b;">{{ $sk->category }}</div>
                        <div><span class="badge {{ $sk->proficiency==='Advanced'?'badge-green':($sk->proficiency==='Intermediate'?'badge-blue':'badge-slate') }}">{{ $sk->proficiency }}</span></div>
                    </div>
                    @empty<div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">No skills recorded</div>@endforelse
                </div>
            </div>
            <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:14px;">Achievements</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                @forelse($student->achievements as $a)
                <div style="padding:14px;border-radius:12px;border:1px solid #f1f5f9;display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:#fffbeb;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;">🏆</div>
                    <div><div style="font-weight:700;font-size:13px;color:#0f172a;">{{ $a->type }}</div><div style="font-size:11px;color:#94a3b8;">{{ $a->level }} · {{ $a->date_received?->format('M Y') }}</div></div>
                </div>
                @empty<div style="color:#94a3b8;font-size:13px;grid-column:span 2;">No achievements recorded.</div>@endforelse
            </div>

            @elseif($activeTab==='extra')
            <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:14px;">Organizations</div>
            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:24px;">
                @forelse($student->organizations as $org)
                <div style="padding:14px 16px;border-radius:12px;border:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;">🏛</div>
                        <div><div style="font-weight:700;font-size:13px;color:#0f172a;">{{ $org->name }}</div><div style="font-size:11px;color:#94a3b8;">{{ $org->position }} · {{ $org->academic_year }}</div></div>
                    </div>
                    <span class="badge {{ $org->status==='Active'?'badge-green':'badge-slate' }}">{{ $org->status }}</span>
                </div>
                @empty<div style="color:#94a3b8;font-size:13px;">No organizations recorded.</div>@endforelse
            </div>
            <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:14px;">Internship / OJT</div>
            @if($student->internship)
            @php $int=$student->internship; @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:16px;border-radius:12px;border:1px solid #f1f5f9;">
                @foreach([['Company',$int->company_name],['Role',$int->role],['Duration',$int->duration],['Status',$int->status]] as [$l,$v])
                <div><div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">{{ $l }}</div><div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $v }}</div></div>
                @endforeach
            </div>
            @else<div style="color:#94a3b8;font-size:13px;">No internship recorded.</div>@endif

            @elseif($activeTab==='medical')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px;">
                <div>
                    <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:16px;">Medical Information</div>
                    @if($student->medical)
                    @php $m=$student->medical; @endphp
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        @foreach([['Blood Type',$m->blood_type],['Allergies',is_array($m->allergies)?implode(', ',$m->allergies):'None'],['Conditions',is_array($m->conditions)?implode(', ',$m->conditions):'None'],['Emergency Contact',$m->emergency_contact],['Emergency Number',$m->emergency_number]] as [$l,$v])
                        <div><div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">{{ $l }}</div><div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $v }}</div></div>
                        @endforeach
                    </div>
                    @else<div style="color:#94a3b8;font-size:13px;">No medical data recorded.</div>@endif
                </div>
                <div>
                    <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:16px;">Guardian Information</div>
                    @if($student->guardian)
                    @php $g=$student->guardian; @endphp
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        @foreach([['Name',$g->name],['Relationship',$g->relationship],['Contact',$g->contact_number],['Address',$g->address]] as [$l,$v])
                        <div><div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">{{ $l }}</div><div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $v }}</div></div>
                        @endforeach
                    </div>
                    @else<div style="color:#94a3b8;font-size:13px;">No guardian data recorded.</div>@endif
                </div>
            </div>

            @elseif($activeTab==='violations')
            <div style="font-size:16px;font-weight:900;color:#0f172a;margin-bottom:16px;">Violation Records</div>
            @forelse($student->violations as $v)
            <div style="padding:16px;border-radius:12px;border:1.5px solid #fee2e2;background:#fef2f2;display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <div>
                    <div style="font-weight:700;font-size:13px;color:#0f172a;">{{ $v->category }} — {{ $v->sanction }}</div>
                    <div style="font-size:12px;color:#94a3b8;">Reported by {{ $v->reported_by }} · {{ $v->date_reported?->format('M d, Y') }}</div>
                </div>
                <span class="badge {{ $v->status==='Resolved'?'badge-green':'badge-red' }}">{{ $v->status }}</span>
            </div>
            @empty
            <div class="empty-state">
                <div style="font-size:48px;margin-bottom:10px;">✅</div>
                <div style="font-weight:700;font-size:15px;color:#475569;">No violations on record</div>
                <div style="font-size:13px;color:#94a3b8;margin-top:4px;">This student has a clean record</div>
            </div>
            @endforelse
            @endif
        </div>
    </div>
</div>
@endif
@endsection
