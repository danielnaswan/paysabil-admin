@extends('layouts.user_type.vendor')

@section('page-title', 'Settings')
@section('page-heading', 'Account Settings')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            {{-- Security Settings --}}
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">Security Settings</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('vendor.settings.update') }}">
                            @csrf
                            @method('PUT')

                            {{-- Change Password Section --}}
                            <h6 class="heading-small text-muted mb-4">Password Information</h6>
                            <div class="pl-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="current_password">Current
                                                Password</label>
                                            <input type="password" id="current_password" name="current_password"
                                                class="form-control form-control-alternative @error('current_password') is-invalid @enderror"
                                                placeholder="Enter current password">
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="new_password">New Password</label>
                                            <input type="password" id="new_password" name="new_password"
                                                class="form-control form-control-alternative @error('new_password') is-invalid @enderror"
                                                placeholder="Enter new password">
                                            @error('new_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="new_password_confirmation">Confirm New
                                                Password</label>
                                            <input type="password" id="new_password_confirmation"
                                                name="new_password_confirmation"
                                                class="form-control form-control-alternative"
                                                placeholder="Confirm new password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Notification Settings --}}
                            <h6 class="heading-small text-muted mb-4">Notification Preferences</h6>
                            <div class="pl-lg-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications"
                                        name="email_notifications" value="1" checked>
                                    <label class="form-check-label" for="email_notifications">
                                        <span class="text-sm">Email Notifications</span>
                                        <br>
                                        <small class="text-muted">Receive notifications about new orders and reviews via
                                            email</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications"
                                        name="sms_notifications" value="1">
                                    <label class="form-check-label" for="sms_notifications">
                                        <span class="text-sm">SMS Notifications</span>
                                        <br>
                                        <small class="text-muted">Receive important updates via SMS</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" id="marketing_emails"
                                        name="marketing_emails" value="1">
                                    <label class="form-check-label" for="marketing_emails">
                                        <span class="text-sm">Marketing Emails</span>
                                        <br>
                                        <small class="text-muted">Receive tips and promotional content</small>
                                    </label>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Save Button --}}
                            <div class="pl-lg-4">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Account Information --}}
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">Account Information</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-0">Business Name</h6>
                                    <small class="text-muted">{{ $vendor->business_name }}</small>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-0">Email</h6>
                                    <small class="text-muted">{{ $vendor->user->email }}</small>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-0">Phone</h6>
                                    <small class="text-muted">{{ $vendor->user->phone_number }}</small>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-0">Service Category</h6>
                                    <small class="text-muted">{{ $vendor->service_category }}</small>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-0">Experience</h6>
                                    <small class="text-muted">{{ $vendor->experience_years }} years</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="{{ route('vendor.profile.edit') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Danger Zone --}}
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 text-danger">Danger Zone</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <small>
                                <strong>Warning:</strong> These actions cannot be undone. Please be careful.
                            </small>
                        </div>

                        <button type="button" class="btn btn-outline-danger btn-sm w-100" disabled>
                            <i class="fas fa-exclamation-triangle me-1"></i>Deactivate Account
                        </button>
                        <small class="text-muted d-block mt-2">Contact support to deactivate your account</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
