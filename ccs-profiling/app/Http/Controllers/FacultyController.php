<?php
namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $query = Faculty::with('skills');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('faculty_id', 'like', "%{$request->search}%");
            });
        }
        if ($request->department && $request->department !== 'All') {
            $query->where('department', $request->department);
        }
        if ($request->status && $request->status !== 'All') {
            $query->where('employment_status', $request->status);
        }

        $faculty      = $query->orderBy('full_name')->paginate(20)->withQueryString();
        $departments  = Faculty::distinct()->pluck('department')->sort()->values();

        return view('faculty.index', compact('faculty', 'departments'));
    }

    public function show(Faculty $faculty)
    {
        $faculty->load(['skills', 'education', 'subjects']);
        return view('faculty.show', compact('faculty'));
    }

    public function create() { return view('faculty.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id'        => 'required|unique:faculty,faculty_id',
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|unique:faculty,email',
            'contact_number'    => 'required',
            'department'        => 'required',
            'academic_rank'     => 'required',
            'employment_status' => 'required',
            'gender'            => 'required',
            'birthdate'         => 'required|date',
            'address'           => 'required',
            'years_experience'  => 'required|integer|min:0',
        ]);

        $validated['date_hired']    = now()->toDateString();
        $validated['profile_photo'] = 'https://ui-avatars.com/api/?name=' . urlencode($validated['full_name']) . '&background=8b5cf6&color=fff&size=200';

        $f = Faculty::create($validated);
        ActivityLog::create(['user_id' => auth()->id(), 'user_name' => auth()->user()->name, 'action' => 'Added new faculty member', 'target' => $f->full_name, 'module' => 'Faculty Information']);

        return redirect()->route('faculty.show', $f)->with('success', 'Faculty record created successfully!');
    }

    public function edit(Faculty $faculty) { return view('faculty.edit', compact('faculty')); }

    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|unique:faculty,email,' . $faculty->id,
            'contact_number'    => 'required',
            'department'        => 'required',
            'academic_rank'     => 'required',
            'employment_status' => 'required',
            'gender'            => 'required',
            'birthdate'         => 'required|date',
            'address'           => 'required',
            'years_experience'  => 'required|integer|min:0',
        ]);

        $faculty->update($validated);
        ActivityLog::create(['user_id' => auth()->id(), 'user_name' => auth()->user()->name, 'action' => 'Updated faculty profile', 'target' => $faculty->full_name, 'module' => 'Faculty Information']);

        return redirect()->route('faculty.show', $faculty)->with('success', 'Faculty record updated successfully!');
    }

    public function destroy(Faculty $faculty)
    {
        $name = $faculty->full_name;
        $faculty->delete();
        ActivityLog::create(['user_id' => auth()->id(), 'user_name' => auth()->user()->name, 'action' => 'Deleted faculty record', 'target' => $name, 'module' => 'Faculty Information']);
        return redirect()->route('faculty.index')->with('success', 'Faculty record deleted.');
    }

    // Faculty self-view
    public function myProfile()
    {
        $user    = auth()->user();
        $faculty = Faculty::where('email', $user->email)
            ->with(['skills', 'education', 'subjects'])
            ->first();
        return view('faculty.show', compact('faculty'));
    }
}
