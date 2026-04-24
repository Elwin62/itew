@extends('layouts.app')
@section('title','Students')
@section('page-title','Student Management')
@section('page-subtitle','Search, filter and manage all student records')

@section('content')

{{-- Header Actions --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('queries.basketball') }}" class="btn btn-primary">🏀 Basketball Query</a>
        <a href="{{ route('queries.programming') }}" class="btn btn-blue">💻 Programming Query</a>
    </div>
    <a href="{{ route('students.create') }}" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Student
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('students.index') }}" class="card" style="padding:20px;margin-bottom:24px;">
    <div style="display:grid;grid-template-columns:2fr 1fr 1fr auto auto;gap:12px;align-items:end;">
        <div>
            <label class="form-label">Search</label>
            <div style="position:relative;">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or ID..." class="form-input" style="padding-left:40px;">
            </div>
        </div>
        <div>
            <label class="form-label">Program</label>
            <select name="program" class="form-input">
                <option value="All">All Programs</option>
                @foreach($programs as $p)<option value="{{ $p }}" {{ request('program')===$p?'selected':'' }}>{{ $p }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Skill</label>
            <select name="skill" class="form-input">
                <option value="All">All Skills</option>
                @foreach($skills as $s)<option value="{{ $s }}" {{ request('skill')===$s?'selected':'' }}>{{ $s }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">&nbsp;</label><button type="submit" class="btn btn-primary" style="width:100%;">Filter</button></div>
        <div><label class="form-label">&nbsp;</label><a href="{{ route('students.index') }}" class="btn btn-secondary" style="width:100%;">Reset</a></div>
    </div>
</form>

{{-- Count --}}
<div style="margin-bottom:12px;font-size:13px;font-weight:600;color:#64748b;">
    Showing <strong style="color:#f97316;">{{ $students->count() }}</strong> of <strong style="color:#f97316;">{{ $students->total() }}</strong> students
    @php $skillFilter = request('skill'); @endphp
    @if($skillFilter && $skillFilter !== 'All') with <strong style="color:#f97316;">{{ $skillFilter }}</strong> skill @endif
</div>

{{-- Table --}}
<div class="card" style="overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th class="table-th" style="text-align:left;">Student</th>
                <th class="table-th" style="text-align:left;">Program</th>
                <th class="table-th" style="text-align:left;">Year / Section</th>
                <th class="table-th" style="text-align:left;">Skills</th>
                <th class="table-th" style="text-align:left;">Status</th>
                <th class="table-th" style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td class="table-td">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="{{ $student->profile_photo }}" class="avatar" style="width:42px;height:42px;" alt="">
                        <div>
                            <div style="font-weight:700;font-size:14px;color:#0f172a;">{{ $student->full_name }}</div>
                            <div style="font-size:12px;color:#94a3b8;font-weight:500;">{{ $student->student_id }}</div>
                        </div>
                    </div>
                </td>
                <td class="table-td" style="font-size:13px;color:#475569;">{{ $student->academic_program }}</td>
                <td class="table-td" style="font-size:13px;color:#475569;">Year {{ $student->year_level }} — {{ $student->section }}</td>
                <td class="table-td">
                    <div style="display:flex;flex-wrap:wrap;gap:4px;">
                        @foreach($student->skills->take(2) as $skill)
                        <span class="badge badge-slate">{{ $skill->name }}</span>
                        @endforeach
                        @if($student->skills->count()>2)<span style="font-size:11px;color:#94a3b8;font-weight:600;">+{{ $student->skills->count()-2 }}</span>@endif
                    </div>
                </td>
                <td class="table-td">
                    @if($student->enrollment_status === 'Enrolled')
                        <span class="badge badge-green">{{ $student->enrollment_status }}</span>
                    @elseif($student->enrollment_status === 'Graduated')
                        <span class="badge badge-blue">{{ $student->enrollment_status }}</span>
                    @else
                        <span class="badge badge-slate">{{ $student->enrollment_status }}</span>
                    @endif
                </td>
                <td class="table-td" style="text-align:center;">
                    <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                        <a href="{{ route('students.show',$student) }}" style="width:32px;height:32px;border-radius:8px;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center;text-decoration:none;" title="View">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('students.edit',$student) }}" style="width:32px;height:32px;border-radius:8px;background:#fffbeb;color:#f59e0b;display:flex;align-items:center;justify-content:center;text-decoration:none;" title="Edit">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('students.destroy',$student) }}" onsubmit="return confirm('Delete {{ $student->full_name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="width:32px;height:32px;border-radius:8px;background:#fef2f2;color:#ef4444;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;" title="Delete">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6">
                <div class="empty-state">
                    <div style="font-size:48px;margin-bottom:12px;">🔍</div>
                    <div style="font-weight:700;font-size:16px;color:#475569;">No students found</div>
                    <div style="font-size:13px;color:#94a3b8;margin-top:4px;">Try adjusting your search or filters</div>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 24px;border-top:1px solid #f8fafc;">{{ $students->links() }}</div>
</div>

@endsection
