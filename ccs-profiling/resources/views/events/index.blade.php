@extends('layouts.app')
@section('title','Events')
@section('page-title','Events Calendar')
@section('page-subtitle','School events, activities, and important dates')
@section('content')

<div style="display:flex;justify-content:flex-end;margin-bottom:24px;">
    <a href="{{ route('events.create') }}" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Event
    </a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
@forelse($events as $event)
@php
$colors=['Academic'=>['#dbeafe','#2563eb','#3b82f6'],'Social'=>['#f3e8ff','#7c3aed','#8b5cf6'],'Sports'=>['#ffedd5','#c2410c','#f97316'],'Workshop'=>['#ccfbf1','#0f766e','#14b8a6'],'Holiday'=>['#fce7f3','#be185d','#ec4899']];
[$bg,$dark,$mid]=$colors[$event->category]??['#f1f5f9','#475569','#64748b'];
@endphp
<div class="card" style="overflow:hidden;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,.08)'" onmouseout="this.style.boxShadow=''">
    <div style="height:6px;background:{{$mid}};"></div>
    <div style="padding:20px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:12px;">
            <div style="font-weight:800;font-size:15px;color:#0f172a;line-height:1.3;">{{ $event->title }}</div>
            <span style="background:{{$bg}};color:{{$dark}};padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;text-transform:uppercase;white-space:nowrap;">{{ $event->category }}</span>
        </div>
        <p style="font-size:13px;color:#64748b;margin-bottom:16px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $event->description }}</p>
        <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:16px;">
            <div style="font-size:12px;color:#475569;display:flex;align-items:center;gap:8px;font-weight:500;">
                <span style="color:#f97316;">📅</span> {{ $event->date->format('F d, Y') }}
            </div>
            <div style="font-size:12px;color:#475569;display:flex;align-items:center;gap:8px;font-weight:500;">
                <span style="color:#f97316;">⏰</span> {{ $event->start_time }} – {{ $event->end_time }}
            </div>
            <div style="font-size:12px;color:#475569;display:flex;align-items:center;gap:8px;font-weight:500;">
                <span style="color:#f97316;">📍</span> {{ $event->location }}
            </div>
        </div>
        <div style="padding-top:14px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            @if($event->status==='Upcoming')<span class="badge badge-blue">{{ $event->status }}</span>
            @elseif($event->status==='Ongoing')<span class="badge badge-green">{{ $event->status }}</span>
            @elseif($event->status==='Completed')<span class="badge badge-slate">{{ $event->status }}</span>
            @else<span class="badge badge-red">{{ $event->status }}</span>@endif
            <div style="display:flex;gap:4px;">
                <a href="{{ route('events.edit',$event) }}" style="width:30px;height:30px;border-radius:8px;background:#fffbeb;color:#f59e0b;display:flex;align-items:center;justify-content:center;text-decoration:none;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form method="POST" action="{{ route('events.destroy',$event) }}" onsubmit="return confirm('Delete event?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="width:30px;height:30px;border-radius:8px;background:#fef2f2;color:#ef4444;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@empty
<div class="card" style="grid-column:1/-1;">
    <div class="empty-state"><div style="font-size:48px;margin-bottom:12px;">📅</div><div style="font-weight:700;font-size:16px;color:#475569;">No events yet</div><a href="{{ route('events.create') }}" class="btn btn-primary" style="margin-top:16px;">Create First Event</a></div>
</div>
@endforelse
</div>
<div style="margin-top:20px;">{{ $events->links() }}</div>
@endsection
