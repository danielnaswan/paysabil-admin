@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Edit Student: {{ $student->full_name }}</h6>
                            <div class="ms-auto">
                                <a href="{{ route('student.show', $student->id) }}" class="btn btn-outline-info btn-sm me-2">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <a href="{{ route('student.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i>Back to List
                                </a>
                            </div>
                        </div>
                        <p class="text-sm mb-0 mt-2">Update student information and account details</p>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <form action="{{ route('student.update', $student->id) }}" method="POST"
                            enctype="multipart/form-data" role="form text-left">
                            @csrf
                            @method('PUT')

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

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" id="alert-success"
                                    role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <span class="alert-text">{{ session('success') }}</span>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                        <i class="fa fa-close" aria-hidden="true"></i>
                                    </button>
                                </div>
                            @endif

                            <!-- Student Status Overview -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-secondary">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Student Status
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold">Application Status</h6>
                                                @if ($student->application)
                                                    @if ($student->application->status === 'APPROVED')
                                                        <span class="badge badge-lg bg-gradient-success">
                                                            <i class="fas fa-check me-1"></i>Approved
                                                        </span>
                                                    @elseif($student->application->status === 'PENDING')
                                                        <span class="badge badge-lg bg-gradient-warning">
                                                            <i class="fas fa-clock me-1"></i>Pending
                                                        </span>
                                                    @elseif($student->application->status === 'REJECTED')
                                                        <span class="badge badge-lg bg-gradient-danger">
                                                            <i class="fas fa-times me-1"></i>Rejected
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-lg bg-gradient-secondary">No Application</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold">Profile Completion</h6>
                                                @php
                                                    $completion = $student->profile_completion_percentage ?? 0;
                                                @endphp
                                                <div class="progress-wrapper">
                                                    <div class="progress-info">
                                                        <div class="progress-percentage">
                                                            <span
                                                                class="text-xs font-weight-bold">{{ $completion }}%</span>
                                                        </div>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-{{ $completion >= 80 ? 'success' : ($completion >= 50 ? 'warning' : 'danger') }}"
                                                            role="progressbar" aria-valuenow="{{ $completion }}"
                                                            aria-valuemin="0" aria-valuemax="100"
                                                            style="width: {{ $completion }}%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold">Account Created</h6>
                                                <p class="text-sm mb-0">{{ $student->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-primary">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-user me-2"></i>Personal Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="full-name" class="form-control-label">
                                                    Full Name <span class="text-danger">*</span>
                                                </label>
                                                <input class="form-control" type="text" id="full-name" name="full_name"
                                                    value="{{ old('full_name', $student->full_name) }}"
                                                    placeholder="Enter student's full name" required>
                                                @error('full_name')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="matrix-no" class="form-control-label">
                                                    Matrix Number <span class="text-danger">*</span>
                                                </label>
                                                <input class="form-control" type="text" id="matrix-no"
                                                    name="matrix_no" value="{{ old('matrix_no', $student->matrix_no) }}"
                                                    placeholder="e.g., AB123456" style="text-transform: uppercase;"
                                                    required>
                                                @error('matrix_no')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                                <small class="text-muted">Format: Uppercase letters and numbers
                                                    only</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-info">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-address-book me-2"></i>Contact Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user-email" class="form-control-label">
                                                    Email Address <span class="text-danger">*</span>
                                                </label>
                                                <input class="form-control" type="email" id="user-email"
                                                    name="email" value="{{ old('email', $student->user->email) }}"
                                                    placeholder="student@example.com" required>
                                                @error('email')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone" class="form-control-label">
                                                    Phone Number <span class="text-danger">*</span>
                                                </label>
                                                <input class="form-control" type="tel" id="phone"
                                                    name="phone_number"
                                                    value="{{ old('phone_number', $student->user->phone_number) }}"
                                                    placeholder="e.g., +60123456789" required>
                                                @error('phone_number')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Picture Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-success">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-camera me-2"></i>Profile Picture
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Current Profile Picture -->
                                        @if ($student->user->profile_picture_url)
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label">Current Profile Picture</label>
                                                    <div class="mt-2">
                                                        <img src="{{ $student->user->profile_picture_url }}"
                                                            alt="Current Profile Picture"
                                                            class="avatar avatar-xl border-radius-lg shadow"
                                                            id="currentImage">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Upload New Picture -->
                                        <div class="col-md-{{ $student->user->profile_picture_url ? '8' : '12' }}">
                                            <div class="form-group">
                                                <label for="profile_picture" class="form-control-label">
                                                    {{ $student->user->profile_picture_url ? 'Update Profile Picture' : 'Upload Profile Picture' }}
                                                </label>
                                                <input class="form-control" type="file" id="profile_picture"
                                                    name="profile_picture"
                                                    accept="image/jpeg,image/png,image/jpg,image/gif"
                                                    onchange="previewImage(event)">
                                                @error('profile_picture')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                                <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF. Maximum
                                                    size: 2MB</small>
                                            </div>

                                            <!-- New Image Preview -->
                                            <div id="imagePreview" class="mt-3" style="display: none;">
                                                <label class="form-control-label">New Picture Preview:</label>
                                                <div class="mt-2">
                                                    <img id="preview" src="#" alt="New Profile Picture Preview"
                                                        class="avatar avatar-xl border-radius-lg shadow">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-dark">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-chart-line me-2"></i>Account Summary
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold text-muted">Total Transactions</h6>
                                                <h4 class="font-weight-bold">{{ $student->transaction_count_today ?? 0 }}
                                                </h4>
                                                <small class="text-muted">Today</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold text-muted">Eligible Status</h6>
                                                @if ($student->is_eligible)
                                                    <span class="badge badge-lg bg-gradient-success">Eligible</span>
                                                @else
                                                    <span class="badge badge-lg bg-gradient-warning">Not Eligible</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold text-muted">Last Updated</h6>
                                                <p class="text-sm mb-0">{{ $student->updated_at->format('M d, Y') }}</p>
                                                <small
                                                    class="text-muted">{{ $student->updated_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
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
                                            <a href="{{ route('student.show', $student->id) }}"
                                                class="btn btn-light me-2">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn bg-gradient-primary">
                                                <i class="fas fa-save me-1"></i>Update Student
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

    <script>
        // Preview uploaded image
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        }

        // Convert matrix number to uppercase as user types
        document.getElementById('matrix-no').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });

        // Form validation feedback
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
                submitBtn.disabled = true;
            });
        });

        // Auto-save indicator (optional enhancement)
        let changesMade = false;
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"]');

        inputs.forEach(input => {
            input.addEventListener('change', function() {
                if (!changesMade) {
                    changesMade = true;
                    // You could add an indicator here that changes have been made
                }
            });
        });

        // Warn user about unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (changesMade) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Reset changesMade flag when form is submitted
        document.querySelector('form').addEventListener('submit', function() {
            changesMade = false;
        });
    </script>

@endsection
