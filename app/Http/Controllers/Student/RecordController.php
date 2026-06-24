<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    // Past semester records (GPA + collapsible subject breakdown) for the student.
    public function showRecords(Request $request)
    {
        $student = Auth::user()->student;

        $records = $student->semesterRecords()
            ->with('schoolYear')
            ->join('school_years', 'school_years.id', '=', 'semester_records.school_year_id')
            ->orderBy('school_years.year_label')
            ->orderBy('semester_records.semester')
            ->select('semester_records.*')
            ->get();

        // Match each record to its enrollment (section + graded subjects) by year + semester.
        $enrollments = $student->enrollments()
            ->with(['section', 'subjects'])
            ->get()
            ->keyBy(fn ($e) => $e->section->school_year_id.'-'.$e->section->semester);

        return view('student.records', compact('student', 'records', 'enrollments'));
    }
}
