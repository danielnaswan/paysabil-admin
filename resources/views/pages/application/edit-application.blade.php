@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Review Application: {{ $application->title }}</h6>
                            <div class="ms-auto">
                                <span class="badge badge-lg {{ $application->status_badge_class }}">
                                    {{ $application->status }}
                                </span>
                                @if ($application->is_overdue)
                                    <span class="badge badge-sm bg-danger ms-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                                    </span>
                                @endif
                            </div>
                        </div>
                        <p class="text-sm mb-0 mt-2">Review and update application status</p>
                    </div>
                    <div class="card-body pt-4 p-3">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span class="alert-text">
                                        <strong>Validation Error:</strong> {{ $errors->first() }}
                                    </span>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('application.update', $application->id) }}" method="POST"
                            enctype="multipart/form-data" role="form text-left" id="reviewForm">
                            @csrf
                            @method('PUT')

                            <!-- Student Information Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-primary">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-user me-2"></i>Student Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
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
                                                    <p class="text-sm text-muted mb-0">
                                                        {{ $application->student->matrix_no }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label">Email Address</label>
                                                <input class="form-control" type="text"
                                                    value="{{ $application->student->user->email }}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label">Phone Number</label>
                                                <input class="form-control" type="text"
                                                    value="{{ $application->student->user->phone_number ?? 'Not provided' }}"
                                                    disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Profile Completion</label>
                                                <div class="progress-wrapper">
                                                    <div class="progress-info">
                                                        <div class="progress-percentage">
                                                            <span
                                                                class="text-xs font-weight-bold">{{ $application->student->profile_completion_percentage }}%</span>
                                                        </div>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-{{ $application->student->profile_completion_percentage >= 80 ? 'success' : ($application->student->profile_completion_percentage >= 50 ? 'warning' : 'danger') }}"
                                                            role="progressbar"
                                                            aria-valuenow="{{ $application->student->profile_completion_percentage }}"
                                                            aria-valuemin="0" aria-valuemax="100"
                                                            style="width: {{ $application->student->profile_completion_percentage }}%;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Member Since</label>
                                                <input class="form-control" type="text"
                                                    value="{{ $application->student->created_at->format('M d, Y') }}"
                                                    disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Application Details Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-info">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Application Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Submission Date</label>
                                                <input class="form-control" type="text"
                                                    value="{{ $application->submission_date->format('d M Y, H:i') }}"
                                                    disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Days Pending</label>
                                                <input class="form-control" type="text"
                                                    value="{{ $application->days_pending }} days" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="title" class="form-control-label">
                                            Application Title <span class="text-danger">*</span>
                                        </label>
                                        <input class="form-control" type="text" id="title" name="title"
                                            value="{{ old('title', $application->title) }}" maxlength="255" required>
                                        @error('title')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="description" class="form-control-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000">{{ old('description', $application->description) }}</textarea>
                                        @error('description')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Document Section -->
                            @if ($application->document_url)
                                <div class="card border-light mb-4">
                                    <div class="card-header bg-gradient-warning">
                                        <h6 class="text-white mb-0">
                                            <i class="fas fa-paperclip me-2"></i>Supporting Document
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="card border-light">
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
                                                            <a href="{{ $application->document_url }}" target="_blank"
                                                                class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-eye me-1"></i>View
                                                            </a>
                                                            <a href="{{ route('application.downloadDocument', $application) }}"
                                                                class="btn btn-primary btn-sm">
                                                                <i class="fas fa-download me-1"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <div class="form-group">
                                                <label for="document" class="form-control-label">
                                                    Replace Document <span class="text-muted">(Optional)</span>
                                                </label>
                                                <input class="form-control" type="file" id="document"
                                                    name="document" accept="application/pdf">
                                                <small class="text-muted">Only upload if you need to replace the current
                                                    document. Maximum file size: 10MB</small>
                                                @error('document')
                                                    {{-- <p class="text-danger text-xs mt-2">{{ $message }}</p> --}}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Review Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-success">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-user-shield me-2"></i>Admin Review
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status" class="form-control-label">
                                                    Application Status <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" id="status" name="status" required>
                                                    <option value="PENDING"
                                                        {{ old('status', $application->status) === 'PENDING' ? 'selected' : '' }}>
                                                        Pending Review
                                                    </option>
                                                    <option value="APPROVED"
                                                        {{ old('status', $application->status) === 'APPROVED' ? 'selected' : '' }}>
                                                        Approved
                                                    </option>
                                                    <option value="REJECTED"
                                                        {{ old('status', $application->status) === 'REJECTED' ? 'selected' : '' }}>
                                                        Rejected
                                                    </option>
                                                </select>
                                                @error('status')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            @if ($application->reviewed_at)
                                                <div class="form-group">
                                                    <label class="form-control-label">Previously Reviewed</label>
                                                    <input class="form-control" type="text"
                                                        value="{{ $application->reviewed_at->format('d M Y, H:i') }} by {{ $application->reviewer->name ?? 'Unknown' }}"
                                                        disabled>
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <label class="form-control-label">Review Status</label>
                                                    <input class="form-control" type="text" value="Not yet reviewed"
                                                        disabled>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="admin_remarks" class="form-control-label">
                                            Admin Remarks <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="admin_remarks" name="admin_remarks" rows="4" maxlength="500"
                                            placeholder="Provide detailed remarks about your decision..." required>{{ old('admin_remarks', $application->admin_remarks) }}</textarea>
                                        <small class="text-muted">This will be visible to the student. Maximum 500
                                            characters.</small>
                                        @error('admin_remarks')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="bg-light p-3 border-radius-md">
                                                <h6 class="mb-2">Quick Actions:</h6>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-success"
                                                        onclick="quickApprove()">
                                                        <i class="fas fa-check me-1"></i>Quick Approve
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger"
                                                        onclick="quickReject()">
                                                        <i class="fas fa-times me-1"></i>Quick Reject
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning"
                                                        onclick="requestMoreInfo()">
                                                        <i class="fas fa-question me-1"></i>Request More Info
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Review History -->
                            @if ($application->reviewed_at || $application->admin_remarks)
                                <div class="card border-light mb-4">
                                    <div class="card-header bg-gradient-dark">
                                        <h6 class="text-white mb-0">
                                            <i class="fas fa-history me-2"></i>Review History
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="timeline timeline-one-side">
                                            <div class="timeline-block mb-3">
                                                <span class="timeline-step">
                                                    <i class="fas fa-paper-plane text-info"></i>
                                                </span>
                                                <div class="timeline-content">
                                                    <h6 class="text-dark text-sm font-weight-bold mb-0">Application
                                                        Submitted</h6>
                                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                                        {{ $application->submission_date->format('d M Y, H:i') }}
                                                    </p>
                                                    <p class="text-xs text-muted mb-0">
                                                        By {{ $application->student->full_name }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($application->reviewed_at)
                                                <div class="timeline-block mb-3">
                                                    <span class="timeline-step">
                                                        <i
                                                            class="fas fa-{{ $application->status === 'APPROVED' ? 'check text-success' : ($application->status === 'REJECTED' ? 'times text-danger' : 'clock text-warning') }}"></i>
                                                    </span>
                                                    <div class="timeline-content">
                                                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                                                            Application {{ $application->status }}
                                                        </h6>
                                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                                            {{ $application->reviewed_at->format('d M Y, H:i') }}
                                                        </p>
                                                        <p class="text-xs text-muted mb-0">
                                                            By {{ $application->reviewer->name ?? 'Admin' }}
                                                        </p>
                                                        @if ($application->admin_remarks)
                                                            <div class="mt-2">
                                                                <small class="text-muted">Previous Remarks:</small>
                                                                <p class="text-sm mb-0">{{ $application->admin_remarks }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Guidelines Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-secondary">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Review Guidelines
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0 text-sm">
                                        <li><strong>Approved:</strong> Student will gain access to the Sabil Al-Hikmah
                                            program</li>
                                        <li><strong>Rejected:</strong> Student will be notified and can resubmit with
                                            corrections</li>
                                        <li><strong>Pending:</strong> Application remains in review queue</li>
                                        <li>Always provide clear and constructive remarks for your decision</li>
                                        <li>Students will receive email notifications about status changes</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="card border-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">
                                                <span class="text-danger">*</span> Required fields
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('application.index') }}" class="btn btn-light me-2">
                                                <i class="fas fa-arrow-left me-1"></i>Back to List
                                            </a>
                                            <a href="{{ route('application.show', $application->id) }}"
                                                class="btn btn-outline-info me-2">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </a>
                                            <button type="submit" class="btn bg-gradient-primary" id="submitBtn">
                                                <i class="fas fa-save me-1"></i>Update Application
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
        }

        .timeline-one-side .timeline-block {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-one-side .timeline-step {
            position: absolute;
            left: 0;
            top: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 50%;
        }

        .timeline-one-side .timeline-content {
            margin-left: 50px;
            padding-bottom: 20px;
            border-left: 2px solid #dee2e6;
            padding-left: 20px;
        }

        .timeline-one-side .timeline-block:last-child .timeline-content {
            border-left: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const remarksTextarea = document.getElementById('admin_remarks');
            const submitBtn = document.getElementById('submitBtn');

            // Form submission handler
            document.getElementById('reviewForm').addEventListener('submit', function(e) {
                // Validate status and remarks
                if (!statusSelect.value) {
                    e.preventDefault();
                    alert('Please select an application status.');
                    return;
                }

                if (!remarksTextarea.value.trim()) {
                    e.preventDefault();
                    alert('Please provide admin remarks for your decision.');
                    remarksTextarea.focus();
                    return;
                }

                // Confirm submission
                const status = statusSelect.options[statusSelect.selectedIndex].text;
                if (!confirm(`Are you sure you want to ${status.toLowerCase()} this application?`)) {
                    e.preventDefault();
                    return;
                }

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
                submitBtn.disabled = true;
            });

            // Status change handler
            statusSelect.addEventListener('change', function() {
                updateSubmitButton();
            });

            function updateSubmitButton() {
                const status = statusSelect.value;

                if (status === 'APPROVED') {
                    submitBtn.className = 'btn bg-gradient-success';
                    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Approve Application';
                } else if (status === 'REJECTED') {
                    submitBtn.className = 'btn bg-gradient-danger';
                    submitBtn.innerHTML = '<i class="fas fa-times me-2"></i>Reject Application';
                } else {
                    submitBtn.className = 'btn bg-gradient-primary';
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Application';
                }
            }

            // Initialize button state
            updateSubmitButton();
        });

        function quickApprove() {
            document.getElementById('status').value = 'APPROVED';
            document.getElementById('admin_remarks').value =
                'Application approved. All requirements have been met and the student is eligible for the Sabil Al-Hikmah program.';

            // Update button appearance
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.className = 'btn bg-gradient-success';
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Approve Application';
        }

        function quickReject() {
            document.getElementById('status').value = 'REJECTED';
            document.getElementById('admin_remarks').value =
                'Application rejected. Please review the requirements and resubmit with the necessary corrections.';

            // Update button appearance
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.className = 'btn bg-gradient-danger';
            submitBtn.innerHTML = '<i class="fas fa-times me-2"></i>Reject Application';
        }

        function requestMoreInfo() {
            document.getElementById('status').value = 'PENDING';
            document.getElementById('admin_remarks').value =
                'Additional information required. Please provide more details or documentation to support your application.';

            // Update button appearance
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.className = 'btn bg-gradient-primary';
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Application';
        }
    </script>
@endsection
