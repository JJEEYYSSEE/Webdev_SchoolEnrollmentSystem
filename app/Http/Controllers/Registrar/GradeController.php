<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    // Show grade encoding form for an approved enrollment.
    public function show(Request $request, $enrollment)
    {
        $enrollment = Enrollment::with(['student', 'section.strand', 'enrollmentSubjects.subject'])
            ->findOrFail($enrollment);

        return view('registrar.grades.form', compact('enrollment'));
    }

    // Save grades per subject for an enrollment.
    public function update(Request $request, $enrollment)
    {
        $enrollment = Enrollment::with('enrollmentSubjects')->findOrFail($enrollment);

        $validated = $request->validate([
            'grades'              => ['required', 'array'],
            'grades.*.grade'      => ['nullable', 'numeric', 'min:60', 'max:100'],
            'grades.*.status'     => ['required', 'in:enrolled,passed,failed,dropped'],
        ]);

        DB::transaction(function () use ($enrollment, $validated) {
            foreach ($enrollment->enrollmentSubjects as $es) {
                if (! isset($validated['grades'][$es->id])) {
                    continue;
                }
                $row = $validated['grades'][$es->id];
                $es->update([
                    'grade'  => $row['grade'] ?? null,
                    'status' => $row['status'],
                ]);
            }

            AuditLog::record('encoded_grades', 'Enrollment', $enrollment->id,
                'Encoded grades for enrollment #'.$enrollment->id);
        });

        return back()->with('success', 'Grades saved.');
    }
}
