<?php
namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Artisan;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('user_name', 'like', "%{$request->search}%")
                  ->orWhere('action', 'like', "%{$request->search}%")
                  ->orWhere('target', 'like', "%{$request->search}%");
            });
        }
        if ($request->module && $request->module !== 'All') {
            $query->where('module', $request->module);
        }

        $logs    = $query->latest()->paginate(30)->withQueryString();
        $modules = ActivityLog::distinct()->pluck('module')->sort()->values();
        $stats   = [
            'total_users'    => User::count(),
            'total_students' => Student::count(),
            'total_faculty'  => Faculty::count(),
            'total_logs'     => ActivityLog::count(),
        ];

        return view('admin.index', compact('logs', 'modules', 'stats'));
    }

    public function seedDatabase()
    {
        try {
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            ActivityLog::create(['user_id' => auth()->id(), 'user_name' => auth()->user()->name, 'action' => 'Seeded database', 'target' => '1000+ records', 'module' => 'Database', 'status' => 'Success']);
            return redirect()->back()->with('success', 'Database seeded successfully! 1,000+ records generated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Seeding failed: ' . $e->getMessage());
        }
    }

    public function destroyLog(ActivityLog $log)
    {
        $log->delete();
        return redirect()->back()->with('success', 'Log entry deleted.');
    }
}
