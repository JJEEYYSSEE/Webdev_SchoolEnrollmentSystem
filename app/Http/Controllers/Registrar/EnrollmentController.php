<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Strand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    // List enrollments — filterable by status / strand / grade / section.
    public function showEnrollments(Request $request)
    {
        $schoolYear = SchoolYear::active();

        $enrollments = Enrollment::query()
            ->with(['student', 'section.strand'])
            ->when($schoolYear, fn ($q) => $q->whereHas('section', fn ($s) => $s->where('school_year_id', $schoolYear->id)))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->strand, fn ($q) => $q->whereHas('section', fn ($s) => $s->where('strand_id', $request->strand)))
            ->when($request->grade, fn ($q) => $q->whereHas('section', fn ($s) => $s->where('grade_level', $request->grade)))
            ->when($request->section, fn ($q) => $q->where('section_id', $request->section))
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        $strands = Strand::orderBy('strand_code')->get();

        $sections = Section::with('strand')
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->orderBy('grade_level')
            ->orderBy('section_name')
            ->get();

        return view('registrar.enrollments.index', compact('enrollments', 'strands', 'sections', 'schoolYear'));
    }

    // Single enrollment with student info and enrolled subjects.
    public function showEnrollment(Request $request, $enrollment)
    {
        $enrollment = Enrollment::with([
            'student.strand', 'student.user', 'section.strand', 'section.schoolYear', 'subjects', 'approver', 'documents',
        ])->findOrFail($enrollment);

        return view('registrar.enrollments.show', compact('enrollment'));
    }

    // Approve — set status, record approver/time, enforce section capacity.
    public function approveEnrollment(Request $request, $enrollment)
    {
        $enrollment = Enrollment::with('section')->findOrFail($enrollment);

        if (! $enrollment->isPending()) {
            return back()->with('error', 'This enrollment has already been reviewed.');
        }

        // Hard block when the section is full
        if ($enrollment->section->isFull()) {
            return back()->with('error', 'Cannot approve — section "'.$enrollment->section->section_name.'" is full.');
        }

        $registrar = Auth::user()->registrar;

        $enrollment->update([
            'status'      => 'approved',
            'approved_by' => $registrar?->id,
            'reviewed_at' => now(),
            'remarks'     => null,
        ]);

        AuditLog::record('approved_enrollment', 'Enrollment', $enrollment->id,
            'Approved enrollment #'.$enrollment->id);

        return back()->with('success', 'Enrollment approved.');
    }

    // Mark invalid — return for compliance. The student can fix the issue and
    // re-submit (this is not a hard rejection).
    public function rejectEnrollment(Request $request, $enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);

        if (! $enrollment->isPending()) {
            return back()->with('error', 'This enrollment has already been reviewed.');
        }

        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);

        $registrar = Auth::user()->registrar;

        $enrollment->update([
            'status'      => 'invalid',
            'approved_by' => $registrar?->id,
            'reviewed_at' => now(),
            'remarks'     => $validated['remarks'],
        ]);

        AuditLog::record('invalidated_enrollment', 'Enrollment', $enrollment->id,
            'Returned enrollment #'.$enrollment->id.' as invalid');

        return back()->with('success', 'Enrollment returned to the student for compliance.');
    }

    // Reopen an invalid enrollment back to pending. Requires a valid reason
    // (e.g. "student submitted Form 138"). Reopens the application for review.
    public function revertEnrollment(Request $request, $enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);

        if ($enrollment->status !== 'invalid') {
            return back()->with('error', 'Only invalid enrollments can be reopened.');
        }

        $validated = $request->validate([
            'revert_reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $enrollment->update([
            'status'      => 'pending',
            'approved_by' => null,
            'reviewed_at' => null,
            'remarks'     => 'Reopened by registrar: '.$validated['revert_reason'],
        ]);

        AuditLog::record('reverted_enrollment', 'Enrollment', $enrollment->id,
            'Reverted enrollment #'.$enrollment->id.' to pending — '.$validated['revert_reason']);

        return back()->with('success', 'Enrollment reopened and set back to pending.');
    }

    // Batch approve all selected pending enrollments (skips full sections).
    public function batchApprove(Request $request)
    {
        $validated = $request->validate([
            'enrollment_ids'   => ['required', 'array'],
            'enrollment_ids.*' => ['integer', 'exists:enrollments,id'],
        ]);

        $registrar = Auth::user()->registrar;
        $approved = 0;
        $skipped  = 0;

        DB::transaction(function () use ($validated, $registrar, &$approved, &$skipped) {
            $enrollments = Enrollment::with('section')
                ->whereIn('id', $validated['enrollment_ids'])
                ->where('status', 'pending')
                ->get();

            foreach ($enrollments as $enrollment) {
                if ($enrollment->section->isFull()) {
                    $skipped++;
                    continue;
                }

                $enrollment->update([
                    'status'      => 'approved',
                    'approved_by' => $registrar?->id,
                    'reviewed_at' => now(),
                ]);
                $approved++;
            }

            AuditLog::record('batch_approved_enrollments', 'Enrollment', null,
                "Batch approved {$approved} enrollment(s)");
        });

        $msg = "Approved {$approved} enrollment(s).";
        if ($skipped) {
            $msg .= " Skipped {$skipped} (section full).";
        }

        return back()->with('success', $msg);
    }
}
