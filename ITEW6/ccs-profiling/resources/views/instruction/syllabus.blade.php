@extends('layouts.app')
@section('title', 'Syllabus - ' . $subject->code)
@section('page-title', 'Syllabus: ' . $subject->title)
@section('page-subtitle', $subject->curriculum->program_name . ' · ' . $subject->curriculum->effective_year)

@section('content')
<div class="mb-4">
    <a href="{{ route('instruction.curriculum.show', $subject->curriculum) }}" class="flex items-center gap-2 text-slate-500 hover:text-orange-500 font-bold text-sm transition-colors w-fit">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Subjects
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Col: Syllabus Form -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-6">
            <h3 class="text-lg font-black text-slate-800 mb-4">Course Overview</h3>
            <form method="POST" action="{{ route('instruction.syllabus.store', $subject) }}" class="space-y-4">
                @csrf
                <div><label class="form-label">Course Description</label><textarea name="course_description" rows="4" class="form-input" placeholder="Enter course description...">{{ $subject->syllabus?->course_description }}</textarea></div>
                <div><label class="form-label">Course Objectives</label><textarea name="course_objectives" rows="4" class="form-input" placeholder="List objectives...">{{ $subject->syllabus?->course_objectives }}</textarea></div>
                <div><label class="form-label">Grading System</label><textarea name="grading_system" rows="3" class="form-input" placeholder="e.g. Quizzes 30%, Exams 40%...">{{ $subject->syllabus?->grading_system }}</textarea></div>
                <div class="pt-2">
                    <button type="submit" class="btn btn-primary w-full justify-center">Save Syllabus</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Col: Lessons -->
    <div class="lg:col-span-2">
        <div class="card p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-slate-800">Weekly Lessons</h3>
                @if($subject->syllabus)
                <button onclick="document.getElementById('add-lesson-modal').classList.remove('hidden')" class="btn btn-primary text-sm">
                    + Add Lesson
                </button>
                @endif
            </div>

            @if(!$subject->syllabus)
            <div class="empty-state py-8">
                <div class="text-3xl mb-2">⚠️</div>
                <div class="font-bold text-slate-600">Syllabus not yet saved</div>
                <div class="text-sm text-slate-400 mt-2">Save the course overview first to start adding lessons.</div>
            </div>
            @elseif($subject->syllabus->lessons->isEmpty())
            <div class="empty-state py-8 border-2 border-dashed border-slate-200 rounded-xl">
                <div class="text-3xl mb-2">📚</div>
                <div class="font-bold text-slate-600">No lessons added</div>
            </div>
            @else
            <div class="space-y-4">
                @foreach($subject->syllabus->lessons->sortBy('week_number') as $lesson)
                <div class="p-5 border border-slate-200 rounded-xl hover:border-orange-200 transition-colors relative">
                    <form method="POST" action="{{ route('instruction.lesson.destroy', $lesson) }}" onsubmit="return confirm('Delete this lesson?')" class="absolute top-4 right-4">
                        @csrf @method('DELETE')
                        <button class="text-slate-400 hover:text-red-500 font-bold text-sm">✖</button>
                    </form>
                    <div class="flex gap-4">
                        <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold uppercase">Wk</span>
                            <span class="text-lg font-black leading-none">{{ $lesson->week_number }}</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800">{{ $lesson->topic_title }}</h4>
                            @if($lesson->learning_outcomes)
                            <div class="text-sm text-slate-500 mt-1"><strong class="text-slate-600">Outcomes:</strong> {{ $lesson->learning_outcomes }}</div>
                            @endif
                            @if($lesson->materials_link)
                            <a href="{{ $lesson->materials_link }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-blue-500 hover:text-blue-600 mt-2">
                                🔗 Learning Materials
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@if($subject->syllabus)
<!-- Add Lesson Modal -->
<div id="add-lesson-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="font-bold text-slate-800">Add Lesson</h3>
            <button onclick="document.getElementById('add-lesson-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✖</button>
        </div>
        <form method="POST" action="{{ route('instruction.lesson.store', $subject->syllabus) }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-1"><label class="form-label">Week *</label><input type="number" name="week_number" class="form-input" required min="1" value="{{ $subject->syllabus->lessons->count() + 1 }}"></div>
                <div class="col-span-3"><label class="form-label">Topic Title *</label><input name="topic_title" class="form-input" required placeholder="e.g. Introduction to OOP"></div>
                <div class="col-span-4"><label class="form-label">Learning Outcomes</label><textarea name="learning_outcomes" rows="2" class="form-input" placeholder="Students should be able to..."></textarea></div>
                <div class="col-span-4"><label class="form-label">Materials Link</label><input type="url" name="materials_link" class="form-input" placeholder="e.g. Google Drive link or Module URL"></div>
            </div>
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('add-lesson-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Lesson</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
