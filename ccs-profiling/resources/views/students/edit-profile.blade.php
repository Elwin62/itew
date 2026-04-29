@extends('layouts.app')
@section('title','Edit Profile')
@section('page-title','Edit My Profile')
@section('page-subtitle','Update your personal information and skills')
@section('content')

<div style="max-width:800px;">
    <div style="margin-bottom:16px;">
        <a href="{{ route('student.my-profile') }}" style="display:flex;align-items:center;gap:8px;color:#94a3b8;font-weight:700;font-size:13px;text-decoration:none;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Profile
        </a>
    </div>

    <form method="POST" action="{{ route('student.update-profile') }}">
        @csrf @method('PUT')

        @if($errors->any())
        <div style="padding:14px 18px;background:#fef2f2;border:1.5px solid #fecaca;border-radius:12px;margin-bottom:20px;">
            <ul style="font-size:13px;color:#dc2626;font-weight:600;margin:0;padding-left:16px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- Read-only info --}}
        <div class="card" style="padding:24px;margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">📋 Account Information (Read Only)</div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                @foreach([['Student ID',$student->student_id],['Full Name',$student->full_name],['Email',$student->email],['Program',$student->academic_program],['Year Level','Year '.$student->year_level],['Section',$student->section]] as [$l,$v])
                <div>
                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">{{ $l }}</div>
                    <div style="font-size:14px;font-weight:700;color:#64748b;">{{ $v }}</div>
                </div>
                @endforeach
            </div>
            <p style="font-size:11px;color:#94a3b8;margin-top:12px;">ℹ️ To change academic information, contact the registrar.</p>
        </div>

        {{-- Editable fields --}}
        <div class="card" style="padding:24px;margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">✏️ Editable Information</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label class="form-label">Contact Number *</label>
                    <input name="contact_number" value="{{ old('contact_number', $student->contact_number) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Civil Status</label>
                    <select name="civil_status" class="form-input">
                        @foreach(['Single','Married','Widowed','Separated'] as $cs)
                        <option value="{{ $cs }}" {{ old('civil_status',$student->civil_status)===$cs?'selected':'' }}>{{ $cs }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:span 2;">
                    <label class="form-label">Address *</label>
                    <textarea name="address" rows="2" class="form-input" required>{{ old('address', $student->address) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Skills --}}
        <div class="card" style="padding:24px;margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">⭐ Skills & Expertise</div>
            <div id="skills-container" style="display:flex;flex-direction:column;gap:10px;margin-bottom:12px;">
                @foreach($student->skills as $i => $sk)
                <div class="skill-row" style="display:grid;grid-template-columns:1fr 1fr 1fr 40px;gap:10px;align-items:end;">
                    <div><label class="form-label">Skill Name</label><input name="skills[{{ $i }}][name]" value="{{ $sk->name }}" class="form-input"></div>
                    <div><label class="form-label">Category</label>
                        <select name="skills[{{ $i }}][category]" class="form-input">
                            @foreach(['Programming','Networking','Design','Soft Skill','Sports'] as $c)
                            <option {{ $sk->category===$c?'selected':'' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="form-label">Proficiency</label>
                        <select name="skills[{{ $i }}][proficiency]" class="form-input">
                            @foreach(['Beginner','Intermediate','Advanced'] as $p)
                            <option {{ $sk->proficiency===$p?'selected':'' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" onclick="this.closest('.skill-row').remove()" style="background:#fee2e2;color:#dc2626;border:none;border-radius:10px;width:38px;height:38px;cursor:pointer;font-size:16px;font-weight:900;">×</button>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addSkill()" class="btn btn-secondary" style="font-size:12px;">+ Add Skill</button>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <a href="{{ route('student.my-profile') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">💾 Save Changes</button>
        </div>
    </form>
</div>

<script>
let skillIdx = {{ $student->skills->count() }};
function addSkill() {
    const c = document.getElementById('skills-container');
    const r = document.createElement('div');
    r.className = 'skill-row';
    r.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 1fr 40px;gap:10px;align-items:end;';
    r.innerHTML = `<div><label class="form-label">Skill Name</label><input name="skills[${skillIdx}][name]" placeholder="Skill name" class="form-input"></div>
        <div><label class="form-label">Category</label><select name="skills[${skillIdx}][category]" class="form-input"><option>Programming</option><option>Networking</option><option>Design</option><option>Soft Skill</option><option>Sports</option></select></div>
        <div><label class="form-label">Proficiency</label><select name="skills[${skillIdx}][proficiency]" class="form-input"><option>Beginner</option><option>Intermediate</option><option>Advanced</option></select></div>
        <button type="button" onclick="this.closest('.skill-row').remove()" style="background:#fee2e2;color:#dc2626;border:none;border-radius:10px;width:38px;height:38px;cursor:pointer;font-size:16px;font-weight:900;">×</button>`;
    c.appendChild(r);
    skillIdx++;
}
</script>
@endsection
