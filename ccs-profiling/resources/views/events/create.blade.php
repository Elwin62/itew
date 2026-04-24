@extends('layouts.app')
@section('title', 'Add Event')
@section('page-title', 'Create Event')
@section('page-subtitle', 'Add a new school event or activity')
@section('content')
<div class="max-w-3xl">
    <div class="mb-4"><a href="{{ route('events.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-orange-500 font-bold text-sm transition-colors w-fit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>Back</a></div>
    <form method="POST" action="{{ route('events.store') }}" class="card p-8 space-y-6">
        @csrf
        @if($errors->any())<div class="p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 rounded-xl"><ul class="text-sm text-red-600 font-semibold space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul></div>@endif
        <div class="grid md:grid-cols-2 gap-6">
            <div class="md:col-span-2"><label class="form-label">Event Title *</label><input name="title" value="{{ old('title') }}" class="form-input" required></div>
            <div class="md:col-span-2"><label class="form-label">Description *</label><textarea name="description" rows="3" class="form-input" required>{{ old('description') }}</textarea></div>
            <div><label class="form-label">Date *</label><input name="date" type="date" value="{{ old('date') }}" class="form-input" required></div>
            <div><label class="form-label">Location *</label><input name="location" value="{{ old('location') }}" class="form-input" required></div>
            <div><label class="form-label">Start Time *</label><input name="start_time" type="time" value="{{ old('start_time') }}" class="form-input" required></div>
            <div><label class="form-label">End Time *</label><input name="end_time" type="time" value="{{ old('end_time') }}" class="form-input" required></div>
            <div><label class="form-label">Organizer *</label><input name="organizer" value="{{ old('organizer') }}" class="form-input" required></div>
            <div><label class="form-label">Category *</label>
                <select name="category" class="form-input" required>
                    @foreach(['Academic','Social','Sports','Workshop','Holiday'] as $c) <option value="{{ $c }}" {{ old('category')===$c?'selected':'' }}>{{ $c }}</option> @endforeach
                </select>
            </div>
            <div><label class="form-label">Status *</label>
                <select name="status" class="form-input" required>
                    @foreach(['Upcoming','Ongoing','Completed','Cancelled'] as $s) <option value="{{ $s }}" {{ old('status')===$s?'selected':'' }}>{{ $s }}</option> @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('events.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-orange">Create Event</button>
        </div>
    </form>
</div>
@endsection
