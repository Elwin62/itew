@extends('layouts.app')
@section('title', 'Instruction Module')
@section('page-title', 'Instruction & Curriculum Management')
@section('page-subtitle', 'Manage curricula, subjects, syllabi, and lessons')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-800">Curricula</h2>
    <button onclick="document.getElementById('add-curriculum-modal').classList.remove('hidden')" class="btn btn-primary">
        + Add Curriculum
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($curricula as $curriculum)
    <div class="card p-6 flex flex-col h-full relative">
        <form method="POST" action="{{ route('instruction.curriculum.destroy', $curriculum) }}" class="absolute top-4 right-4" onsubmit="return confirm('Delete this curriculum?')">
            @csrf @method('DELETE')
            <button class="text-red-400 hover:text-red-600 font-bold text-sm">✖</button>
        </form>

        <h3 class="text-lg font-bold text-slate-800 mb-1">{{ $curriculum->program_name }}</h3>
        <div class="text-sm text-slate-500 font-semibold mb-3">Effective: {{ $curriculum->effective_year }}</div>
        
        <span class="w-fit mb-4 badge {{ $curriculum->status === 'Active' ? 'badge-green' : ($curriculum->status === 'Draft' ? 'badge-slate' : 'badge-red') }}">
            {{ $curriculum->status }}
        </span>

        <p class="text-sm text-slate-600 flex-1">{{ $curriculum->description }}</p>

        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-sm font-bold text-slate-400">{{ $curriculum->subjects->count() }} Subjects</span>
            <a href="{{ route('instruction.curriculum.show', $curriculum) }}" class="text-orange-500 hover:text-orange-600 font-bold text-sm">Manage &rarr;</a>
        </div>
    </div>
    @endforeach
</div>

@if($curricula->isEmpty())
<div class="empty-state card p-12 text-center mt-6">
    <div class="text-4xl mb-4">📚</div>
    <div class="text-lg font-bold text-slate-800">No Curricula Found</div>
    <div class="text-sm text-slate-500 mt-2">Start by creating a new curriculum.</div>
</div>
@endif

<!-- Add Curriculum Modal -->
<div id="add-curriculum-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="font-bold text-slate-800">Add New Curriculum</h3>
            <button onclick="document.getElementById('add-curriculum-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✖</button>
        </div>
        <form method="POST" action="{{ route('instruction.curriculum.store') }}" class="p-6 space-y-4">
            @csrf
            <div><label class="form-label">Program Name *</label><input name="program_name" class="form-input" required placeholder="e.g. Bachelor of Science in IT"></div>
            <div><label class="form-label">Effective Year *</label><input name="effective_year" class="form-input" required placeholder="e.g. 2024-2025"></div>
            <div><label class="form-label">Status *</label>
                <select name="status" class="form-input" required>
                    <option value="Active">Active</option>
                    <option value="Draft">Draft</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div><label class="form-label">Description</label><textarea name="description" rows="3" class="form-input" placeholder="Optional notes..."></textarea></div>
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('add-curriculum-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
