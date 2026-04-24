@extends('layouts.app')
@section('title','Admin Panel')
@section('page-title','Admin Control Center')
@section('page-subtitle','System management, activity logs, and database tools')
@section('content')

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px;">
@foreach([['Total Users','👥',$stats['total_users'],'#6366f1','#eef2ff'],['Students','🎓',$stats['total_students'],'#3b82f6','#dbeafe'],['Faculty','👨‍🏫',$stats['total_faculty'],'#8b5cf6','#f3e8ff'],['Log Entries','📋',$stats['total_logs'],'#f97316','#fff7ed']] as [$l,$ico,$v,$c,$bg])
<div class="stat-card" style="border-top:4px solid {{$c}};">
    <div style="width:48px;height:48px;background:{{$bg}};border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px;">{{$ico}}</div>
    <div style="font-size:32px;font-weight:900;color:#0f172a;">{{ number_format($v) }}</div>
    <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-top:6px;">{{$l}}</div>
</div>
@endforeach
</div>

{{-- DB Tools --}}
<div class="card" style="padding:24px;margin-bottom:28px;">
    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">🗄 Database Management</div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:12px;">
        <form method="POST" action="{{ route('admin.seed') }}" onsubmit="return confirm('This replaces ALL data with 1,000+ fresh records. Continue?')">
            @csrf
            <button type="submit" class="btn" style="background:#8b5cf6;color:#fff;box-shadow:0 4px 14px rgba(139,92,246,.3);">⚡ Re-seed Database (1,000+ Records)</button>
        </form>
        <a href="{{ route('queries.basketball') }}" class="btn btn-primary">🏀 Basketball Query</a>
        <a href="{{ route('queries.programming') }}" class="btn btn-blue">💻 Programming Query</a>
    </div>
    <p style="font-size:12px;color:#f59e0b;font-weight:600;">⚠️ Seeding drops all existing data and regenerates 800 students + 200 faculty with realistic records.</p>
</div>

{{-- Activity Logs --}}
<div class="card" style="overflow:hidden;">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div style="font-weight:800;font-size:16px;color:#0f172a;">System Activity Logs</div>
        <form method="GET" action="{{ route('admin.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;">
            <div style="position:relative;">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." class="form-input" style="padding-left:40px;width:220px;">
            </div>
            <select name="module" class="form-input" style="width:160px;">
                <option value="All">All Modules</option>
                @foreach($modules as $m)<option value="{{ $m }}" {{ request('module')===$m?'selected':'' }}>{{ $m }}</option>@endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th class="table-th" style="text-align:left;width:50px;">Status</th>
                <th class="table-th" style="text-align:left;">User</th>
                <th class="table-th" style="text-align:left;">Action</th>
                <th class="table-th" style="text-align:left;">Target</th>
                <th class="table-th" style="text-align:left;">Module</th>
                <th class="table-th" style="text-align:left;">Time</th>
                <th class="table-th" style="text-align:center;width:50px;">Del</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td class="table-td" style="font-size:18px;">{{ $log->status==='Success'?'✅':($log->status==='Failed'?'❌':'⚠️') }}</td>
                <td class="table-td">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:30px;height:30px;border-radius:8px;background:#fff7ed;color:#f97316;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:12px;">{{ strtoupper(substr($log->user_name,0,1)) }}</div>
                        <span style="font-size:13px;font-weight:600;color:#334155;">{{ $log->user_name }}</span>
                    </div>
                </td>
                <td class="table-td" style="font-size:13px;color:#475569;">{{ $log->action }}</td>
                <td class="table-td" style="font-size:13px;font-weight:600;color:#0f172a;">{{ Str::limit($log->target,30) }}</td>
                <td class="table-td"><span class="badge badge-slate">{{ $log->module }}</span></td>
                <td class="table-td" style="font-size:12px;color:#94a3b8;">{{ $log->created_at->format('M d, Y H:i') }}</td>
                <td class="table-td" style="text-align:center;">
                    <form method="POST" action="{{ route('admin.logs.destroy',$log) }}" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="width:28px;height:28px;border-radius:7px;background:#fef2f2;color:#ef4444;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;margin:auto;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><div style="font-size:48px;margin-bottom:12px;">📋</div><div style="font-weight:700;font-size:16px;color:#475569;">No logs found</div></div></td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 24px;border-top:1px solid #f8fafc;">{{ $logs->links() }}</div>
</div>
@endsection
