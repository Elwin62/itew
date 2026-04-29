@extends('layouts.app')
@section('title','System Reports')
@section('page-title','System Reports')
@section('page-subtitle','Comprehensive institutional analytics & data exports')
@section('content')

{{-- Filter & Download Bar --}}
<div class="card" style="padding:20px 24px;margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <div>
            <div style="font-size:16px;font-weight:900;color:#0f172a;">📊 System Reports</div>
            <div style="font-size:12px;color:#94a3b8;">Generated {{ now()->format('M d, Y h:i A') }}</div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <a href="{{ route('reports.admin.download',['type'=>'students']) }}" class="btn btn-primary" style="font-size:12px;">📥 Students</a>
            <a href="{{ route('reports.admin.download',['type'=>'faculty']) }}" class="btn btn-blue" style="font-size:12px;">📥 Faculty</a>
            <a href="{{ route('reports.admin.download',['type'=>'enrollment']) }}" class="btn" style="background:#22c55e;color:#fff;font-size:12px;">📥 Enrollment</a>
            <a href="{{ route('reports.admin.download',['type'=>'attendance']) }}" class="btn" style="background:#8b5cf6;color:#fff;font-size:12px;">📥 Attendance</a>
            <a href="{{ route('reports.admin.download',['type'=>'logs']) }}" class="btn" style="background:#64748b;color:#fff;font-size:12px;">📥 Logs</a>
            <button onclick="window.print()" class="btn btn-secondary no-print" style="font-size:12px;">🖨 Print</button>
        </div>
    </div>
    {{-- Filters --}}
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:end;" class="no-print">
        <div>
            <label class="form-label">Program</label>
            <select name="program" class="form-input" style="width:220px;" onchange="this.form.submit()">
                <option value="">All Programs</option>
                @foreach($programs as $p)<option value="{{ $p }}" {{ $programFilter===$p?'selected':'' }}>{{ $p }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Year Level</label>
            <select name="year_level" class="form-input" style="width:140px;" onchange="this.form.submit()">
                <option value="">All Years</option>
                @foreach([1,2,3,4] as $y)<option value="{{ $y }}" {{ $yearFilter==$y?'selected':'' }}>Year {{ $y }}</option>@endforeach
            </select>
        </div>
        @if($programFilter || $yearFilter)
        <a href="{{ route('reports.admin') }}" class="btn btn-secondary" style="font-size:12px;">✕ Clear Filters</a>
        @endif
    </form>
</div>

{{-- Stat Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:14px;margin-bottom:24px;">
@foreach([
    ['Total Students', $totalStudents,'🎓','#3b82f6','#dbeafe'],
    ['Enrolled', $enrolled,'✅','#22c55e','#dcfce7'],
    ['Faculty', $totalFaculty,'👨‍🏫','#8b5cf6','#f3e8ff'],
    ['Scholars', $scholars,'🏆','#f97316','#ffedd5'],
    ['At-Risk', $atRiskCount,'⚠️','#ef4444','#fee2e2'],
    ['Events', $totalEvents,'📅','#14b8a6','#ccfbf1'],
] as [$l,$v,$ico,$c,$bg])
<div class="stat-card" style="border-top:4px solid {{$c}};">
    <div style="width:40px;height:40px;background:{{$bg}};border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:12px;">{{$ico}}</div>
    <div style="font-size:24px;font-weight:900;color:#0f172a;">{{ number_format($v) }}</div>
    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-top:4px;">{{$l}}</div>
</div>
@endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Enrollment by Program --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">🎓 Enrollment by Program</div>
        <canvas id="programChart" height="200"></canvas>
    </div>
    {{-- Gender Distribution --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">👤 Gender Distribution</div>
        <div style="display:flex;align-items:center;gap:24px;">
            <div style="width:180px;height:180px;"><canvas id="genderChart"></canvas></div>
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach($genderDist as $g)
                @php $gpct=$totalStudents>0?round(($g->count/$totalStudents)*100):0; $gc=$g->gender==='Male'?'#3b82f6':'#ec4899'; @endphp
                <div>
                    <div style="font-size:28px;font-weight:900;color:{{$gc}};">{{ $gpct }}%</div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;">{{ $g->gender }} · {{ number_format($g->count) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Enrollment Status --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📋 Enrollment Status Distribution</div>
        <canvas id="enrollStatusChart" height="200"></canvas>
    </div>
    {{-- Year Level Distribution --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📚 Year Level Breakdown</div>
        <canvas id="yearLevelChart" height="200"></canvas>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Attendance Trends --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📋 School-wide Attendance Trends</div>
        <div style="display:flex;align-items:center;gap:24px;">
            <div style="width:180px;height:180px;"><canvas id="attendChart"></canvas></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @php $at=$attendanceStats; $atPct=$at['total']>0?round(($at['present']/$at['total'])*100):0; @endphp
                <div><div style="font-size:28px;font-weight:900;color:#22c55e;">{{ $atPct }}%</div><div style="font-size:11px;font-weight:700;color:#94a3b8;">Present Rate</div></div>
                @foreach([['Present',$at['present'],'#22c55e'],['Late',$at['late'],'#f59e0b'],['Absent',$at['absent'],'#ef4444']] as [$sl,$sv,$sc])
                <div style="display:flex;align-items:center;gap:8px;"><div style="width:10px;height:10px;border-radius:3px;background:{{$sc}};"></div><span style="font-size:12px;font-weight:700;color:#0f172a;">{{ number_format($sv) }}</span><span style="font-size:11px;color:#94a3b8;">{{ $sl }}</span></div>
                @endforeach
            </div>
        </div>
    </div>
    {{-- Assignment Stats --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📝 Assignment Submission Overview</div>
        <div style="display:flex;align-items:center;gap:24px;">
            <div style="width:180px;height:180px;"><canvas id="assignChart"></canvas></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @php $as=$assignmentStats; @endphp
                <div><div style="font-size:28px;font-weight:900;color:#3b82f6;">{{ $as['avgScore'] }}</div><div style="font-size:11px;font-weight:700;color:#94a3b8;">Avg Score</div></div>
                @foreach([['Submitted',$as['submitted'],'#22c55e'],['Late',$as['late'],'#f59e0b'],['Missing',$as['missing'],'#ef4444']] as [$sl,$sv,$sc])
                <div style="display:flex;align-items:center;gap:8px;"><div style="width:10px;height:10px;border-radius:3px;background:{{$sc}};"></div><span style="font-size:12px;font-weight:700;">{{ number_format($sv) }}</span><span style="font-size:11px;color:#94a3b8;">{{ $sl }}</span></div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Faculty by Department + Top Students --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">🏛 Faculty by Department</div>
        <canvas id="deptChart" height="200"></canvas>
    </div>
    <div class="card" style="overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;">🌟 Top Performing Students (by GPA)</div>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead><tr><th class="table-th" style="text-align:center;">#</th><th class="table-th" style="text-align:left;">Student</th><th class="table-th" style="text-align:left;">Program</th><th class="table-th" style="text-align:center;">GPA</th></tr></thead>
                <tbody>
                @foreach($topStudents->take(10) as $i => $ts)
                @if($ts->student)
                <tr>
                    <td class="table-td" style="text-align:center;font-weight:900;color:{{ $i<3?'#f97316':'#94a3b8' }};">{{ $i+1 }}</td>
                    <td class="table-td" style="font-size:13px;font-weight:700;">{{ $ts->student->full_name }}<div style="font-size:10px;color:#94a3b8;">{{ $ts->student->student_id }}</div></td>
                    <td class="table-td" style="font-size:12px;color:#64748b;">{{ $ts->student->academic_program }}</td>
                    <td class="table-td" style="text-align:center;font-size:18px;font-weight:900;color:#22c55e;">{{ round($ts->avg_gpa,2) }}</td>
                </tr>
                @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Faculty by Rank --}}
<div class="card" style="padding:24px;">
    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">🎓 Faculty by Academic Rank</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
        @php $rc=['#3b82f6','#8b5cf6','#f97316','#22c55e','#ec4899','#14b8a6','#f59e0b','#ef4444']; @endphp
        @foreach($facultyRankDist as $i => $r)
        <div style="padding:16px;border-radius:14px;background:#f8fafc;border-left:4px solid {{ $rc[$i%count($rc)] }};">
            <div style="font-size:24px;font-weight:900;color:{{ $rc[$i%count($rc)] }};">{{ $r->count }}</div>
            <div style="font-size:12px;font-weight:700;color:#475569;margin-top:4px;">{{ $r->academic_rank }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Charts --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
    const colors = ['#3b82f6','#8b5cf6','#f97316','#22c55e','#ec4899','#14b8a6','#f59e0b','#ef4444'];
    // Program
    new Chart(document.getElementById('programChart'),{type:'bar',data:{labels:{!! json_encode($programDist->pluck('academic_program')) !!},datasets:[{data:{!! json_encode($programDist->pluck('count')) !!},backgroundColor:colors,borderRadius:8}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
    // Gender
    new Chart(document.getElementById('genderChart'),{type:'doughnut',data:{labels:{!! json_encode($genderDist->pluck('gender')) !!},datasets:[{data:{!! json_encode($genderDist->pluck('count')) !!},backgroundColor:['#3b82f6','#ec4899'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{display:false}}}});
    // Enrollment Status
    @php $esLabels=$enrollmentDist->pluck('enrollment_status'); $esData=$enrollmentDist->pluck('count'); @endphp
    new Chart(document.getElementById('enrollStatusChart'),{type:'doughnut',data:{labels:{!! json_encode($esLabels) !!},datasets:[{data:{!! json_encode($esData) !!},backgroundColor:colors,borderWidth:0}]},options:{responsive:true,cutout:'55%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}});
    // Year Level
    new Chart(document.getElementById('yearLevelChart'),{type:'bar',data:{labels:{!! json_encode($yearLevelDist->map(fn($y)=>'Year '.$y->year_level)) !!},datasets:[{data:{!! json_encode($yearLevelDist->pluck('count')) !!},backgroundColor:['#3b82f6','#8b5cf6','#f97316','#22c55e'],borderRadius:8}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
    // Attendance
    new Chart(document.getElementById('attendChart'),{type:'doughnut',data:{labels:['Present','Late','Absent'],datasets:[{data:[{{ $attendanceStats['present'] }},{{ $attendanceStats['late'] }},{{ $attendanceStats['absent'] }}],backgroundColor:['#22c55e','#f59e0b','#ef4444'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{display:false}}}});
    // Assignment
    new Chart(document.getElementById('assignChart'),{type:'doughnut',data:{labels:['Submitted','Late','Missing'],datasets:[{data:[{{ $assignmentStats['submitted'] }},{{ $assignmentStats['late'] }},{{ $assignmentStats['missing'] }}],backgroundColor:['#22c55e','#f59e0b','#ef4444'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{display:false}}}});
    // Department
    new Chart(document.getElementById('deptChart'),{type:'bar',data:{labels:{!! json_encode($facultyDeptDist->pluck('department')->map(fn($d)=>Str::limit($d,25))) !!},datasets:[{data:{!! json_encode($facultyDeptDist->pluck('count')) !!},backgroundColor:colors,borderRadius:8}]},options:{responsive:true,indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{beginAtZero:true}}}});
});
</script>
@endsection
