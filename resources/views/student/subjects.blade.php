@extends('layouts.student')
@section('title', 'My Subjects')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">My Subjects</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('student.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Subjects</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (! $enrollment)
        <div class="alert alert-info border-0 d-flex gap-3 mb-4 shadow-sm rounded-3">
            <i class="bi bi-info-circle-fill fs-4 text-info"></i>
            <div>
                <div class="fw-bold text-dark">No Enrollment Record</div>
                <div class="small text-muted">No enrollment found for the active semester. Complete your enrollment registration form first to see your assigned subjects.</div>
            </div>
        </div>
    @elseif ($subjects->isEmpty())
        <div class="alert alert-info border-0 d-flex gap-3 mb-4 shadow-sm rounded-3">
            <i class="bi bi-info-circle-fill fs-4 text-info"></i>
            <div>
                <div class="fw-bold text-dark">No Subjects Attached</div>
                <div class="small text-muted">There are no subjects attached to your enrollment record yet. Please wait for the registrar to approve your application.</div>
            </div>
        </div>
    @else
        @php
            $badgeClass = [
                'enrolled' => 'bg-primary text-white',
                'passed'   => 'bg-success text-white',
                'failed'   => 'bg-danger text-white',
                'dropped'  => 'bg-secondary text-white',
            ];
        @endphp

        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark fs-5">
                    <i class="bi bi-book-fill text-success fs-5"></i>
                    <span>{{ $enrollment->section->section_name ?? 'Enrolled Subjects' }}</span>
                </span>
                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 fw-bold" style="font-size:0.75rem;">
                    {{ $subjects->sum('units') }} Total Units
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Code</th>
                            <th>Subject Name</th>
                            <th class="text-center">Units</th>
                            <th class="text-center">Grade</th>
                            <th class="text-center px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $subject)
                            <tr>
                                <td class="px-4 text-muted fw-bold">{{ $subject->subject_code }}</td>
                                <td class="fw-semibold text-dark">{{ $subject->subject_name }}</td>
                                <td class="text-center fw-bold text-muted">{{ $subject->units }}</td>
                                <td class="text-center">
                                    @if ($subject->pivot->grade !== null)
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1.5 fw-bold">
                                            {{ number_format($subject->pivot->grade, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center px-4">
                                    <span class="badge {{ $badgeClass[$subject->pivot->status] ?? 'bg-secondary text-white' }} px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">
                                        {{ ucfirst($subject->pivot->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection
