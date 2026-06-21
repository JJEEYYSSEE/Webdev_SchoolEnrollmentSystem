@extends('layouts.registrar')
@section('title', 'Applications')
@section('content')

    @php
        $badge = [
            'pending'    => 'bg-warning-subtle text-warning-emphasis',
            'invalid'    => 'bg-warning-subtle text-warning-emphasis',
            'qualified'  => 'bg-success-subtle text-success-emphasis',
            'waitlisted' => 'bg-info-subtle text-info-emphasis',
        ];
        $flash = [
            'application-returned'   => ['success', 'Application returned to the applicant for correction.'],
            'application-qualified'  => ['success', 'Applicant qualified — School ID issued and emailed.'],
            'application-waitlisted' => ['info', 'Applicant waitlisted — no slots left for that strand.'],
        ];
    @endphp

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Admission Applications</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('registrar.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Applications</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (isset($flash[session('status')]))
        @php [$variant, $message] = $flash[session('status')]; @endphp
        <div class="alert alert-{{ $variant }} alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ $message }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Status filter pills --}}
    <ul class="nav nav-pills gap-2 mb-3">
        @php $tabs = ['' => 'All', 'pending' => 'Pending', 'invalid' => 'Returned', 'waitlisted' => 'Waitlisted', 'qualified' => 'Qualified']; @endphp
        @foreach ($tabs as $value => $label)
            <li class="nav-item">
                <a href="{{ route('registrar.showApplications', array_filter(['status' => $value])) }}"
                   class="nav-link px-3 py-2 fw-semibold {{ ($activeStatus === $value || (! $activeStatus && $value === '')) ? 'active' : 'bg-white text-secondary border' }}">
                    {{ $label }}
                    @if ($value && isset($counts[$value]))
                        <span class="badge bg-light text-dark ms-1">{{ $counts[$value] }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ref No.</th>
                        <th>Applicant</th>
                        <th>Strand</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $app)
                        <tr>
                            <td class="fw-semibold">APP-{{ now()->year }}-{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $app->fullName() }}<div class="small text-muted">{{ $app->user->email }}</div></td>
                            <td>{{ optional($app->strand)->strand_code ?? '—' }}</td>
                            <td class="small text-muted">{{ optional($app->submitted_at)->format('M d, Y') ?? '—' }}</td>
                            <td><span class="badge {{ $badge[$app->status] ?? 'bg-secondary' }} text-capitalize">{{ $app->status === 'invalid' ? 'Returned' : $app->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('registrar.showApplication', $app) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No applications found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $applications->links() }}</div>

@endsection
