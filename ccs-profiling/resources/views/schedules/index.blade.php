@extends('layouts.app')
@section('title', 'Schedules')
@section('page-title', 'Class Schedules')
@section('page-subtitle', 'Weekly schedule grid for all classes')
@section('content')
<div class="card overflow-hidden">
    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
        <h2 class="font-black text-slate-800 dark:text-slate-100">AY 2023-2024 · 1st Semester Schedule</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Subject</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Day</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Time</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Room</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Section</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Type</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Faculty</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                @forelse($schedules as $sched)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $sched->subject_name }}</p>
                        <p class="text-xs text-orange-500 font-bold">{{ $sched->subject_code }}</p>
                    </td>
                    <td class="px-6 py-4"><span class="text-sm font-bold text-slate-600 dark:text-slate-400">{{ $sched->day }}</span></td>
                    <td class="px-6 py-4"><span class="text-sm font-bold text-slate-600 dark:text-slate-400">{{ $sched->start_time }} – {{ $sched->end_time }}</span></td>
                    <td class="px-6 py-4"><span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-2 py-1 rounded-lg text-xs font-bold">{{ $sched->room }}</span></td>
                    <td class="px-6 py-4"><span class="text-sm font-bold text-slate-600 dark:text-slate-400">{{ $sched->section }}</span></td>
                    <td class="px-6 py-4"><span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase {{ $sched->type === 'Laboratory' ? 'bg-purple-100 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400' : 'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400' }}">{{ $sched->type }}</span></td>
                    <td class="px-6 py-4"><span class="text-sm text-slate-500 dark:text-slate-400">{{ $sched->faculty_id }}</span></td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-16 text-center"><div class="text-4xl mb-3">📅</div><p class="font-bold text-slate-500">No schedules found</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
