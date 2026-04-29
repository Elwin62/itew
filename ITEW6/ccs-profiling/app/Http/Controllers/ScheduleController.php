<?php
namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::orderByRaw("FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->orderBy('start_time')->get();
        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        return view('schedules.index', compact('schedules', 'days'));
    }

    // Faculty: view their own schedules by faculty_id
    public function mySchedules()
    {
        $user     = auth()->user();
        $faculty  = \App\Models\Faculty::where('email', $user->email)->first();
        $schedules = $faculty
            ? Schedule::where('faculty_id', $faculty->faculty_id)
                ->orderByRaw("FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
                ->orderBy('start_time')->get()
            : collect();
        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        return view('schedules.index', compact('schedules', 'days'));
    }
}

