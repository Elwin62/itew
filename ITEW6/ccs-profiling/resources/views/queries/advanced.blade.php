@extends('layouts.app')
@section('title', 'Advanced Student Profiling')
@section('page-title', 'Advanced Profiling Search')
@section('page-subtitle', 'Query and filter students across all profiling dimensions')

@section('content')

{{-- Filter Panel --}}
<div class="card p-6 mb-6">
    <form method="GET" action="{{ route('queries.advanced') }}">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="form-label">Program</label>
                <select name="program" class="form-input">
                    <option value="">All Programs</option>
                    @foreach($programs as $p)
                        <option value="{{ $p }}" {{ request('program') === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Year Level</label>
                <select name="year_level" class="form-input">
                    <option value="">All Years</option>
                    @foreach([1,2,3,4] as $y)
                        <option value="{{ $y }}" {{ request('year_level') == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Enrollment Status</label>
                <select name="status" class="form-input">
                    <option value="">Any Status</option>
                    <option value="Enrolled" {{ request('status') === 'Enrolled' ? 'selected' : '' }}>Enrolled</option>
                    <option value="Not Enrolled" {{ request('status') === 'Not Enrolled' ? 'selected' : '' }}>Not Enrolled</option>
                    <option value="Graduated" {{ request('status') === 'Graduated' ? 'selected' : '' }}>Graduated</option>
                </select>
            </div>
            <div>
                <label class="form-label">Discipline</label>
                <select name="violations_filter" class="form-input">
                    <option value="">Any Record</option>
                    <option value="none" {{ request('violations_filter') === 'none' ? 'selected' : '' }}>Clean Record (No Violations)</option>
                    <option value="has" {{ request('violations_filter') === 'has' ? 'selected' : '' }}>Has Violations</option>
                </select>
            </div>
            <div>
                <label class="form-label">Skill</label>
                <select name="skill" class="form-input">
                    <option value="">Any Skill</option>
                    @foreach($skillsList as $s)
                        <option value="{{ $s }}" {{ request('skill') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Skill Proficiency</label>
                <select name="skill_proficiency" class="form-input">
                    <option value="">Any Proficiency</option>
                    <option value="Beginner" {{ request('skill_proficiency') === 'Beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="Intermediate" {{ request('skill_proficiency') === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="Advanced" {{ request('skill_proficiency') === 'Advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
            </div>
            <div>
                <label class="form-label">Organization / Affiliation</label>
                <select name="organization" class="form-input">
                    <option value="">Any Affiliation</option>
                    @foreach($orgsList as $o)
                        <option value="{{ $o }}" {{ request('organization') === $o ? 'selected' : '' }}>{{ $o }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-3">
                <a href="{{ route('queries.advanced') }}" class="btn btn-secondary flex-1 justify-center">Reset</a>
                <button type="submit" class="btn btn-primary flex-1 justify-center">Search</button>
            </div>
        </div>
    </form>
</div>

{{-- Results Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div style="font-size:13px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;">
        {{ $students->total() }} Profile(s) Found
    </div>
    @if(request()->hasAny(['program','year_level','status','violations_filter','skill','skill_proficiency','organization']))
    <div style="display:flex;flex-wrap:wrap;gap:6px;">
        @foreach(['program' => 'Program', 'year_level' => 'Year', 'status' => 'Status', 'violations_filter' => 'Discipline', 'skill' => 'Skill', 'skill_proficiency' => 'Proficiency', 'organization' => 'Org'] as $key => $label)
            @if(request($key))
            <span style="background:#fff7ed;color:#f97316;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;">
                {{ $label }}: {{ request($key) }}
            </span>
            @endif
        @endforeach
    </div>
    @endif
</div>

{{-- Results Grid --}}
@forelse($students as $student)
@if($loop->first)
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;">
@endif

<div class="card" style="overflow:hidden;transition:box-shadow .2s,transform .2s;" onmouseover="this.style.boxShadow='0 8px 32px rgba(0,0,0,.12)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
    {{-- Card Banner --}}
    <div style="height:72px;background:linear-gradient(135deg,#f97316,#fb923c,#fbbf24);"></div>

    {{-- Avatar --}}
    <div style="padding:0 20px 20px;margin-top:-36px;">
        <img src="{{ $student->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=f97316&color=fff&size=200' }}"
             style="width:68px;height:68px;border-radius:16px;border:4px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,.1);object-fit:cover;display:block;background:#fff;"
             alt="{{ $student->full_name }}">

        <div style="margin-top:10px;">
            <div style="font-size:15px;font-weight:900;color:#0f172a;line-height:1.3;">{{ $student->full_name }}</div>
            <div style="font-size:11px;color:#f97316;font-weight:700;margin-top:2px;">{{ $student->student_id }}</div>
        </div>

        {{-- Badges --}}
        <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:5px;">
            <span class="badge badge-slate" style="font-size:10px;">{{ Str::limit($student->academic_program, 15) }}</span>
            <span class="badge badge-orange" style="font-size:10px;">Yr {{ $student->year_level }}</span>
            @if($student->enrollment_status === 'Enrolled')
                <span class="badge badge-green" style="font-size:10px;">Enrolled</span>
            @elseif($student->enrollment_status === 'Graduated')
                <span class="badge badge-blue" style="font-size:10px;">Graduated</span>
            @else
                <span class="badge badge-slate" style="font-size:10px;">{{ $student->enrollment_status }}</span>
            @endif
        </div>

        {{-- Query Highlights --}}
        @php $highlights = []; @endphp
        @if(request('skill') && $student->skills->where('name', request('skill'))->count() > 0)
            @php $highlights[] = ['color' => '#3b82f6', 'icon' => '⭐', 'text' => request('skill') . ' (' . $student->skills->where('name', request('skill'))->first()->proficiency . ')']; @endphp
        @endif
        @if(request('organization') && $student->organizations->where('name', request('organization'))->count() > 0)
            @php $highlights[] = ['color' => '#9333ea', 'icon' => '🏛', 'text' => request('organization')]; @endphp
        @endif
        @if(request('violations_filter') === 'none')
            @php $highlights[] = ['color' => '#16a34a', 'icon' => '✅', 'text' => 'Clean Record']; @endphp
        @elseif(request('violations_filter') === 'has' && $student->violations->count() > 0)
            @php $highlights[] = ['color' => '#dc2626', 'icon' => '⚠️', 'text' => $student->violations->count() . ' Violation(s)']; @endphp
        @endif

        @if(count($highlights) > 0)
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;flex-direction:column;gap:5px;">
            @foreach($highlights as $h)
            <div style="font-size:11px;font-weight:700;color:{{ $h['color'] }};">{{ $h['icon'] }} {{ $h['text'] }}</div>
            @endforeach
        </div>
        @endif

        {{-- View Profile Button --}}
        <a href="{{ route('students.show', $student) }}" class="btn btn-primary" style="margin-top:14px;width:100%;justify-content:center;">
            View Profile →
        </a>
    </div>
</div>

@if($loop->last)
</div>
@endif
@empty
<div class="card" style="padding:64px 24px;text-align:center;">
    <div style="font-size:48px;margin-bottom:12px;">🔍</div>
    <div style="font-size:18px;font-weight:900;color:#0f172a;margin-bottom:8px;">No Profiles Found</div>
    <div style="font-size:14px;color:#94a3b8;">Try adjusting your filter criteria.</div>
</div>
@endforelse

{{-- Pagination --}}
<div style="margin-top:24px;">
    {{ $students->links() }}
</div>

@endsection
