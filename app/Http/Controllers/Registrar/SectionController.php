<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Strand;
use App\Models\Subject;
use App\Services\ScheduleGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    // List sections grouped by grade level → strand.
    public function showSections(Request $request)
    {
        $sections = Section::with(['strand', 'schoolYear'])
            ->withCount(['enrollments as approved_count' => fn ($q) => $q->where('status', 'approved')])
            ->orderBy('grade_level')
            ->orderBy('section_name')
            ->get();

        $grouped = $sections->groupBy([
            fn ($s) => 'Grade '.$s->grade_level,
            fn ($s) => $s->strand?->strand_code ?? 'Unassigned',
        ]);

        return view('registrar.sections.index', compact('sections', 'grouped'));
    }

    public function showCreateSection()
    {
        return view('registrar.sections.form', [
            'section'    => null,
            'strands'    => Strand::orderBy('strand_code')->get(),
            'schoolYears' => SchoolYear::orderByDesc('year_label')->get(),
            'allSubjects' => Subject::orderBy('subject_code')->get(),
        ]);
    }

    public function postCreateSection(Request $request)
    {
        $validated = $this->validateSection($request);

        DB::transaction(function () use ($validated, $request) {
            $section = Section::create($validated);
            $section->subjects()->sync($request->input('subject_ids', []));
            AuditLog::record('created_section', 'Section', $section->id, 'Created section '.$section->section_name);
        });

        return redirect()->route('registrar.sections.showSections')
            ->with('success', 'Section created.');
    }

    public function showSection(Request $request, $section)
    {
        // No dedicated show view — editing covers detail + subject assignment.
        return redirect()->route('registrar.sections.showEditSection', $section);
    }

    public function showEditSection(Request $request, $section)
    {
        $section = Section::with('subjects')->findOrFail($section);

        return view('registrar.sections.form', [
            'section'     => $section,
            'strands'     => Strand::orderBy('strand_code')->get(),
            'schoolYears' => SchoolYear::orderByDesc('year_label')->get(),
            'allSubjects' => Subject::orderBy('subject_code')->get(),
        ]);
    }

    public function updateSection(Request $request, $section)
    {
        $section = Section::findOrFail($section);
        $validated = $this->validateSection($request, $section->id);

        DB::transaction(function () use ($section, $validated, $request) {
            $section->update($validated);
            $section->subjects()->sync($request->input('subject_ids', []));
            AuditLog::record('updated_section', 'Section', $section->id, 'Updated section '.$section->section_name);
        });

        return redirect()->route('registrar.sections.showSections')
            ->with('success', 'Section updated.');
    }

    public function deleteSection(Request $request, $section)
    {
        $section = Section::findOrFail($section);

        if ($section->enrollments()->exists()) {
            return back()->with('error', 'Cannot delete — students are enrolled in this section.');
        }

        $name = $section->section_name;
        $section->subjects()->detach();
        $section->delete();

        AuditLog::record('deleted_section', 'Section', null, 'Deleted section '.$name);

        return redirect()->route('registrar.sections.showSections')
            ->with('success', 'Section deleted.');
    }

    // a section's weekly timetable
    public function showSchedule(Request $request, $section)
    {
        $section = Section::with(['strand', 'schoolYear', 'subjects'])->findOrFail($section);

        return view('registrar.sections.schedule', compact('section'));
    }

    // (re)generate the section's weekly schedule
    public function generateSchedule(Request $request, $section, ScheduleGenerator $generator)
    {
        $section = Section::with('subjects')->findOrFail($section);

        $count = $generator->generate($section);

        AuditLog::record('generated_schedule', 'Section', $section->id, 'Generated schedule for '.$section->section_name);

        return redirect()->route('registrar.sections.showSchedule', $section->id)
            ->with('success', $count
                ? "Schedule generated for {$count} subject(s)."
                : 'Add subjects to this section first, then generate the schedule.');
    }

    /** Shared validation for create/update. */
    private function validateSection(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'strand_id'      => ['required', 'exists:strands,id'],
            'school_year_id' => ['required', 'exists:school_years,id'],
            'grade_level'    => ['required', 'in:11,12'],
            'semester'       => ['required', 'in:1st,2nd'],
            'section_name'   => ['required', 'string', 'max:50'],
            'time_period'    => ['required', 'in:AM,PM'],
            'max_capacity'   => ['required', 'integer', 'min:1', 'max:100'],
            'subject_ids'    => ['array'],
            'subject_ids.*'  => ['exists:subjects,id'],
        ]);
    }
}
