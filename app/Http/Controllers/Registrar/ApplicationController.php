<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /** List submitted applications (pending / invalid / qualified). */
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

    /** Review one application — form details + uploaded documents. */
    public function showApplication(Application $application): View
    {
        $application->load(['user', 'strand', 'documents', 'reviewer']);

        return view('registrar.applications.show', [
            'application' => $application,
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

        // Email C (return-for-compliance notification) is wired in Phase 2.

        return redirect()
            ->route('registrar.showApplications')
            ->with('status', 'application-returned');
    }
}
