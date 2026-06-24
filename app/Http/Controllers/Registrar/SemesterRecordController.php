<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolYear;
use App\Models\SemesterRecord;
use App\Models\Student;
use Illuminate\Http\Request;

class SemesterRecordController extends Controller
{
    // Show all semester records (GPA per semester) for a student.
    public function showSemesterRecord(Request $request, $student)
    {
        $student = Student::with(['user', 'semesterRecords.schoolYear'])->findOrFail($student);

        $records = $student->semesterRecords()
            ->with('schoolYear')
            ->get()
            ->sortBy(fn ($r) => $r->schoolYear->year_label.$r->semester)
            ->values();

        // Match each record to its enrollment (section + graded subjects).
        $enrollments = $student->enrollments()
            ->with(['section', 'subjects'])
            ->get()
            ->keyBy(fn ($e) => $e->section->school_year_id.'-'.$e->section->semester);

        $schoolYears = SchoolYear::orderByDesc('year_label')->get();

        return view('registrar.records.show', compact('student', 'records', 'enrollments', 'schoolYears'));
    }

    // Manually create/update a single semester record (GPA + lock).
    public function updateSemesterRecord(Request $request, $student)
    {
        $student = Student::findOrFail($student);

        $validated = $request->validate([
            'school_year_id' => ['required', 'exists:school_years,id'],
            'semester'       => ['required', 'in:1st,2nd'],
            'gpa'            => ['nullable', 'numeric', 'min:60', 'max:100'],
            'is_locked'      => ['nullable', 'boolean'],
        ]);

        SemesterRecord::updateOrCreate(
            [
                'student_id'     => $student->id,
                'school_year_id' => $validated['school_year_id'],
                'semester'       => $validated['semester'],
            ],
            [
                'gpa'       => $validated['gpa'] ?? null,
                'is_locked' => (bool) ($validated['is_locked'] ?? false),
            ]
        );

        AuditLog::record('updated_semester_record', 'Student', $student->id,
            'Updated semester record for student #'.$student->id);

        return back()->with('success', 'Semester record saved.');
    }
}
