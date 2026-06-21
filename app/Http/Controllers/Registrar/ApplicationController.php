<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Section;
use App\Models\Student;
use App\Notifications\ApplicationQualifiedNotification;
use App\Notifications\ApplicationReturnedNotification;
use App\Notifications\ApplicationWaitlistedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /** List submitted applications (pending / invalid / qualified / waitlisted). */
    public function showApplications(Request $request): View
    {
        $status = $request->query('status');

        $applications = Application::with(['user', 'strand'])
            ->where('status', '!=', 'draft')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('submitted_at')
            ->paginate(20)
            ->withQueryString();

        $counts = Application::where('status', '!=', 'draft')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('registrar.applications.index', [
            'applications' => $applications,
            'counts'       => $counts,
            'activeStatus' => $status,
        ]);
    }

    /** Review one application — details, documents, and slot availability. */
    public function showApplication(Application $application): View
    {
        $application->load(['user', 'strand', 'documents', 'reviewer']);

        $seats    = $this->seatsFor($application);
        $admitted = $this->admittedFor($application);

        return view('registrar.applications.show', [
            'application' => $application,
            'seats'       => $seats,
            'admitted'    => $admitted,
            'hasSlot'     => $seats > 0 && $admitted < $seats,
        ]);
    }

    /** Return for compliance — mark invalid with a reason (applicant reuploads). */
    public function returnApplication(Request $request, Application $application): RedirectResponse
    {
        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);

        if (! $application->isPending()) {
            return back()->withErrors(['remarks' => 'Only pending applications can be returned.']);
        }

        $application->update([
            'status'      => 'invalid',
            'remarks'     => $validated['remarks'],
            'reviewed_by' => $request->user()->registrar?->id,
            'reviewed_at' => now(),
        ]);

        $application->user->notify(new ApplicationReturnedNotification($validated['remarks']));

        return redirect()
            ->route('registrar.showApplications')
            ->with('status', 'application-returned');
    }

    /**
     * Qualify an application: issue a School ID + default password, create the
     * student profile, and admit the applicant. If the strand+grade has no
     * remaining section capacity, waitlist them instead.
     */
    public function qualifyApplication(Request $request, Application $application): RedirectResponse
    {
        if (! $application->isPending()) {
            return back()->withErrors(['qualify' => 'Only pending applications can be qualified.']);
        }

        $registrarId = $request->user()->registrar?->id;

        // No remaining slot for this strand + grade → waitlist.
        if (! ($this->seatsFor($application) > 0 && $this->admittedFor($application) < $this->seatsFor($application))) {
            $application->update([
                'status'      => 'waitlisted',
                'reviewed_by' => $registrarId,
                'reviewed_at' => now(),
            ]);

            $application->user->notify(
                new ApplicationWaitlistedNotification(optional($application->strand)->strand_code ?? 'your strand')
            );

            return redirect()
                ->route('registrar.showApplications')
                ->with('status', 'application-waitlisted');
        }

        $schoolId = Student::generateNumber();
        $plainPassword = strtoupper(Str::random(8));

        DB::transaction(function () use ($application, $schoolId, $plainPassword, $registrarId) {
            Student::create([
                'user_id'        => $application->user_id,
                'student_number' => $schoolId,
                'first_name'     => $application->first_name,
                'last_name'      => $application->last_name,
                'phone'          => $application->mobile,
                'birthdate'      => $application->birthdate,
                'address'        => trim(implode(', ', array_filter([
                    $application->current_address,
                    $application->current_barangay,
                    $application->current_city,
                    $application->current_province,
                ]))),
                'strand_id'      => $application->strand_id,
                'grade_level'    => $application->grade_level,
            ]);

            $user = $application->user;
            $user->school_id = $schoolId;
            $user->password = $plainPassword;          // hashed by the cast
            $user->must_change_password = true;
            $user->save();

            $application->update([
                'status'      => 'qualified',
                'reviewed_by' => $registrarId,
                'reviewed_at' => now(),
            ]);
        });

        $application->user->notify(new ApplicationQualifiedNotification($schoolId, $plainPassword));

        return redirect()
            ->route('registrar.showApplications')
            ->with('status', 'application-qualified');
    }

    // ── Capacity helpers ─────────────────────────────────────────────────────

    /** Total seats across Grade-11 sections of the application's strand. */
    private function seatsFor(Application $application): int
    {
        return (int) Section::where('strand_id', $application->strand_id)
            ->where('grade_level', $application->grade_level)
            ->sum('max_capacity');
    }

    /** Students already admitted into that strand + grade. */
    private function admittedFor(Application $application): int
    {
        return Student::where('strand_id', $application->strand_id)
            ->where('grade_level', $application->grade_level)
            ->count();
    }
}
