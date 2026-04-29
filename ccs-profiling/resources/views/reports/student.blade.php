@extends('layouts.app')
@section('title','My Reports')
@section('page-title','Academic Reports')
@section('page-subtitle','Your complete academic performance, attendance, and activity reports')
@section('content')

@if($student)

{{-- Alerts --}}
@if($alerts->count())
<div style="display:flex;flex-direction:column;gap:10px;margin-bottom:24px;">
@foreach($alerts as $alert)
<div style="padding:14px 18px;border-radius:12px;display:flex;align-items:center;gap:10px;font-size:13px;font-weight:700;
    {{ $alert['type']==='danger' ? 'background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;' : ($alert['type']==='success' ? 'background:#f0fdf4;border:1.5px solid #bbf7d0;color:#16a34a;' : 'background:#fffbeb;border:1.5px solid #fde68a;color:#d97706;') }}">
    {{ $alert['type']==='danger' ? '🚨' : ($alert['type']==='success' ? '🎉' : '⚠️') }} {{ $alert['msg'] }}
</div>
@endforeach
</div>
@endif

{{-- Download Bar --}}
<div class="card" style="padding:20px 24px;margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:16px;font-weight:900;color:#0f172a;">📊 {{ $student->full_name }}</div>
            <div style="font-size:12px;color:#94a3b8;">{{ $student->student_id }} · {{ $student->academic_program }} · Year {{ $student->year_level }}</div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <a href="{{ route('reports.student.download',['type'=>'full']) }}" class="btn btn-primary" style="font-size:12px;">📥 Full Report CSV</a>
            <a href="{{ route('reports.student.download',['type'=>'grades']) }}" class="btn btn-blue" style="font-size:12px;">📥 Grades CSV</a>
            <a href="{{ route('reports.student.download',['type'=>'attendance']) }}" class="btn" style="background:#22c55e;color:#fff;font-size:12px;">📥 Attendance CSV</a>
            <button onclick="window.print()" class="btn btn-secondary no-print" style="font-size:12px;">🖨 Print</button>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:14px;margin-bottom:24px;">
@php $rec=$student->academicRecords->first(); @endphp
@foreach([
    ['GPA', $rec?->gpa ?? 'N/A','📊','#3b82f6','#dbeafe'],
    ['Attendance', $attendanceStats['rate'].'%','📋','#22c55e','#dcfce7'],
    ['Submitted', $assignmentStats['submitted'],'✅','#8b5cf6','#f3e8ff'],
    ['Missing', $assignmentStats['missing'],'❌','#ef4444','#fee2e2'],
    ['Avg Score', $assignmentStats['avgScore'],'📝','#f97316','#ffedd5'],
    ['Achievements', $student->achievements->count(),'🏆','#f59e0b','#fffbeb'],
] as [$l,$v,$ico,$c,$bg])
<div class="stat-card" style="border-top:4px solid {{$c}};">
    <div style="width:40px;height:40px;background:{{$bg}};border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:12px;">{{$ico}}</div>
    <div style="font-size:24px;font-weight:900;color:#0f172a;">{{$v}}</div>
    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-top:4px;">{{$l}}</div>
</div>
@endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- GPA Trend Chart --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📈 Academic Progress Over Time</div>
        <canvas id="gpaTrendChart" height="200"></canvas>
    </div>
    {{-- Attendance Donut --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📋 Attendance Summary</div>
        <div style="display:flex;align-items:center;gap:24px;">
            <div style="width:180px;height:180px;"><canvas id="attendanceChart"></canvas></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach([['Present',$attendanceStats['present'],'#22c55e'],['Late',$attendanceStats['late'],'#f59e0b'],['Absent',$attendanceStats['absent'],'#ef4444']] as [$sl,$sv,$sc])
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:12px;height:12px;border-radius:3px;background:{{$sc}};"></div>
                    <span style="font-size:13px;font-weight:700;color:#0f172a;">{{$sv}}</span>
                    <span style="font-size:11px;color:#94a3b8;">{{$sl}}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Attendance by Subject --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📊 Attendance by Subject</div>
        <canvas id="attendSubjectChart" height="200"></canvas>
    </div>
    {{-- Assignment Scores Chart --}}
    <div class="card" style="padding:24px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📝 Assignment Submission Status</div>
        <canvas id="assignmentChart" height="200"></canvas>
    </div>
</div>

{{-- Subject Grades Table --}}
<div class="card" style="overflow:hidden;margin-bottom:24px;">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;">📚 Grades per Subject & Overall Average</div>
    </div>
    @php $allGrades=collect(); foreach($student->academicRecords as $r) foreach($r->grades as $g) $allGrades->push($g); @endphp
    @if($allGrades->count())
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr>
                <th class="table-th" style="text-align:left;">Code</th>
                <th class="table-th" style="text-align:left;">Subject</th>
                <th class="table-th" style="text-align:center;">Grade</th>
                <th class="table-th" style="text-align:center;">Units</th>
                <th class="table-th" style="text-align:center;">Status</th>
            </tr></thead>
            <tbody>
            @foreach($allGrades as $g)
            @php $gc=$g->grade<=1.5?'#22c55e':($g->grade<=2.5?'#3b82f6':($g->grade<=3.0?'#f59e0b':'#ef4444')); @endphp
            <tr>
                <td class="table-td" style="font-weight:800;font-size:13px;">{{ $g->code }}</td>
                <td class="table-td" style="font-size:13px;color:#475569;">{{ $g->name }}</td>
                <td class="table-td" style="text-align:center;font-size:18px;font-weight:900;color:{{$gc}};">{{ $g->grade }}</td>
                <td class="table-td" style="text-align:center;font-size:13px;color:#64748b;">{{ $g->units }}</td>
                <td class="table-td" style="text-align:center;"><span class="badge {{ $g->grade<=3.0?'badge-green':'badge-red' }}">{{ $g->grade<=3.0?'Passed':'Failed' }}</span></td>
            </tr>
            @endforeach
            <tr style="background:#f8fafc;">
                <td colspan="2" class="table-td" style="font-weight:900;font-size:13px;">Overall Average</td>
                <td class="table-td" style="text-align:center;font-size:20px;font-weight:900;color:#3b82f6;">{{ round($allGrades->avg('grade'),2) }}</td>
                <td class="table-td" style="text-align:center;font-weight:700;">{{ $allGrades->sum('units') }}</td>
                <td class="table-td"></td>
            </tr>
            </tbody>
        </table>
    </div>
    @else <div class="empty-state" style="padding:32px;"><div style="font-size:36px;">📝</div><div style="color:#94a3b8;margin-top:8px;">No grades recorded</div></div> @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Assignment Details Table --}}
    <div class="card" style="overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;">📝 Assignment & Activity Scores</div>
        </div>
        <div style="overflow-x:auto;max-height:400px;overflow-y:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead><tr>
                    <th class="table-th" style="text-align:left;">Title</th>
                    <th class="table-th" style="text-align:center;">Type</th>
                    <th class="table-th" style="text-align:center;">Score</th>
                    <th class="table-th" style="text-align:center;">Status</th>
                </tr></thead>
                <tbody>
                @foreach($assignments->take(20) as $a)
                @php $sc=$a['status']==='Submitted'?'badge-green':($a['status']==='Late'?'badge-orange':'badge-red'); @endphp
                <tr>
                    <td class="table-td" style="font-size:12px;font-weight:700;">{{ Str::limit($a['title'],28) }}<div style="font-size:10px;color:#94a3b8;">{{ $a['subject'] }}</div></td>
                    <td class="table-td" style="text-align:center;"><span class="badge badge-blue" style="font-size:9px;">{{ $a['type'] }}</span></td>
                    <td class="table-td" style="text-align:center;font-weight:800;font-size:14px;color:{{ $a['pct']>=75?'#22c55e':($a['pct']>=50?'#f59e0b':'#ef4444') }};">{{ $a['status']==='Missing'?'—':$a['score'].'/'.$a['total'] }}</td>
                    <td class="table-td" style="text-align:center;"><span class="badge {{$sc}}">{{ $a['status'] }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Teacher Feedback --}}
    <div class="card" style="padding:24px;max-height:470px;overflow-y:auto;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">💬 Teacher Feedback & Comments</div>
        @forelse($feedbacks as $fb)
        @php $fbc=['Commendation'=>['#dcfce7','#15803d','🌟'],'Warning'=>['#fee2e2','#dc2626','⚠️'],'General'=>['#f1f5f9','#475569','💬']][$fb->type]; @endphp
        <div style="padding:14px;border-radius:12px;background:{{ $fbc[0] }};margin-bottom:10px;border-left:4px solid {{ $fbc[1] }};">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:12px;font-weight:800;color:{{ $fbc[1] }};">{{ $fbc[2] }} {{ $fb->type }}</span>
                <span style="font-size:10px;color:#94a3b8;">{{ $fb->created_at->diffForHumans() }}</span>
            </div>
            <p style="font-size:12px;color:#334155;line-height:1.6;margin:0;">{{ $fb->comment }}</p>
            <div style="font-size:10px;color:#94a3b8;margin-top:6px;">— {{ $fb->faculty_name }} · {{ $fb->subject_code }}</div>
        </div>
        @empty <p style="font-size:13px;color:#94a3b8;">No feedback received yet.</p> @endforelse
    </div>
</div>

{{-- Achievements --}}
<div class="card" style="padding:24px;">
    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">🏆 Achievements, Honors & Awards</div>
    @if($student->achievements->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;">
        @foreach($student->achievements as $a)
        <div style="padding:16px;border-radius:14px;background:#fffbeb;border:1.5px solid #fde68a;">
            <div style="font-size:20px;margin-bottom:6px;">🏆</div>
            <div style="font-size:13px;font-weight:800;color:#0f172a;">{{ $a->type }}</div>
            <div style="font-size:11px;color:#92400e;">{{ $a->level }} · {{ $a->date_received }}</div>
        </div>
        @endforeach
    </div>
    @else <p style="font-size:13px;color:#94a3b8;">No achievements recorded yet.</p> @endif
</div>

{{-- Charts JS --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
    // GPA Trend
    new Chart(document.getElementById('gpaTrendChart'), {
        type:'line',
        data:{
            labels: {!! json_encode($gpaTrend->pluck('label')) !!},
            datasets:[{label:'GPA',data:{!! json_encode($gpaTrend->pluck('gpa')) !!},borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,.1)',fill:true,tension:.4,pointRadius:6,pointBackgroundColor:'#3b82f6',borderWidth:3}]
        },
        options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{reverse:true,min:1,max:5,ticks:{stepSize:.5}}}}
    });
    // Attendance Donut
    new Chart(document.getElementById('attendanceChart'), {
        type:'doughnut',
        data:{labels:['Present','Late','Absent'],datasets:[{data:[{{ $attendanceStats['present'] }},{{ $attendanceStats['late'] }},{{ $attendanceStats['absent'] }}],backgroundColor:['#22c55e','#f59e0b','#ef4444'],borderWidth:0}]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{display:false}}}
    });
    // Attendance by Subject
    @if($attendanceBySubject->count())
    new Chart(document.getElementById('attendSubjectChart'), {
        type:'bar',
        data:{labels:{!! json_encode($attendanceBySubject->pluck('subject')) !!},datasets:[
            {label:'Present',data:{!! json_encode($attendanceBySubject->pluck('present')) !!},backgroundColor:'#22c55e'},
            {label:'Late',data:{!! json_encode($attendanceBySubject->pluck('late')) !!},backgroundColor:'#f59e0b'},
            {label:'Absent',data:{!! json_encode($attendanceBySubject->pluck('absent')) !!},backgroundColor:'#ef4444'},
        ]},
        options:{responsive:true,scales:{x:{stacked:true},y:{stacked:true}},plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}
    });
    @endif
    // Assignment Status
    new Chart(document.getElementById('assignmentChart'), {
        type:'doughnut',
        data:{labels:['Submitted','Late','Missing'],datasets:[{data:[{{ $assignmentStats['submitted'] }},{{ $assignmentStats['late'] }},{{ $assignmentStats['missing'] }}],backgroundColor:['#22c55e','#f59e0b','#ef4444'],borderWidth:0}]},
        options:{responsive:true,cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}
    });
});
</script>

@else
<div class="card" style="padding:24px;"><div class="empty-state"><div style="font-size:48px;">📊</div><div style="font-size:16px;font-weight:700;color:#475569;margin-top:12px;">No Student Record Linked</div><p style="color:#94a3b8;margin-top:8px;">Contact the registrar to link your profile.</p></div></div>
@endif
@endsection
