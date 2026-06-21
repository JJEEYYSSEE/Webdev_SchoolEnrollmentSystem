@extends(Auth::user()?->isRegistrar() ? 'layouts.registrar' : 'layouts.student')
@section('title', 'Dashboard')
@section('content')

    <div class="container py-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center py-5 bg-white text-dark">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-check-circle-fill text-success fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark">Welcome Back!</h5>
                <p class="text-muted small mb-4">You have successfully authenticated. Let's redirect you to your portal.</p>
                <a href="{{ Auth::user()?->isRegistrar() ? route('registrar.showDashboard') : route('student.showDashboard') }}" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                    <span>Go to Dashboard</span> <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

@endsection
