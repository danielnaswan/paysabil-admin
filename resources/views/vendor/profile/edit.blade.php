@extends('layouts.user_type.vendor')

@section('page-title', 'Edit Profile')
@section('page-heading', 'Edit Profile')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <form method="POST" action="{{ route('vendor.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <p class="mb-0">Edit Profile</p>
                                <button type="submit" class="btn btn-primary btn-sm ms-auto">Save</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-uppercase text-sm">User Information</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Full Name</label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text"
                                            name="name" id="name" value="{{ old('name', $vendor->user->name) }}"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-control-label">Email address</label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email"
                                            name="email" id="email" value="{{ old('email', $vendor->user->email) }}"
                                            required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone_number" class="form-control-label">Phone Number</label>
                                        <input class="form-control @error('phone_number') is-invalid @enderror"
                                            type="text" name="phone_number" id="phone_number"
                                            value="{{ old('phone_number', $vendor->user->phone_number) }}" required>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location" class="form-control-label">Location</label>
                                        <input class="form-control @error('location') is-invalid @enderror" type="text"
                                            name="location" id="location"
                                            value="{{ old('location', $vendor->user->location) }}">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="horizontal dark">

                            <p class="text-uppercase text-sm">Business Information</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="business_name" class="form-control-label">Business Name</label>
                                        <input class="form-control @error('business_name') is-invalid @enderror"
                                            type="text" name="business_name" id="business_name"
                                            value="{{ old('business_name', $vendor->business_name) }}" required>
                                        @error('business_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service_category" class="form-control-label">Service Category</label>
                                        <select class="form-control @error('service_category') is-invalid @enderror"
                                            name="service_category" id="service_category" required>
                                            <option value="">Select Category</option>
                                            @foreach (['Food & Beverage', 'Restaurant', 'Cafe', 'Fast Food', 'Catering', 'Other'] as $category)
                                                <option value="{{ $category }}"
                                                    {{ old('service_category', $vendor->service_category) == $category ? 'selected' : '' }}>
                                                    {{ $category }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('service_category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="experience_years" class="form-control-label">Years of Experience</label>
                                        <input class="form-control @error('experience_years') is-invalid @enderror"
                                            type="number" name="experience_years" id="experience_years" min="0"
                                            max="50" value="{{ old('experience_years', $vendor->experience_years) }}"
                                            required>
                                        @error('experience_years')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="about_me" class="form-control-label">About Business</label>
                                        <textarea class="form-control @error('about_me') is-invalid @enderror" name="about_me" id="about_me" rows="4"
                                            placeholder="Tell customers about your business...">{{ old('about_me', $vendor->user->about_me) }}</textarea>
                                        @error('about_me')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="horizontal dark">

                            <p class="text-uppercase text-sm">Profile Picture</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="profile_picture" class="form-control-label">Profile Picture</label>
                                        <input class="form-control @error('profile_picture') is-invalid @enderror"
                                            type="file" name="profile_picture" id="profile_picture" accept="image/*">
                                        @error('profile_picture')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Max file size: 2MB. Supported formats: JPEG, PNG, JPG,
                                            GIF</small>
                                    </div>
                                    @if ($vendor->user->profile_picture_url)
                                        <div class="mt-2">
                                            <img src="{{ $vendor->user->profile_picture_url }}"
                                                alt="Current profile picture" class="img-thumbnail"
                                                style="max-width: 150px;">
                                            <p class="text-sm text-muted mt-1">Current profile picture</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <hr class="horizontal dark">

                            <p class="text-uppercase text-sm">Change Password</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="form-control-label">New Password</label>
                                        <input class="form-control @error('password') is-invalid @enderror"
                                            type="password" name="password" id="password"
                                            placeholder="Leave blank to keep current password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-control-label">Confirm
                                            Password</label>
                                        <input class="form-control" type="password" name="password_confirmation"
                                            id="password_confirmation" placeholder="Confirm new password">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-profile">
                    <img src="{{ asset('assets/img/bg-profile.jpg') }}" alt="Image placeholder" class="card-img-top">
                    <div class="row justify-content-center">
                        <div class="col-4 col-lg-4 order-lg-2">
                            <div class="mt-n4 mt-lg-n6 mb-4 mb-lg-0">
                                @if ($vendor->user->profile_picture_url)
                                    <img src="{{ $vendor->user->profile_picture_url }}"
                                        class="rounded-circle img-fluid border border-2 border-white"
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('assets/img/default-avatar.png') }}"
                                        class="rounded-circle img-fluid border border-2 border-white"
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-header text-center border-0 pt-0 pt-lg-2 pb-4 pb-lg-3">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('vendor.profile') }}"
                                class="btn btn-sm btn-info mb-0 d-none d-lg-block">Back to Profile</a>
                            <a href="{{ route('vendor.profile') }}" class="btn btn-sm btn-info mb-0 d-block d-lg-none"><i
                                    class="ni ni-collection"></i></a>
                            <a href="{{ route('vendor.dashboard') }}"
                                class="btn btn-sm btn-dark float-right mb-0 d-none d-lg-block">Dashboard</a>
                            <a href="{{ route('vendor.dashboard') }}"
                                class="btn btn-sm btn-dark float-right mb-0 d-block d-lg-none"><i
                                    class="ni ni-pin-3"></i></a>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="text-center mt-4">
                            <h5>{{ $vendor->business_name }}</h5>
                            <div class="h6 font-weight-300">
                                <i class="ni location_pin mr-2"></i>{{ $vendor->service_category }}
                            </div>
                            <div class="h6 mt-4">
                                <i class="ni business_briefcase-24 mr-2"></i>{{ $vendor->experience_years }} years
                                experience
                            </div>
                            <div>
                                <i class="ni education_hat mr-2"></i>{{ $vendor->total_reviews }} reviews -
                                {{ $vendor->average_rating }} ⭐
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
