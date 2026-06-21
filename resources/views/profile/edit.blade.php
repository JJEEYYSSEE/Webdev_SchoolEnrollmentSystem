@extends(Auth::user()?->isRegistrar() ? 'layouts.registrar' : 'layouts.student')
@section('title', 'Profile Settings')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Profile Settings</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ Auth::user()?->isRegistrar() ? route('registrar.showDashboard') : route('student.showDashboard') }}" class="text-decoration-none">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Profile Settings</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-12 d-flex flex-column gap-4">
            
            {{-- Update Profile Info Card --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password Card --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account Card --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white border-start border-4 border-danger">
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            
        </div>
    </div>

@endsection
