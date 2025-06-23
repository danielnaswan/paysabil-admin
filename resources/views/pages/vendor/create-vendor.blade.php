@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Register New Vendor</h6>
                            <a href="{{ route('vendor.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                        <p class="text-sm mb-0 mt-2">Add a new vendor to the system with business information and
                            credentials</p>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <form action="{{ route('vendor.store') }}" method="POST" enctype="multipart/form-data"
                            role="form text-left">
                            @csrf

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

                            <!-- Business Information Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-primary">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-building me-2"></i>Business Information
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
                                                    name="business_name" value="{{ old('business_name') }}"
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
                                                        {{ old('service_category') == 'Food & Beverage' ? 'selected' : '' }}>
                                                        Food & Beverage</option>
                                                    <option value="Restaurant"
                                                        {{ old('service_category') == 'Restaurant' ? 'selected' : '' }}>
                                                        Restaurant</option>
                                                    <option value="Cafe"
                                                        {{ old('service_category') == 'Cafe' ? 'selected' : '' }}>Cafe
                                                    </option>
                                                    <option value="Fast Food"
                                                        {{ old('service_category') == 'Fast Food' ? 'selected' : '' }}>Fast
                                                        Food</option>
                                                    <option value="Catering"
                                                        {{ old('service_category') == 'Catering' ? 'selected' : '' }}>
                                                        Catering</option>
                                                    <option value="Other"
                                                        {{ old('service_category') == 'Other' ? 'selected' : '' }}>Other
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
                                                    name="experience_years" value="{{ old('experience_years') }}"
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
                                                <input class="form-control" type="email" id="user-email" name="email"
                                                    value="{{ old('email') }}" placeholder="vendor@example.com"
                                                    required>
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
                                                    name="phone_number" value="{{ old('phone_number') }}"
                                                    placeholder="e.g., +60123456789" required>
                                                @error('phone_number')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Security Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-warning">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-lock me-2"></i>Account Security
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password" class="form-control-label">
                                                    Password <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input class="form-control" type="password" id="password"
                                                        name="password" placeholder="Enter secure password" required>
                                                    <span class="input-group-text cursor-pointer"
                                                        onclick="togglePassword('password')">
                                                        <i class="fas fa-eye" id="passwordIcon"></i>
                                                    </span>
                                                </div>
                                                @error('password')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                                <small class="text-muted">Minimum 5 characters required</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password_confirmation" class="form-control-label">
                                                    Confirm Password <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input class="form-control" type="password"
                                                        id="password_confirmation" name="password_confirmation"
                                                        placeholder="Confirm password" required>
                                                    <span class="input-group-text cursor-pointer"
                                                        onclick="togglePassword('password_confirmation')">
                                                        <i class="fas fa-eye" id="passwordConfirmationIcon"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Picture Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-success">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-camera me-2"></i>Profile Picture (Optional)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="profile_picture" class="form-control-label">Profile
                                                    Picture</label>
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

                                            <!-- Image Preview -->
                                            <div id="imagePreview" class="mt-3" style="display: none;">
                                                <label class="form-control-label">Preview:</label>
                                                <div class="mt-2">
                                                    <img id="preview" src="#" alt="Profile Picture Preview"
                                                        class="avatar avatar-xl border-radius-lg shadow">
                                                </div>
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
                                            <a href="{{ route('vendor.index') }}" class="btn btn-light me-2">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn bg-gradient-primary">
                                                <i class="fas fa-save me-1"></i>Register Vendor
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
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + 'Icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating...';
                submitBtn.disabled = true;
            });

            // Password confirmation validation
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');

            function validatePassword() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Passwords don't match");
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }

            password.addEventListener('change', validatePassword);
            confirmPassword.addEventListener('keyup', validatePassword);
        });
    </script>
@endsection
