<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('date')->paginate(20);
        return view('events.index', compact('events'));
    }

    public function create() { return view('events.create'); }

    public function store(Request $request)
    {
        $v = $request->validate([
            'title'      => 'required|string|max:255',
            'description'=> 'required',
            'date'       => 'required|date',
            'start_time' => 'required',
            'end_time'   => 'required',
            'location'   => 'required',
            'organizer'  => 'required',
            'category'   => 'required',
            'status'     => 'required',
        ]);
        $event = Event::create($v);
        ActivityLog::create(['user_id' => auth()->id(), 'user_name' => auth()->user()->name, 'action' => 'Created event', 'target' => $event->title, 'module' => 'Events']);
        return redirect()->route('events.index')->with('success', 'Event created!');
    }

    public function edit(Event $event) { return view('events.edit', compact('event')); }

    public function update(Request $request, Event $event)
    {
        $v = $request->validate([
            'title'      => 'required|string|max:255',
            'description'=> 'required',
            'date'       => 'required|date',
            'start_time' => 'required',
            'end_time'   => 'required',
            'location'   => 'required',
            'organizer'  => 'required',
            'category'   => 'required',
            'status'     => 'required',
        ]);
        $event->update($v);
        return redirect()->route('events.index')->with('success', 'Event updated!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }
}
