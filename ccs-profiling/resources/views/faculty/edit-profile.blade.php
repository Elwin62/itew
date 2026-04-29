@extends('layouts.app')
@section('title','Edit Profile')
@section('page-title','Edit My Profile')
@section('page-subtitle','Update your professional information and skills')
@section('content')

<div style="max-width:800px;">
    <div style="margin-bottom:16px;">
        <a href="{{ route('faculty.my-profile') }}" style="display:flex;align-items:center;gap:8px;color:#94a3b8;font-weight:700;font-size:13px;text-decoration:none;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Profile
        </a>
    </div>

    <form method="POST" action="{{ route('faculty.update-profile') }}">
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
                @foreach([
                    ['Faculty ID', $faculty->faculty_id],
                    ['Full Name', $faculty->full_name],
                    ['Email', $faculty->email],
                    ['Department', $faculty->department],
                    ['Rank', $faculty->academic_rank],
                    ['Status', $faculty->employment_status]
                ] as [$l,$v])
                <div>
                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">{{ $l }}</div>
                    <div style="font-size:14px;font-weight:700;color:#64748b;">{{ $v }}</div>
                </div>
                @endforeach
            </div>
            <p style="font-size:11px;color:#94a3b8;margin-top:12px;">ℹ️ To change your core professional details, please contact HR or the Admin.</p>
        </div>

        {{-- Editable fields --}}
        <div class="card" style="padding:24px;margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">✏️ Editable Information</div>
            <div style="display:grid;grid-template-columns:1fr;gap:16px;">
                <div>
                    <label class="form-label">Contact Number *</label>
                    <input name="contact_number" value="{{ old('contact_number', $faculty->contact_number) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="2" class="form-input">{{ old('address', $faculty->address) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Skills --}}
        <div class="card" style="padding:24px;margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:16px;">⭐ Skills & Expertise</div>
            <div id="skills-container" style="display:flex;flex-direction:column;gap:10px;margin-bottom:12px;">
                @foreach($faculty->skills as $i => $sk)
                <div class="skill-row" style="display:grid;grid-template-columns:1fr 40px;gap:10px;align-items:end;">
                    <div><label class="form-label">Skill</label><input name="skills[{{ $i }}][skill]" value="{{ $sk->skill }}" class="form-input"></div>
                    <button type="button" onclick="this.closest('.skill-row').remove()" style="background:#fee2e2;color:#dc2626;border:none;border-radius:10px;width:38px;height:38px;cursor:pointer;font-size:16px;font-weight:900;">×</button>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addSkill()" class="btn btn-secondary" style="font-size:12px;">+ Add Skill</button>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <a href="{{ route('faculty.my-profile') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">💾 Save Changes</button>
        </div>
    </form>
</div>

<script>
let skillIdx = {{ $faculty->skills->count() }};
function addSkill() {
    const c = document.getElementById('skills-container');
    const r = document.createElement('div');
    r.className = 'skill-row';
    r.style.cssText = 'display:grid;grid-template-columns:1fr 40px;gap:10px;align-items:end;';
    r.innerHTML = `<div><label class="form-label">Skill</label><input name="skills[${skillIdx}][skill]" placeholder="Skill name" class="form-input"></div>
        <button type="button" onclick="this.closest('.skill-row').remove()" style="background:#fee2e2;color:#dc2626;border:none;border-radius:10px;width:38px;height:38px;cursor:pointer;font-size:16px;font-weight:900;">×</button>`;
    c.appendChild(r);
    skillIdx++;
}
</script>
@endsection
