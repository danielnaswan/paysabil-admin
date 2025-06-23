@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Student Profile</h5>
                                <p class="text-sm mb-0">Complete student information and activity overview</p>
                            </div>
                            <div>
                                <a href="{{ route('student.edit', $student->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                </a>
                                <a href="{{ route('student.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-2"></i>Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Profile Information</h6>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <div class="text-center mb-4">
                            @if ($student->user->profile_picture_url)
                                <img src="{{ $student->user->profile_picture_url }}" alt="Profile Picture"
                                    class="avatar avatar-xxl rounded-circle">
                            @else
                                <img src="{{ asset('assets/img/default-avatar.png') }}" alt="Default Profile"
                                    class="avatar avatar-xxl rounded-circle">
                            @endif
                            <h5 class="mt-3 mb-1">{{ $student->full_name }}</h5>
                            <p class="text-sm text-muted">{{ $student->matrix_no }}</p>

                            <!-- Status Badge -->
                            @if ($student->application)
                                @if ($student->application->status === 'APPROVED')
                                    <span class="badge bg-gradient-success">Eligible for Meal Claims</span>
                                @elseif($student->application->status === 'PENDING')
                                    <span class="badge bg-gradient-warning">Application Pending</span>
                                @elseif($student->application->status === 'REJECTED')
                                    <span class="badge bg-gradient-danger">Application Rejected</span>
                                @endif
                            @else
                                <span class="badge bg-gradient-secondary">No Application Submitted</span>
                            @endif
                        </div>

                        <!-- Contact Information -->
                        <div class="info-list">
                            <div class="info-item mb-3">
                                <h6 class="text-sm font-weight-bold mb-1">
                                    <i class="fas fa-envelope text-primary me-2"></i>Email
                                </h6>
                                <p class="text-sm mb-0">{{ $student->user->email }}</p>
                            </div>

                            @if ($student->user->phone_number)
                                <div class="info-item mb-3">
                                    <h6 class="text-sm font-weight-bold mb-1">
                                        <i class="fas fa-phone text-primary me-2"></i>Phone Number
                                    </h6>
                                    <p class="text-sm mb-0">{{ $student->user->phone_number }}</p>
                                </div>
                            @endif

                            <div class="info-item mb-3">
                                <h6 class="text-sm font-weight-bold mb-1">
                                    <i class="fas fa-calendar text-primary me-2"></i>Registration Date
                                </h6>
                                <p class="text-sm mb-0">{{ $student->created_at->format('F d, Y') }}</p>
                            </div>

                            <div class="info-item mb-3">
                                <h6 class="text-sm font-weight-bold mb-1">
                                    <i class="fas fa-clock text-primary me-2"></i>Last Updated
                                </h6>
                                <p class="text-sm mb-0">{{ $student->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- Profile Completion -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-sm font-weight-bold">Profile Completion</span>
                                <span
                                    class="text-sm font-weight-bold">{{ $student->profile_completion_percentage }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-gradient-primary" role="progressbar"
                                    style="width: {{ $student->profile_completion_percentage }}%"
                                    aria-valuenow="{{ $student->profile_completion_percentage }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Status & Activity -->
            <div class="col-md-8">
                <!-- Application Status Card -->
                @if ($student->application)
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6 class="mb-0">Application Status</h6>
                        </div>
                        <div class="card-body pt-4 p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item mb-3">
                                        <h6 class="text-sm font-weight-bold mb-1">Application Title</h6>
                                        <p class="text-sm mb-0">{{ $student->application->title }}</p>
                                    </div>
                                    <div class="info-item mb-3">
                                        <h6 class="text-sm font-weight-bold mb-1">Status</h6>
                                        @if ($student->application->status === 'APPROVED')
                                            <span class="badge bg-gradient-success">Approved</span>
                                        @elseif($student->application->status === 'PENDING')
                                            <span class="badge bg-gradient-warning">Pending Review</span>
                                        @elseif($student->application->status === 'REJECTED')
                                            <span class="badge bg-gradient-danger">Rejected</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item mb-3">
                                        <h6 class="text-sm font-weight-bold mb-1">Submission Date</h6>
                                        <p class="text-sm mb-0">
                                            {{ $student->application->submission_date->format('F d, Y H:i') }}</p>
                                    </div>
                                    @if ($student->application->reviewed_at)
                                        <div class="info-item mb-3">
                                            <h6 class="text-sm font-weight-bold mb-1">Review Date</h6>
                                            <p class="text-sm mb-0">
                                                {{ $student->application->reviewed_at->format('F d, Y H:i') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if ($student->application->description)
                                <div class="info-item mb-3">
                                    <h6 class="text-sm font-weight-bold mb-1">Description</h6>
                                    <p class="text-sm mb-0">{{ $student->application->description }}</p>
                                </div>
                            @endif
                            @if ($student->application->admin_remarks && $student->application->status !== 'PENDING')
                                <div class="info-item">
                                    <h6 class="text-sm font-weight-bold mb-1">Admin Remarks</h6>
                                    <p class="text-sm mb-0 text-muted">{{ $student->application->admin_remarks }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card mb-4">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-file-alt text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No Application Submitted</h5>
                            <p class="text-sm text-muted">This student hasn't submitted an application for meal benefits
                                yet.</p>
                        </div>
                    </div>
                @endif

                <!-- Recent Activity -->
                @if ($student->transactions && $student->transactions->count() > 0)
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Recent Transactions</h6>
                                <span class="badge bg-gradient-info">{{ $student->transactions->count() }} total</span>
                            </div>
                        </div>
                        <div class="card-body pt-4 p-3">
                            <div class="timeline timeline-one-side">
                                @foreach ($student->transactions as $transaction)
                                    <div class="timeline-block mb-3">
                                        <span class="timeline-step">
                                            @if ($transaction->status === 'COMPLETED')
                                                <i class="fas fa-check text-success"></i>
                                            @elseif($transaction->status === 'PENDING')
                                                <i class="fas fa-clock text-warning"></i>
                                            @else
                                                <i class="fas fa-times text-danger"></i>
                                            @endif
                                        </span>
                                        <div class="timeline-content">
                                            <h6 class="text-dark text-sm font-weight-bold mb-0">
                                                {{ $transaction->meal_details }}
                                            </h6>
                                            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                                {{ $transaction->vendor->business_name ?? 'Unknown Vendor' }}
                                            </p>
                                            <p class="text-sm mt-3 mb-2">
                                                <span
                                                    class="badge badge-sm bg-gradient-{{ $transaction->status === 'COMPLETED' ? 'success' : ($transaction->status === 'PENDING' ? 'warning' : 'danger') }}">
                                                    {{ $transaction->status }}
                                                </span>
                                                <span class="text-secondary">{{ $transaction->formatted_amount }}</span>
                                            </p>
                                            <small
                                                class="text-secondary">{{ $transaction->transaction_date->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-receipt text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No Transaction History</h5>
                            <p class="text-sm text-muted">This student hasn't made any meal claims yet.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Activity Statistics -->
        @if ($student->is_eligible)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6 class="mb-0">Activity Summary</h6>
                        </div>
                        <div class="card-body pt-4 p-3">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="border-radius-md bg-gradient-info p-3 text-center">
                                        <h4 class="text-white font-weight-bolder">
                                            {{ $student->transaction_count_today }}
                                        </h4>
                                        <p class="text-white text-sm opacity-8 mb-0">Claims Today</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="border-radius-md bg-gradient-success p-3 text-center">
                                        <h4 class="text-white font-weight-bolder">
                                            {{ $student->transactions->where('status', 'COMPLETED')->count() }}
                                        </h4>
                                        <p class="text-white text-sm opacity-8 mb-0">Total Completed</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="border-radius-md bg-gradient-warning p-3 text-center">
                                        <h4 class="text-white font-weight-bolder">
                                            {{ $student->transactions->where('status', 'PENDING')->count() }}
                                        </h4>
                                        <p class="text-white text-sm opacity-8 mb-0">Pending Claims</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="border-radius-md bg-gradient-primary p-3 text-center">
                                        <h4 class="text-white font-weight-bolder">
                                            {{ $student->ratings->count() }}
                                        </h4>
                                        <p class="text-white text-sm opacity-8 mb-0">Reviews Given</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
