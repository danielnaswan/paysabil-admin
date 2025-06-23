@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Edit Vendor: {{ $vendor->business_name }}</h6>
                            <div class="ms-auto">
                                <a href="{{ route('vendor.show', $vendor->id) }}" class="btn btn-outline-info btn-sm me-2">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <a href="{{ route('vendor.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i>Back to List
                                </a>
                            </div>
                        </div>
                        <p class="text-sm mb-0 mt-2">Update vendor information and account details</p>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <form action="{{ route('vendor.update', $vendor->id) }}" method="POST"
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

                            <!-- Vendor Status Overview -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-secondary">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Vendor Status
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold">Account Status</h6>
                                                @if ($vendor->user)
                                                    <span class="badge badge-lg bg-gradient-success">
                                                        <i class="fas fa-check me-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="badge badge-lg bg-gradient-danger">
                                                        <i class="fas fa-times me-1"></i>No Account
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold">Average Rating</h6>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span
                                                        class="font-weight-bold me-2">{{ number_format($vendor->average_rating, 1) }}</span>
                                                    <i class="fas fa-star text-warning"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold">Member Since</h6>
                                                <p class="text-sm mb-0">{{ $vendor->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Business Information Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-primary">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-store me-2"></i>Business Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="business-name" class="form-control-label">
                                                    Business Name <span class="text-danger">*</span>
                                                </label>
                                                <input class="form-control" type="text" id="business-name"
                                                    name="business_name"
                                                    value="{{ old('business_name', $vendor->business_name) }}"
                                                    placeholder="Enter business name" required>
                                                @error('business_name')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="service-category" class="form-control-label">
                                                    Service Category <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" id="service-category" name="service_category"
                                                    required>
                                                    <option value="">Select Category</option>
                                                    <option value="Food & Beverage"
                                                        {{ old('service_category', $vendor->service_category) == 'Food & Beverage' ? 'selected' : '' }}>
                                                        Food & Beverage
                                                    </option>
                                                    <option value="Restaurant"
                                                        {{ old('service_category', $vendor->service_category) == 'Restaurant' ? 'selected' : '' }}>
                                                        Restaurant
                                                    </option>
                                                    <option value="Cafe"
                                                        {{ old('service_category', $vendor->service_category) == 'Cafe' ? 'selected' : '' }}>
                                                        Cafe
                                                    </option>
                                                    <option value="Fast Food"
                                                        {{ old('service_category', $vendor->service_category) == 'Fast Food' ? 'selected' : '' }}>
                                                        Fast Food
                                                    </option>
                                                    <option value="Catering"
                                                        {{ old('service_category', $vendor->service_category) == 'Catering' ? 'selected' : '' }}>
                                                        Catering
                                                    </option>
                                                    <option value="Other"
                                                        {{ old('service_category', $vendor->service_category) == 'Other' ? 'selected' : '' }}>
                                                        Other
                                                    </option>
                                                </select>
                                                @error('service_category')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="experience-years" class="form-control-label">
                                                    Years of Experience <span class="text-danger">*</span>
                                                </label>
                                                <input class="form-control" type="number" id="experience-years"
                                                    name="experience_years"
                                                    value="{{ old('experience_years', $vendor->experience_years) }}"
                                                    placeholder="0" min="0" max="50" required>
                                                @error('experience_years')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
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
                                                    name="email" value="{{ old('email', $vendor->user->email ?? '') }}"
                                                    placeholder="vendor@example.com" required>
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
                                                    value="{{ old('phone_number', $vendor->user->phone_number ?? '') }}"
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
                                        @if ($vendor->user && $vendor->user->profile_picture_url)
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label">Current Profile Picture</label>
                                                    <div class="mt-2">
                                                        <img src="{{ $vendor->user->profile_picture_url }}"
                                                            alt="Current Profile Picture"
                                                            class="avatar avatar-xl border-radius-lg shadow"
                                                            id="currentImage">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Upload New Picture -->
                                        <div
                                            class="col-md-{{ $vendor->user && $vendor->user->profile_picture_url ? '8' : '12' }}">
                                            <div class="form-group">
                                                <label for="profile_picture" class="form-control-label">
                                                    {{ $vendor->user && $vendor->user->profile_picture_url ? 'Update Profile Picture' : 'Upload Profile Picture' }}
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

                            <!-- Vendor Statistics Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-dark">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-chart-line me-2"></i>Business Statistics
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold text-muted">Total Reviews</h6>
                                                <h4 class="font-weight-bold">{{ $vendor->total_reviews }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold text-muted">Services Count</h6>
                                                <h4 class="font-weight-bold">{{ $vendor->services->count() }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold text-muted">Average Rating</h6>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <h4 class="font-weight-bold me-1">
                                                        {{ number_format($vendor->average_rating, 1) }}</h4>
                                                    <i class="fas fa-star text-warning"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-sm font-weight-bold text-muted">Last Updated</h6>
                                                <p class="text-sm mb-0">{{ $vendor->updated_at->format('M d, Y') }}</p>
                                                <small
                                                    class="text-muted">{{ $vendor->updated_at->diffForHumans() }}</small>
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
                                            <a href="{{ route('vendor.show', $vendor->id) }}" class="btn btn-light me-2">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn bg-gradient-primary">
                                                <i class="fas fa-save me-1"></i>Update Vendor
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

        <!-- Quick Actions Section -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('services.create', ['vendor' => $vendor->id]) }}"
                                    class="btn btn-outline-primary w-100">
                                    <i class="fas fa-plus mb-2 d-block"></i>
                                    Add Service
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('qrcode.create') }}?vendor_id={{ $vendor->id }}"
                                    class="btn btn-outline-info w-100">
                                    <i class="fas fa-qrcode mb-2 d-block"></i>
                                    Generate QR
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('report.financial', $vendor->id) }}"
                                    class="btn btn-outline-success w-100">
                                    <i class="fas fa-chart-line mb-2 d-block"></i>
                                    View Reports
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('report.feedback', $vendor->id) }}"
                                    class="btn btn-outline-warning w-100">
                                    <i class="fas fa-comments mb-2 d-block"></i>
                                    View Feedback
                                </a>
                            </div>
                        </div>
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

        // Form validation feedback
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
                submitBtn.disabled = true;
            });

            // Email validation
            const emailInput = document.getElementById('user-email');
            emailInput.addEventListener('blur', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    emailInput.classList.add('is-invalid');
                } else {
                    emailInput.classList.remove('is-invalid');
                }
            });

            // Phone validation (basic)
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('blur', function() {
                const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                if (!phoneRegex.test(phoneInput.value.trim())) {
                    phoneInput.classList.add('is-invalid');
                } else {
                    phoneInput.classList.remove('is-invalid');
                }
            });
        });

        // Auto-save indicator (optional enhancement)
        let changesMade = false;
        const inputs = document.querySelectorAll(
            'input[type="text"], input[type="email"], input[type="tel"], input[type="number"], select');

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
