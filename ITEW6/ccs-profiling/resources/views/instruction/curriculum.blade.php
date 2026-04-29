@extends('layouts.app')
@section('title', 'Manage Curriculum')
@section('page-title', $curriculum->program_name)
@section('page-subtitle', 'Effective Year: ' . $curriculum->effective_year)

@section('content')
<div class="mb-4">
    <a href="{{ route('instruction.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-orange-500 font-bold text-sm transition-colors w-fit">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Curricula
    </a>
</div>

<div class="card p-8 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-black text-slate-800">Subjects</h3>
        <button onclick="document.getElementById('add-subject-modal').classList.remove('hidden')" class="btn btn-primary text-sm">
            + Add Subject
        </button>
    </div>

    @if($curriculum->subjects->isEmpty())
    <div class="empty-state py-8">
        <div class="text-3xl mb-2">📋</div>
        <div class="font-bold text-slate-600">No subjects added yet</div>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="table-th">Code</th>
                    <th class="table-th">Title</th>
                    <th class="table-th">Units</th>
                    <th class="table-th">Year Level</th>
                    <th class="table-th">Semester</th>
                    <th class="table-th">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($curriculum->subjects as $subject)
                <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100">
                    <td class="table-td font-bold text-slate-800">{{ $subject->code }}</td>
                    <td class="table-td text-slate-600 font-semibold">{{ $subject->title }}</td>
                    <td class="table-td text-slate-600">{{ $subject->units }}</td>
                    <td class="table-td text-slate-600">{{ $subject->year_level ?? 'N/A' }}</td>
                    <td class="table-td text-slate-600">{{ $subject->semester ?? 'N/A' }}</td>
                    <td class="table-td">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('instruction.syllabus.show', $subject) }}" class="text-orange-500 hover:text-orange-600 font-bold text-sm">Syllabus</a>
                            <form method="POST" action="{{ route('instruction.subject.destroy', $subject) }}" onsubmit="return confirm('Delete subject?')">
                                @csrf @method('DELETE')
                                <button class="text-slate-400 hover:text-red-500 font-bold text-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Add Subject Modal -->
<div id="add-subject-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="font-bold text-slate-800">Add New Subject</h3>
            <button onclick="document.getElementById('add-subject-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✖</button>
        </div>
        <form method="POST" action="{{ route('instruction.subject.store', $curriculum) }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="form-label">Subject Code *</label><input name="code" class="form-input" required placeholder="e.g. IT 101"></div>
                <div><label class="form-label">Units *</label><input type="number" name="units" class="form-input" required min="1"></div>
                <div class="col-span-2"><label class="form-label">Title *</label><input name="title" class="form-input" required placeholder="e.g. Introduction to Computing"></div>
                <div><label class="form-label">Year Level</label>
                    <select name="year_level" class="form-input">
                        <option value="">Any</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
                <div><label class="form-label">Semester</label>
                    <select name="semester" class="form-input">
                        <option value="">Any</option>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
            </div>
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('add-subject-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Subject</button>
            </div>
        </form>
    </div>
</div>
@endsection
