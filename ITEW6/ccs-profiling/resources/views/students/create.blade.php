@extends('layouts.app')
@section('title', 'Add Student')
@section('page-title', 'Add New Student')
@section('page-subtitle', 'Fill in the student information below')

@section('content')
<div class="max-w-4xl">
    <div class="mb-4">
        <a href="{{ route('students.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-orange-500 font-bold text-sm transition-colors w-fit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back
        </a>
    </div>

    <form method="POST" action="{{ route('students.store') }}" class="space-y-6">
        @csrf
        @if($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-xl">
            <ul class="text-sm text-red-600 dark:text-red-400 font-semibold space-y-1">
                @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card p-8">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Personal Information</h3>
            <div class="grid md:grid-cols-2 gap-6">
                <div><label class="form-label">Full Name *</label><input name="full_name" value="{{ old('full_name') }}" class="form-input" required></div>
                <div><label class="form-label">Student ID *</label><input name="student_id" value="{{ old('student_id') }}" placeholder="2024-XXXXX" class="form-input" required></div>
                <div><label class="form-label">Email *</label><input name="email" type="email" value="{{ old('email') }}" class="form-input" required></div>
                <div><label class="form-label">Contact Number *</label><input name="contact_number" value="{{ old('contact_number') }}" class="form-input" required></div>
                <div>
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-input" required>
                        <option value="">Select</option>
                        @foreach(['Male','Female','Other'] as $g) <option value="{{ $g }}" {{ old('gender') === $g ? 'selected' : '' }}>{{ $g }}</option> @endforeach
                    </select>
                </div>
                <div><label class="form-label">Birthdate *</label><input name="birthdate" type="date" value="{{ old('birthdate') }}" class="form-input" required></div>
                <div class="md:col-span-2"><label class="form-label">Address *</label><textarea name="address" rows="2" class="form-input" required>{{ old('address') }}</textarea></div>
            </div>
        </div>

        <div class="card p-8">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Academic Information</h3>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">Academic Program *</label>
                    <select name="academic_program" class="form-input" required>
                        <option value="">Select Program</option>
                        @foreach(['BS Information Technology','BS Computer Science','BS Information Systems'] as $prog)
                        <option value="{{ $prog }}" {{ old('academic_program') === $prog ? 'selected' : '' }}>{{ $prog }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Year Level *</label>
                    <select name="year_level" class="form-input" required>
                        @foreach([1,2,3,4] as $y) <option value="{{ $y }}" {{ old('year_level') == $y ? 'selected' : '' }}>Year {{ $y }}</option> @endforeach
                    </select>
                </div>
                <div><label class="form-label">Section *</label><input name="section" value="{{ old('section') }}" class="form-input" required placeholder="e.g. 3A"></div>
                <div>
                    <label class="form-label">Enrollment Status *</label>
                    <select name="enrollment_status" class="form-input" required>
                        @foreach(['Enrolled','Not Enrolled','Graduated','Dropped'] as $s)
                        <option value="{{ $s }}" {{ old('enrollment_status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Admission Type</label>
                    <select name="admission_type" class="form-input">
                        @foreach(['Regular','Transferee','Irregular'] as $t)
                        <option value="{{ $t }}" {{ old('admission_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card p-8">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4">Skills & Expertise</h3>
            <div id="skills-container" class="space-y-3 mb-4">
                <div class="skill-row grid grid-cols-3 gap-3">
                    <input name="skills[0][name]" placeholder="Skill name (e.g. Basketball)" class="form-input">
                    <select name="skills[0][category]" class="form-input">
                        @foreach(['Programming','Networking','Design','Soft Skill','Sports'] as $c) <option>{{ $c }}</option> @endforeach
                    </select>
                    <select name="skills[0][proficiency]" class="form-input">
                        @foreach(['Beginner','Intermediate','Advanced'] as $p) <option>{{ $p }}</option> @endforeach
                    </select>
                </div>
            </div>
            <button type="button" onclick="addSkill()" class="btn btn-secondary text-sm">+ Add Another Skill</button>
        </div>

        <div class="card p-8">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4">Violations</h3>
            <div id="violations-container" class="space-y-4 mb-4">
                <!-- Violations will be added here -->
            </div>
            <button type="button" onclick="addViolation()" class="btn btn-secondary text-sm">+ Add Violation</button>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Student</button>
        </div>
    </form>
</div>

<script>
let skillIndex = 1;
function addSkill() {
    const container = document.getElementById('skills-container');
    const row = document.createElement('div');
    row.className = 'skill-row grid grid-cols-3 gap-3';
    row.innerHTML = `
        <input name="skills[${skillIndex}][name]" placeholder="Skill name" class="form-input">
        <select name="skills[${skillIndex}][category]" class="form-input">
            <option>Programming</option><option>Networking</option><option>Design</option><option>Soft Skill</option><option>Sports</option>
        </select>
        <select name="skills[${skillIndex}][proficiency]" class="form-input">
            <option>Beginner</option><option>Intermediate</option><option>Advanced</option>
        </select>
    `;
    container.appendChild(row);
    skillIndex++;
}

let violationIndex = 0;
function addViolation() {
    const container = document.getElementById('violations-container');
    const row = document.createElement('div');
    row.className = 'violation-row grid md:grid-cols-2 gap-4 p-6 border border-slate-200 rounded-xl relative pt-8';
    row.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-3 right-4 text-slate-400 hover:text-red-500 font-bold">✖ Remove</button>
        <div><label class="form-label">Category</label><input name="violations[${violationIndex}][category]" class="form-input" required placeholder="e.g. Minor Offense"></div>
        <div><label class="form-label">Sanction</label><input name="violations[${violationIndex}][sanction]" class="form-input" required placeholder="e.g. Warning"></div>
        <div><label class="form-label">Status</label><select name="violations[${violationIndex}][status]" class="form-input" required><option>Active</option><option>Resolved</option></select></div>
        <div><label class="form-label">Reported By</label><input name="violations[${violationIndex}][reported_by]" class="form-input" required placeholder="e.g. Prof. Smith"></div>
        <div><label class="form-label">Date Reported</label><input type="date" name="violations[${violationIndex}][date_reported]" class="form-input" required></div>
    `;
    container.appendChild(row);
    violationIndex++;
}
</script>
@endsection
