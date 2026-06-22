<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    // Show enrollment form — sections matching the student's strand + grade for
    // the active, open school year, each with its pre-loaded subject list.
    public function showEnrollForm(Request $request)
    {
        $student    = Auth::user()->student;
        $schoolYear = SchoolYear::active();

        // Gate: enrollment must be open
        if (! $schoolYear || ! $schoolYear->is_enrollment_open) {
            return view('student.enroll', [
                'student'    => $student,
                'schoolYear' => $schoolYear,
                'sections'   => collect(),
                'blocked'    => 'closed',
            ]);
        }

        // Gate: block only if there's an active (pending/approved) enrollment.
        // An "invalid" enrollment is returned-for-compliance — the student may
        // fix the issue and re-submit, so it does NOT block the form.
        $existing = $this->activeEnrollment($student, $schoolYear);
        if ($existing) {
            return view('student.enroll', [
                'student'    => $student,
                'schoolYear' => $schoolYear,
                'sections'   => collect(),
                'blocked'    => 'enrolled',
                'existing'   => $existing,
            ]);
        }

        // Surface the registrar's remarks from a previously returned submission.
        $invalid = $student->enrollments()
            ->where('status', 'invalid')
            ->whereHas('section', fn ($s) => $s
                ->where('school_year_id', $schoolYear->id)
                ->where('semester', $schoolYear->active_semester))
            ->latest('submitted_at')
            ->first();

        $sections = Section::with('subjects')
            ->where('school_year_id', $schoolYear->id)
            ->where('semester', $schoolYear->active_semester)
            ->where('strand_id', $student->strand_id)
            ->where('grade_level', $student->grade_level)
            ->get();

        return view('student.enroll', [
            'student'        => $student,
            'schoolYear'     => $schoolYear,
            'sections'       => $sections,
            'blocked'        => null,
            'invalidRemarks' => $invalid?->remarks,
        ]);
    }

    // Submit enrollment — create a pending enrollment and snapshot the section's
    // subjects into enrollment_subjects (one-click, no manual subject picking).
    public function postEnrollForm(Request $request)
    {
        $student    = Auth::user()->student;
        $schoolYear = SchoolYear::active();

        if (! $schoolYear || ! $schoolYear->is_enrollment_open) {
            return redirect()->route('student.showEnrollForm')
                ->with('error', 'Enrollment is currently closed.');
        }

        if ($this->activeEnrollment($student, $schoolYear)) {
            return redirect()->route('student.showEnrollStatus')
                ->with('error', 'You already have an active enrollment this semester.');
        }

        $validated = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
        ]);

        $section = Section::with('subjects')->findOrFail($validated['section_id']);

        // Guard: section must match student's strand + grade and the active year/semester
        if (
            $section->school_year_id !== $schoolYear->id ||
            $section->semester !== $schoolYear->active_semester ||
            $section->strand_id !== $student->strand_id ||
            $section->grade_level !== $student->grade_level
        ) {
            return redirect()->route('student.showEnrollForm')
                ->with('error', 'That section is not available for your strand and grade level.');
        }

        DB::transaction(function () use ($student, $section) {
            $enrollment = Enrollment::create([
                'student_id'   => $student->id,
                'section_id'   => $section->id,
                'status'       => 'pending',
                'submitted_at' => now(),
            ]);

            // Snapshot subjects from section_subjects → enrollment_subjects
            $rows = $section->subjects->map(fn ($subject) => [
                'enrollment_id' => $enrollment->id,
                'subject_id'    => $subject->id,
                'status'        => 'enrolled',
                'created_at'    => now(),
                'updated_at'    => now(),
            ])->all();

            if ($rows) {
                DB::table('enrollment_subjects')->insert($rows);
            }
        });

        return redirect()->route('student.showEnrollStatus')
            ->with('success', 'Enrollment submitted! It is now pending registrar approval.');
    }

    // Show current enrollment status for the active school year.
    public function showEnrollStatus(Request $request)
    {
        $student    = Auth::user()->student;
        $schoolYear = SchoolYear::active();

        $enrollment = $student->enrollments()
            ->with(['section.strand', 'section.schoolYear', 'subjects', 'approver'])
            ->when($schoolYear, fn ($q) => $q->whereHas('section', fn ($s) => $s->where('school_year_id', $schoolYear->id)))
            ->latest('submitted_at')
            ->first();

        return view('student.status', compact('student', 'schoolYear', 'enrollment'));
    }

    /**
     * Active enrollment for the student in the current year + semester.
     * Only pending/approved block the form. An "invalid" (returned) enrollment
     * does NOT block — the student can comply and re-submit.
     */
    private function activeEnrollment($student, ?SchoolYear $schoolYear): ?Enrollment
    {
        if (! $student || ! $schoolYear) {
            return null;
        }

        return $student->enrollments()
            ->whereIn('status', ['pending', 'approved'])
            ->whereHas('section', fn ($s) => $s
                ->where('school_year_id', $schoolYear->id)
                ->where('semester', $schoolYear->active_semester))
            ->latest('submitted_at')
            ->first();
    }
}
