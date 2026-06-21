<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    // Show the student's assigned section for the active school year.
    // The section is whatever they have an approved (or pending) enrollment in —
    // students don't pick it here, it's shown read-only.
    public function showSection(Request $request)
    {
        $student    = Auth::user()->student;
        $schoolYear = SchoolYear::active();

        $enrollment = $student->enrollments()
            ->with(['section.strand', 'section.schoolYear', 'section.subjects'])
            ->when($schoolYear, fn ($q) => $q->whereHas('section', fn ($s) => $s->where('school_year_id', $schoolYear->id)))
            ->latest('submitted_at')
            ->first();

        $section = $enrollment?->section;

        return view('student.section', compact('student', 'schoolYear', 'enrollment', 'section'));
    }

    // Weekly class schedule for the student's section (read-only).
    public function showSchedule(Request $request)
    {
        $student    = Auth::user()->student;
        $schoolYear = SchoolYear::active();

        $enrollment = $student->enrollments()
            ->with(['section.subjects', 'section.strand'])
            ->when($schoolYear, fn ($q) => $q->whereHas('section', fn ($s) => $s->where('school_year_id', $schoolYear->id)))
            ->latest('submitted_at')
            ->first();

        $section = $enrollment?->section;

        return view('student.schedule', compact('student', 'schoolYear', 'enrollment', 'section'));
    }
}
