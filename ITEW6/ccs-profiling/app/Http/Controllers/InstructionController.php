<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\Lesson;
use Illuminate\Http\Request;

class InstructionController extends Controller
{
    public function index()
    {
        $curricula = Curriculum::with('subjects')->get();
        return view('instruction.index', compact('curricula'));
    }

    public function storeCurriculum(Request $request)
    {
        $validated = $request->validate([
            'program_name' => 'required|string|max:255',
            'effective_year' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:Active,Inactive,Draft',
        ]);

        Curriculum::create($validated);
        return redirect()->route('instruction.index')->with('success', 'Curriculum added successfully.');
    }

    public function destroyCurriculum(Curriculum $curriculum)
    {
        $curriculum->delete();
        return redirect()->route('instruction.index')->with('success', 'Curriculum deleted.');
    }

    public function showCurriculum(Curriculum $curriculum)
    {
        $curriculum->load('subjects');
        return view('instruction.curriculum', compact('curriculum'));
    }

    public function storeSubject(Request $request, Curriculum $curriculum)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'units' => 'required|integer|min:1',
            'semester' => 'nullable|string',
            'year_level' => 'nullable|integer|min:1|max:5',
        ]);

        $curriculum->subjects()->create($validated);
        return redirect()->route('instruction.curriculum.show', $curriculum)->with('success', 'Subject added successfully.');
    }

    public function destroySubject(Subject $subject)
    {
        $curriculum = $subject->curriculum;
        $subject->delete();
        return redirect()->route('instruction.curriculum.show', $curriculum)->with('success', 'Subject deleted.');
    }

    public function showSyllabus(Subject $subject)
    {
        $subject->load('syllabus.lessons');
        return view('instruction.syllabus', compact('subject'));
    }

    public function storeSyllabus(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'course_description' => 'nullable|string',
            'course_objectives' => 'nullable|string',
            'grading_system' => 'nullable|string',
        ]);

        if ($subject->syllabus) {
            $subject->syllabus->update($validated);
        } else {
            $subject->syllabus()->create($validated);
        }

        return redirect()->route('instruction.syllabus.show', $subject)->with('success', 'Syllabus updated successfully.');
    }

    public function storeLesson(Request $request, Syllabus $syllabus)
    {
        $validated = $request->validate([
            'week_number' => 'required|integer|min:1',
            'topic_title' => 'required|string|max:255',
            'learning_outcomes' => 'nullable|string',
            'materials_link' => 'nullable|string',
        ]);

        $syllabus->lessons()->create($validated);
        return redirect()->route('instruction.syllabus.show', $syllabus->subject)->with('success', 'Lesson added successfully.');
    }

    public function destroyLesson(Lesson $lesson)
    {
        $subject = $lesson->syllabus->subject;
        $lesson->delete();
        return redirect()->route('instruction.syllabus.show', $subject)->with('success', 'Lesson deleted.');
    }
}
