@extends('layouts.app')
@section('title','Faculty')
@section('page-title','Faculty Management')
@section('page-subtitle','Manage academic staff and professors')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <form method="GET" action="{{ route('faculty.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;">
        <div style="position:relative;">
            <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or ID..." class="form-input" style="padding-left:40px;width:260px;">
        </div>
        <select name="department" class="form-input" style="width:200px;">
            <option value="All">All Departments</option>
            @foreach($departments as $d)<option value="{{ $d }}" {{ request('department')===$d?'selected':'' }}>{{ Str::limit($d,30) }}</option>@endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('faculty.index') }}" class="btn btn-secondary">Reset</a>
    </form>
    <a href="{{ route('faculty.create') }}" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Faculty
    </a>
</div>

<div class="card" style="overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th class="table-th" style="text-align:left;">Professor</th>
                <th class="table-th" style="text-align:left;">Department</th>
                <th class="table-th" style="text-align:left;">Rank</th>
                <th class="table-th" style="text-align:left;">Status</th>
                <th class="table-th" style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($faculty as $f)
            <tr>
                <td class="table-td">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="{{ $f->profile_photo }}" class="avatar" style="width:42px;height:42px;" alt="">
                        <div>
                            <div style="font-weight:700;font-size:14px;color:#0f172a;">{{ $f->full_name }}</div>
                            <div style="font-size:12px;color:#94a3b8;">{{ $f->faculty_id }}</div>
                        </div>
                    </div>
                </td>
                <td class="table-td" style="font-size:13px;color:#475569;">{{ Str::limit($f->department,35) }}</td>
                <td class="table-td" style="font-size:13px;color:#475569;">{{ $f->academic_rank }}</td>
                <td class="table-td">
                    @if($f->employment_status==='Full-time')<span class="badge badge-green">{{ $f->employment_status }}</span>
                    @elseif($f->employment_status==='Part-time')<span class="badge badge-orange">{{ $f->employment_status }}</span>
                    @else<span class="badge badge-slate">{{ $f->employment_status }}</span>@endif
                </td>
                <td class="table-td" style="text-align:center;">
                    <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                        <a href="{{ route('faculty.show',$f) }}" style="width:32px;height:32px;border-radius:8px;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center;text-decoration:none;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('faculty.edit',$f) }}" style="width:32px;height:32px;border-radius:8px;background:#fffbeb;color:#f59e0b;display:flex;align-items:center;justify-content:center;text-decoration:none;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('faculty.destroy',$f) }}" onsubmit="return confirm('Delete {{ $f->full_name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="width:32px;height:32px;border-radius:8px;background:#fef2f2;color:#ef4444;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state"><div style="font-size:48px;margin-bottom:12px;">👨‍🏫</div><div style="font-weight:700;font-size:16px;color:#475569;">No faculty found</div></div></td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 24px;border-top:1px solid #f8fafc;">{{ $faculty->links() }}</div>
</div>
@endsection
