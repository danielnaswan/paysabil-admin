@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <div class="me-auto">
                                <h5 class="mb-0">Application Details</h5>
                                <p class="text-sm mb-0">Review and manage application information</p>
                            </div>
                            <div class="ms-auto">
                                <span class="badge badge-lg {{ $application->status_badge_class }}">
                                    {{ $application->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Application Title and Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h4 class="mb-2">{{ $application->title }}</h4>
                                        @if ($application->is_overdue)
                                            <div class="alert alert-warning d-inline-flex align-items-center">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                This application is overdue ({{ $application->days_pending }} days pending)
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">Application ID: #{{ $application->id }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Student Information Card -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border shadow-none h-100">
                                    <div class="card-header bg-gradient-primary">
                                        <h6 class="text-white mb-0">
                                            <i class="fas fa-user me-2"></i>Student Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            @if ($application->student->user->profile_picture_url)
                                                <img src="{{ $application->student->user->profile_picture_url }}"
                                                    alt="Profile" class="avatar avatar-lg me-3">
                                            @else
                                                <div class="avatar avatar-lg bg-gradient-secondary me-3">
                                                    <span class="text-white font-weight-bold">
                                                        {{ substr($application->student->full_name, 0, 2) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $application->student->full_name }}</h6>
                                                <p class="text-sm text-muted mb-0">{{ $application->student->matrix_no }}
                                                </p>
                                            </div>
                                        </div>

                                        <hr class="my-3">

                                        <div class="row">
                                            <div class="col-12">
                                                <p class="text-sm mb-2">
                                                    <strong>Email:</strong>
                                                    <a href="mailto:{{ $application->student->user->email }}"
                                                        class="text-primary">
                                                        {{ $application->student->user->email }}
                                                    </a>
                                                </p>
                                                @if ($application->student->user->phone_number)
                                                    <p class="text-sm mb-2">
                                                        <strong>Phone:</strong>
                                                        <a href="tel:{{ $application->student->user->phone_number }}"
                                                            class="text-primary">
                                                            {{ $application->student->user->phone_number }}
                                                        </a>
                                                    </p>
                                                @endif
                                                <p class="text-sm mb-0">
                                                    <strong>Profile Completion:</strong>
                                                    {{ $application->student->profile_completion_percentage }}%
                                                </p>
                                                <div class="progress progress-sm mt-1">
                                                    <div class="progress-bar bg-gradient-primary"
                                                        style="width: {{ $application->student->profile_completion_percentage }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Application Timeline -->
                            <div class="col-md-6">
                                <div class="card border shadow-none h-100">
                                    <div class="card-header bg-gradient-info">
                                        <h6 class="text-white mb-0">
                                            <i class="fas fa-clock me-2"></i>Application Timeline
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="timeline timeline-one-side">
                                            <div class="timeline-block mb-3">
                                                <span class="timeline-step">
                                                    <i class="fas fa-paper-plane text-success"></i>
                                                </span>
                                                <div class="timeline-content">
                                                    <h6 class="text-dark text-sm font-weight-bold mb-0">Application
                                                        Submitted</h6>
                                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                                        {{ $application->submission_date->format('d M Y, H:i') }}
                                                    </p>
                                                    <p class="text-xs text-muted mb-0">
                                                        {{ $application->submission_date->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($application->reviewed_at)
                                                <div class="timeline-block mb-3">
                                                    <span class="timeline-step">
                                                        <i
                                                            class="fas fa-{{ $application->status === 'APPROVED' ? 'check' : 'times' }} 
                                                       text-{{ $application->status === 'APPROVED' ? 'success' : 'danger' }}"></i>
                                                    </span>
                                                    <div class="timeline-content">
                                                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                                                            Application {{ $application->status }}
                                                        </h6>
                                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                                            {{ $application->reviewed_at->format('d M Y, H:i') }}
                                                        </p>
                                                        @if ($application->reviewer)
                                                            <p class="text-xs text-muted mb-0">
                                                                Reviewed by {{ $application->reviewer->name }}
                                                            </p>
                                                        @endif
                                                        @if ($application->review_turnaround_time)
                                                            <p class="text-xs text-muted mb-0">
                                                                Processing time:
                                                                {{ number_format($application->review_turnaround_time, 1) }}
                                                                hours
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="timeline-block mb-3">
                                                    <span class="timeline-step">
                                                        <i class="fas fa-hourglass-half text-warning"></i>
                                                    </span>
                                                    <div class="timeline-content">
                                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Pending Review
                                                        </h6>
                                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                                            Waiting for admin review
                                                        </p>
                                                        <p class="text-xs text-muted mb-0">
                                                            {{ $application->days_pending }} days pending
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Application Content -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border shadow-none">
                                    <div class="card-header bg-gradient-secondary">
                                        <h6 class="text-white mb-0">
                                            <i class="fas fa-file-alt me-2"></i>Application Content
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($application->description)
                                            <div class="mb-4">
                                                <h6 class="mb-2">Description</h6>
                                                <div class="bg-gray-100 p-3 border-radius-md">
                                                    <p class="mb-0">{{ $application->description }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($application->admin_remarks)
                                            <div class="mb-4">
                                                <h6 class="mb-2">
                                                    <i class="fas fa-comment-dots me-2"></i>Admin Remarks
                                                </h6>
                                                <div class="alert alert-info">
                                                    <p class="mb-0">{{ $application->admin_remarks }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Supporting Document -->
                                        <div class="mb-4">
                                            <h6 class="mb-3">
                                                <i class="fas fa-paperclip me-2"></i>Supporting Document
                                            </h6>
                                            @if ($application->document_url && $documentExists)
                                                <div class="card border-primary">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <div class="icon icon-lg bg-gradient-danger">
                                                                    <i class="fas fa-file-pdf text-white"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1">{{ $application->document_name }}</h6>
                                                                <p class="text-sm text-muted mb-0">
                                                                    Size: {{ $application->document_size_human }}
                                                                </p>
                                                            </div>
                                                            <div class="ms-auto">
                                                                <div class="btn-group">
                                                                    <a href="{{ $application->document_url }}"
                                                                        target="_blank"
                                                                        class="btn btn-outline-primary btn-sm">
                                                                        <i class="fas fa-eye me-2"></i>View
                                                                    </a>
                                                                    <a href="{{ route('application.downloadDocument', $application) }}"
                                                                        class="btn btn-primary btn-sm">
                                                                        <i class="fas fa-download me-2"></i>Download
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning d-flex align-items-center">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Document file is missing or corrupted
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between flex-wrap">
                                    <div class="mb-2">
                                        <a href="{{ route('application.index') }}" class="btn btn-light">
                                            <i class="fas fa-arrow-left me-2"></i>Back to List
                                        </a>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if ($application->canBeReviewed())
                                            <a href="{{ route('application.edit', $application->id) }}"
                                                class="btn btn-primary">
                                                <i class="fas fa-edit me-2"></i>Review Application
                                            </a>
                                        @else
                                            <a href="{{ route('application.edit', $application->id) }}"
                                                class="btn btn-outline-primary">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                        @endif

                                        @if ($application->status !== 'APPROVED')
                                            <form action="{{ route('application.destroy', $application->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this application? This action cannot be undone.')">
                                                    <i class="fas fa-trash me-2"></i>Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
