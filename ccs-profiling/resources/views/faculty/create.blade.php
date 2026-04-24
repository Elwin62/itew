@extends('layouts.app')
@section('title', 'Add Faculty')
@section('page-title', 'Add New Faculty')
@section('page-subtitle', 'Fill in the faculty member information below')
@section('content')
<div class="max-w-4xl">
    <div class="mb-4"><a href="{{ route('faculty.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-orange-500 font-bold text-sm transition-colors w-fit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>Back</a></div>
    <form method="POST" action="{{ route('faculty.store') }}" class="space-y-6">
        @csrf
        @if($errors->any())<div class="p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 rounded-xl"><ul class="text-sm text-red-600 font-semibold space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul></div>@endif
        <div class="card p-8">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Personal Information</h3>
            <div class="grid md:grid-cols-2 gap-6">
                <div><label class="form-label">Full Name *</label><input name="full_name" value="{{ old('full_name') }}" class="form-input" required></div>
                <div><label class="form-label">Faculty ID *</label><input name="faculty_id" value="{{ old('faculty_id') }}" placeholder="PROF-2024-XXX" class="form-input" required></div>
                <div><label class="form-label">Email *</label><input name="email" type="email" value="{{ old('email') }}" class="form-input" required></div>
                <div><label class="form-label">Contact Number *</label><input name="contact_number" value="{{ old('contact_number') }}" class="form-input" required></div>
                <div><label class="form-label">Gender *</label>
                    <select name="gender" class="form-input" required><option value="">Select</option>@foreach(['Male','Female','Other'] as $g)<option value="{{ $g }}" {{ old('gender')===$g?'selected':'' }}>{{ $g }}</option>@endforeach</select>
                </div>
                <div><label class="form-label">Birthdate *</label><input name="birthdate" type="date" value="{{ old('birthdate') }}" class="form-input" required></div>
                <div class="md:col-span-2"><label class="form-label">Address *</label><textarea name="address" rows="2" class="form-input" required>{{ old('address') }}</textarea></div>
            </div>
        </div>
        <div class="card p-8">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Professional Information</h3>
            <div class="grid md:grid-cols-2 gap-6">
                <div><label class="form-label">Department *</label>
                    <select name="department" class="form-input" required><option value="">Select Department</option>
                        @foreach(['College of Computing and Engineering','College of Arts and Sciences','College of Business Administration','College of Education','College of Nursing'] as $d)
                        <option value="{{ $d }}" {{ old('department')===$d?'selected':'' }}>{{ $d }}</option>@endforeach
                    </select>
                </div>
                <div><label class="form-label">Academic Rank *</label><input name="academic_rank" value="{{ old('academic_rank') }}" placeholder="e.g. Professor I" class="form-input" required></div>
                <div><label class="form-label">Employment Status *</label>
                    <select name="employment_status" class="form-input" required>
                        @foreach(['Full-time','Part-time','Contractual'] as $s)<option value="{{ $s }}" {{ old('employment_status')===$s?'selected':'' }}>{{ $s }}</option>@endforeach
                    </select>
                </div>
                <div><label class="form-label">Years of Experience *</label><input name="years_experience" type="number" min="0" value="{{ old('years_experience', 0) }}" class="form-input" required></div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('faculty.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-orange">Save Faculty</button>
        </div>
    </form>
</div>
@endsection
