@extends('layouts.app')
@section('title','Faculty Reports')
@section('page-title','Faculty Reports')
@section('page-subtitle','Class performance analytics, attendance, and assignment tracking')
@section('content')

{{-- Download Bar --}}
<div class="card" style="padding:20px 24px;margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:16px;font-weight:900;color:#0f172a;">📊 Teaching Reports</div>
            <div style="font-size:12px;color:#94a3b8;">{{ $user->name }} · Generated {{ now()->format('M d, Y h:i A') }}</div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <a href="{{ route('reports.faculty.download',['type'=>'schedule']) }}" class="btn btn-primary" style="font-size:12px;">📥 Schedule CSV</a>
            <a href="{{ route('reports.faculty.download',['type'=>'grades']) }}" class="btn btn-blue" style="font-size:12px;">📥 Student Grades CSV</a>
            <a href="{{ route('reports.faculty.download',['type'=>'attendance']) }}" class="btn" style="background:#22c55e;color:#fff;font-size:12px;">📥 Attendance CSV</a>
            <button onclick="window.print()" class="btn btn-secondary no-print" style="font-size:12px;">🖨 Print</button>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:24px;">
@foreach([
    ['My Sections', $sections->count(),'📋','#3b82f6','#dbeafe'],
    ['Total Classes', $schedules->count(),'📚','#8b5cf6','#f3e8ff'],
    ['Students Taught', $sectionStudentCounts->sum(),'👥','#f97316','#ffedd5'],
    ['Subjects', $subjectCodes->count(),'📝','#14b8a6','#ccfbf1'],
    ['Passed', $passCount,'✅','#22c55e','#dcfce7'],
    ['At-Risk', count($atRiskStudents),'⚠️','#ef4444','#fee2e2'],
] as [$l,$v,$ico,$c,$bg])
<div class="stat-card" style="border-top:4px solid {{$c}};">
    <div style="width:40px;height:40px;background:{{$bg}};border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:12px;">{{$ico}}</div>
    <div style="font-size:24px;font-weight:900;color:#0f172a;">{{$v}}</div>
    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-top:4px;">{{$l}}</div>
</div>
@endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Grade Analytics by Section --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📊 Performance Analytics by Section</div>
        <canvas id="gradeChart" height="220"></canvas>
    </div>
    {{-- Pass/Fail Distribution --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📈 Grade Distribution (Pass/Fail)</div>
        <div style="display:flex;align-items:center;gap:24px;">
            <div style="width:200px;height:200px;"><canvas id="passFailChart"></canvas></div>
            <div>
                <div style="margin-bottom:12px;"><div style="font-size:32px;font-weight:900;color:#22c55e;">{{ $passCount }}</div><div style="font-size:11px;font-weight:700;color:#94a3b8;">PASSED (GPA ≤ 3.0)</div></div>
                <div><div style="font-size:32px;font-weight:900;color:#ef4444;">{{ $failCount }}</div><div style="font-size:11px;font-weight:700;color:#94a3b8;">FAILING (GPA > 3.0)</div></div>
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Attendance Summary per Subject --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📋 Attendance Summary per Subject</div>
        <canvas id="attendChart" height="220"></canvas>
    </div>
    {{-- Section Enrollment Cards --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">👥 Class List & Enrolled Students</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;">
        @foreach($sections as $sec)
        <div style="padding:16px;border-radius:14px;background:#eff6ff;border:1.5px solid #bfdbfe;text-align:center;">
            <div style="font-size:12px;font-weight:800;background:#3b82f6;color:#fff;padding:3px 10px;border-radius:99px;display:inline-block;">{{ $sec }}</div>
            <div style="font-size:28px;font-weight:900;color:#1d4ed8;margin-top:8px;">{{ $sectionStudentCounts[$sec] ?? 0 }}</div>
            <div style="font-size:10px;color:#64748b;margin-top:2px;">students</div>
            @if(isset($gradeAnalytics[$sec]))
            <div style="font-size:10px;color:#94a3b8;margin-top:6px;">Avg GPA: <strong style="color:#3b82f6;">{{ $gradeAnalytics[$sec]['avg'] }}</strong></div>
            @endif
        </div>
        @endforeach
        </div>
    </div>
</div>

{{-- Assignment Tracking --}}
<div class="card" style="overflow:hidden;margin-bottom:24px;">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;">📝 Assignment Submission Tracking</div>
    </div>
    <div style="overflow-x:auto;max-height:420px;overflow-y:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr>
                <th class="table-th" style="text-align:left;">Title</th>
                <th class="table-th" style="text-align:center;">Type</th>
                <th class="table-th" style="text-align:left;">Subject</th>
                <th class="table-th" style="text-align:center;">Section</th>
                <th class="table-th" style="text-align:center;">Points</th>
                <th class="table-th" style="text-align:center;">✅ Submitted</th>
                <th class="table-th" style="text-align:center;">⏰ Late</th>
                <th class="table-th" style="text-align:center;">❌ Missing</th>
                <th class="table-th" style="text-align:center;">Avg Score</th>
            </tr></thead>
            <tbody>
            @foreach($assignmentSummary->take(25) as $a)
            <tr>
                <td class="table-td" style="font-size:12px;font-weight:700;">{{ Str::limit($a['title'],30) }}</td>
                <td class="table-td" style="text-align:center;"><span class="badge badge-blue" style="font-size:9px;">{{ $a['type'] }}</span></td>
                <td class="table-td" style="font-size:12px;color:#f97316;font-weight:700;">{{ $a['subject'] }}</td>
                <td class="table-td" style="text-align:center;font-size:12px;font-weight:700;">{{ $a['section'] }}</td>
                <td class="table-td" style="text-align:center;font-size:12px;">{{ $a['total_points'] }}</td>
                <td class="table-td" style="text-align:center;font-weight:800;color:#22c55e;">{{ $a['submitted'] }}</td>
                <td class="table-td" style="text-align:center;font-weight:800;color:#f59e0b;">{{ $a['late'] }}</td>
                <td class="table-td" style="text-align:center;font-weight:800;color:#ef4444;">{{ $a['missing'] }}</td>
                <td class="table-td" style="text-align:center;font-weight:800;color:#3b82f6;">{{ $a['avg_score'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- At-Risk Students --}}
@if(count($atRiskStudents))
<div class="card" style="overflow:hidden;">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;background:#fef2f2;">
        <div style="font-size:11px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:.1em;">⚠️ At-Risk Students (Low GPA or High Absence Rate)</div>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr>
                <th class="table-th" style="text-align:left;">Student</th>
                <th class="table-th" style="text-align:left;">ID</th>
                <th class="table-th" style="text-align:center;">Section</th>
                <th class="table-th" style="text-align:center;">GPA</th>
                <th class="table-th" style="text-align:center;">Absence Rate</th>
                <th class="table-th" style="text-align:center;">Risk Level</th>
            </tr></thead>
            <tbody>
            @foreach(collect($atRiskStudents)->take(15) as $s)
            @php $risk = ($s['gpa']>3.0 && $s['abs_rate']>20) ? 'High' : (($s['gpa']>2.5||$s['abs_rate']>20) ? 'Medium' : 'Low'); @endphp
            <tr>
                <td class="table-td" style="font-size:13px;font-weight:700;">{{ $s['name'] }}</td>
                <td class="table-td" style="font-size:12px;color:#64748b;">{{ $s['id'] }}</td>
                <td class="table-td" style="text-align:center;font-weight:700;">{{ $s['section'] }}</td>
                <td class="table-td" style="text-align:center;font-weight:900;color:{{ $s['gpa']>3.0?'#ef4444':($s['gpa']>2.5?'#f59e0b':'#22c55e') }};">{{ $s['gpa'] }}</td>
                <td class="table-td" style="text-align:center;font-weight:800;color:{{ $s['abs_rate']>20?'#ef4444':'#64748b' }};">{{ $s['abs_rate'] }}%</td>
                <td class="table-td" style="text-align:center;"><span class="badge {{ $risk==='High'?'badge-red':($risk==='Medium'?'badge-orange':'badge-slate') }}">{{ $risk }}</span></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Charts --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Grade Analytics Bar
    @php $gaLabels=collect($gradeAnalytics)->keys(); $gaAvg=collect($gradeAnalytics)->pluck('avg'); @endphp
    new Chart(document.getElementById('gradeChart'), {
        type:'bar',
        data:{labels:{!! json_encode($gaLabels) !!},datasets:[
            {label:'Avg GPA',data:{!! json_encode($gaAvg) !!},backgroundColor:'#3b82f6',borderRadius:8},
        ]},
        options:{responsive:true,scales:{y:{min:1,max:5,reverse:false}},plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}
    });
    // Pass/Fail Donut
    new Chart(document.getElementById('passFailChart'), {
        type:'doughnut',
        data:{labels:['Passed','Failing'],datasets:[{data:[{{ $passCount }},{{ $failCount }}],backgroundColor:['#22c55e','#ef4444'],borderWidth:0}]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{display:false}}}
    });
    // Attendance by Subject
    @php
        $attLabels=collect($attendanceSummary)->keys();
        $attPresent=collect($attendanceSummary)->pluck('present');
        $attLate=collect($attendanceSummary)->pluck('late');
        $attAbsent=collect($attendanceSummary)->pluck('absent');
    @endphp
    new Chart(document.getElementById('attendChart'), {
        type:'bar',
        data:{labels:{!! json_encode($attLabels) !!},datasets:[
            {label:'Present',data:{!! json_encode($attPresent) !!},backgroundColor:'#22c55e'},
            {label:'Late',data:{!! json_encode($attLate) !!},backgroundColor:'#f59e0b'},
            {label:'Absent',data:{!! json_encode($attAbsent) !!},backgroundColor:'#ef4444'},
        ]},
        options:{responsive:true,scales:{x:{stacked:true},y:{stacked:true}},plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}
    });
});
</script>
@endsection
