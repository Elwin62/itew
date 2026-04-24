@extends('layouts.app')
@section('title', $skill . ' Skill Query')
@section('page-title', $skill . ' Skill Query')
@section('page-subtitle', number_format($total) . ' students found with this skill')

@section('content')

{{-- Toggle Buttons --}}
<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:24px;align-items:center;">
    <a href="{{ route('queries.basketball') }}" class="btn {{ $skill==='Basketball'?'btn-primary':'btn-secondary' }}">🏀 Basketball Skills</a>
    <a href="{{ route('queries.programming') }}" class="btn {{ $skill==='Programming'?'btn-blue':'btn-secondary' }}">💻 Programming Skills</a>
    <a href="{{ route('students.index') }}" class="btn btn-secondary" style="margin-left:auto;">← All Students</a>
</div>

{{-- Banner --}}
<div style="padding:24px 28px;border-radius:20px;background:linear-gradient(135deg,#fff7ed,#ffedd5);border:1.5px solid #fed7aa;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;">
    <div>
        <div style="font-size:36px;font-weight:900;color:#0f172a;">{{ number_format($total) }} <span style="font-size:18px;color:#94a3b8;font-weight:600;">students</span></div>
        <div style="font-size:14px;color:#64748b;font-weight:600;">have <strong style="color:#f97316;">{{ $skill }}</strong> as a skill</div>
    </div>
    <div style="font-size:80px;opacity:.15;">{{ $skill==='Basketball'?'🏀':'💻' }}</div>
</div>

{{-- Card Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:28px;">
    @foreach($students as $student)
    <div class="card" style="overflow:hidden;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 8px 30px rgba(249,115,22,.12)'" onmouseout="this.style.boxShadow=''">
        <div style="height:64px;background:linear-gradient(135deg,#f97316,#fb923c);position:relative;">
            <div style="position:absolute;bottom:-22px;left:16px;">
                <img src="{{ $student->profile_photo }}" style="width:48px;height:48px;border-radius:12px;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.1);" alt="">
            </div>
        </div>
        <div style="padding:28px 16px 16px;">
            <div style="font-weight:800;font-size:14px;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $student->full_name }}</div>
            <div style="font-size:11px;color:#f97316;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">{{ $student->student_id }}</div>
            <div style="font-size:11px;color:#64748b;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $student->academic_program }}</div>
            <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:4px;">
                @foreach($student->skills->where('name',$skill) as $s)
                <span class="badge badge-orange">{{ $s->proficiency }}</span>
                @endforeach
            </div>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                @if($student->enrollment_status==='Enrolled')<span class="badge badge-green">{{ $student->enrollment_status }}</span>
                @else<span class="badge badge-slate">{{ $student->enrollment_status }}</span>@endif
                <a href="{{ route('students.show',$student) }}" style="font-size:12px;font-weight:700;color:#3b82f6;text-decoration:none;">View →</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Table View --}}
<div class="card" style="overflow:hidden;">
    <div style="padding:16px 24px;border-bottom:1px solid #f1f5f9;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;">Table View</div>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th class="table-th" style="text-align:left;">#</th>
                <th class="table-th" style="text-align:left;">Student</th>
                <th class="table-th" style="text-align:left;">Program</th>
                <th class="table-th" style="text-align:left;">Year</th>
                <th class="table-th" style="text-align:left;">Proficiency</th>
                <th class="table-th" style="text-align:left;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $i => $student)
            <tr>
                <td class="table-td" style="font-size:12px;color:#94a3b8;font-weight:700;">{{ ($students->currentPage()-1)*$students->perPage()+$i+1 }}</td>
                <td class="table-td">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <img src="{{ $student->profile_photo }}" style="width:34px;height:34px;border-radius:10px;object-fit:cover;" alt="">
                        <div>
                            <div style="font-weight:700;font-size:13px;color:#0f172a;">{{ $student->full_name }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $student->student_id }}</div>
                        </div>
                    </div>
                </td>
                <td class="table-td" style="font-size:13px;color:#475569;">{{ $student->academic_program }}</td>
                <td class="table-td" style="font-size:13px;color:#475569;">Year {{ $student->year_level }}</td>
                <td class="table-td">
                    @foreach($student->skills->where('name',$skill) as $s)
                    <span class="badge badge-orange">{{ $s->proficiency }}</span>
                    @endforeach
                </td>
                <td class="table-td"><a href="{{ route('students.show',$student) }}" style="font-size:13px;font-weight:700;color:#3b82f6;text-decoration:none;">View →</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:16px 24px;border-top:1px solid #f8fafc;">{{ $students->links() }}</div>
</div>

@endsection
