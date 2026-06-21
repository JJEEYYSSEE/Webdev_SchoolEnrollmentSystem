<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Strand;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /** Required admission documents: type => human label. */
    private array $documentTypes = [
        'sf10'       => 'SF10 / Form 137',
        'sf9'        => 'SF9 / Report Card (Grade 10)',
        'good_moral' => 'Certificate of Good Moral Character',
        'psa'        => 'PSA Birth Certificate',
        'photo'      => '2x2 ID Photo',
    ];

    /** Show the wizard (draft/invalid) or bounce to the status page (submitted). */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmitted()) {
            return redirect()->route('dashboard');
        }

        $application = $this->draftFor($user);

        if (in_array($application->status, ['pending', 'qualified'])) {
            return redirect()->route('application.status');
        }

        return view('application.wizard', [
            'application'   => $application,
            'strands'       => Strand::orderBy('strand_code')->get(),
            'documentTypes' => $this->documentTypes,
        ]);
    }

    /** Save a single wizard step (forward validates, back is lenient). */
    public function save(Request $request): RedirectResponse
    {
        $user        = $request->user();
        $application = $this->draftFor($user);

        if (in_array($application->status, ['pending', 'qualified'])) {
            return redirect()->route('application.status');
        }

        $step = (int) $request->input('step', 1);

        if ($request->input('direction') === 'back') {
            $application->update(['current_step' => max(1, $step - 1)]);

            return redirect()->route('application.show');
        }

        match ($step) {
            1       => $this->saveStep1($request, $application),
            2       => $this->saveStep2($request, $application),
            3       => $this->saveStep3($request, $application),
            default => null,
        };

        $application->update(['current_step' => min(4, $step + 1)]);

        return redirect()->route('application.show');
    }

    /** Finalize the application for registrar review. */
    public function submit(Request $request): RedirectResponse
    {
        $user        = $request->user();
        $application = $user->application;

        if (! $application || in_array($application->status, ['pending', 'qualified'])) {
            return redirect()->route('application.status');
        }

        if ($application->documents()->count() < count($this->documentTypes)) {
            return redirect()->route('application.show')
                ->withErrors(['documents' => 'Please complete all required documents before submitting.']);
        }

        $application->update([
            'status'       => 'pending',
            'submitted_at' => now(),
            'current_step' => 4,
        ]);

        return redirect()->route('application.status');
    }

    /** Status tracker for a submitted (pending/qualified) application. */
    public function status(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmitted()) {
            return redirect()->route('dashboard');
        }

        $application = $user->application;

        if (! $application || in_array($application->status, ['draft', 'invalid'])) {
            return redirect()->route('application.show');
        }

        return view('application.status', ['application' => $application]);
    }

    // ── Step handlers ──────────────────────────────────────────────────────

    private function saveStep1(Request $request, Application $application): void
    {
        $data = $request->validate([
            'lrn'             => ['nullable', 'digits:12'],
            'first_name'      => ['required', 'string', 'max:100'],
            'middle_name'     => ['nullable', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'extension_name'  => ['nullable', 'string', 'max:20'],
            'birthdate'       => ['required', 'date', 'before:today'],
            'sex'             => ['required', 'in:Male,Female'],
            'place_of_birth'  => ['required', 'string', 'max:255'],
            'civil_status'    => ['nullable', 'string', 'max:50'],
            'mother_tongue'   => ['required', 'string', 'max:100'],
            'religion'        => ['nullable', 'string', 'max:100'],
            'ip_community'    => ['nullable', 'string', 'max:255'],
            'disability_type' => ['nullable', 'string', 'max:255'],
            'household_id'    => ['nullable', 'string', 'max:100'],
            'mobile'          => ['required', 'string', 'max:20'],
            'current_address'  => ['required', 'string', 'max:255'],
            'current_barangay' => ['required', 'string', 'max:100'],
            'current_city'     => ['required', 'string', 'max:100'],
            'current_province' => ['required', 'string', 'max:100'],
            'current_zip'      => ['nullable', 'string', 'max:10'],
            'permanent_address'  => ['nullable', 'string', 'max:255'],
            'permanent_barangay' => ['nullable', 'string', 'max:100'],
            'permanent_city'     => ['nullable', 'string', 'max:100'],
            'permanent_province' => ['nullable', 'string', 'max:100'],
            'permanent_zip'      => ['nullable', 'string', 'max:10'],
            'father_name'           => ['nullable', 'string', 'max:150'],
            'father_contact'        => ['nullable', 'string', 'max:20'],
            'mother_name'           => ['nullable', 'string', 'max:150'],
            'mother_contact'        => ['nullable', 'string', 'max:20'],
            'guardian_name'         => ['nullable', 'string', 'max:150'],
            'guardian_relationship' => ['nullable', 'string', 'max:50'],
            'guardian_contact'      => ['nullable', 'string', 'max:20'],
        ]);

        $data['is_ip']          = $request->boolean('is_ip');
        $data['has_disability'] = $request->boolean('has_disability');
        $data['is_4ps']         = $request->boolean('is_4ps');
        $data['permanent_same'] = $request->boolean('permanent_same');

        $application->update($data);
    }

    private function saveStep2(Request $request, Application $application): void
    {
        $data = $request->validate([
            'jhs_name'                  => ['required', 'string', 'max:255'],
            'jhs_school_id'             => ['nullable', 'string', 'max:100'],
            'jhs_year_graduated'        => ['required', 'string', 'max:10'],
            'general_average'           => ['required', 'numeric', 'min:0', 'max:100'],
            'elementary_name'           => ['required', 'string', 'max:255'],
            'elementary_year_graduated' => ['nullable', 'string', 'max:10'],
            'previous_school'           => ['nullable', 'string', 'max:255'],
            'strand_id'                 => ['required', 'exists:strands,id'],
        ]);

        $data['is_returning']  = $request->boolean('is_returning');
        $data['is_transferee'] = $request->boolean('is_transferee');
        $data['grade_level']   = '11';

        $application->update($data);
    }

    private function saveStep3(Request $request, Application $application): void
    {
        $request->validate([
            'documents'   => ['array'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        foreach ($request->file('documents', []) as $type => $file) {
            if (! array_key_exists($type, $this->documentTypes)) {
                continue;
            }

            $path = $file->store("applications/{$application->id}", 'public');

            ApplicationDocument::updateOrCreate(
                ['application_id' => $application->id, 'type' => $type],
                ['path' => $path, 'original_name' => $file->getClientOriginalName()],
            );
        }

        $present = $application->documents()->pluck('type')->all();
        $missing = array_diff(array_keys($this->documentTypes), $present);

        if ($missing) {
            $labels = array_map(fn ($t) => $this->documentTypes[$t], $missing);

            throw \Illuminate\Validation\ValidationException::withMessages([
                'documents' => 'Please upload: '.implode(', ', $labels),
            ]);
        }
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function draftFor(User $user): Application
    {
        return Application::firstOrCreate(
            ['user_id' => $user->id],
            $this->prefill($user),
        );
    }

    private function prefill(User $user): array
    {
        $parts = preg_split('/\s+/', trim($user->name), 2);

        return [
            'first_name'   => $parts[0] ?? '',
            'last_name'    => $parts[1] ?? '',
            'birthdate'    => $user->birthdate,
            'email'        => $user->email,
            'status'       => 'draft',
            'current_step' => 1,
            'grade_level'  => '11',
        ];
    }
}
