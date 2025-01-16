@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Register New Vendor</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('vendor.store') }}" method="POST" enctype="multipart/form-data" role="form text-left">
                @csrf
                @if($errors->any())
                    <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
                        <span class="alert-text text-white">{{$errors->first()}}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="business-name" class="form-control-label">Business Name</label>
                            <input class="form-control" type="text" id="business-name" name="business_name" value="{{ old('business_name') }}" required>
                            @error('business_name')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="service-category" class="form-control-label">Service Category</label>
                            <input class="form-control" type="text" id="service-category" name="service_category" value="{{ old('service_category') }}" required>
                            @error('service_category')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="experience-years" class="form-control-label">Years of Experience</label>
                            <input class="form-control" type="number" id="experience-years" name="experience_years" value="{{ old('experience_years') }}" required min="0">
                            @error('experience_years')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user-email" class="form-control-label">Email</label>
                            <input class="form-control" type="email" id="user-email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-control-label">Phone Number</label>
                            <input class="form-control" type="tel" id="phone" name="phone_number" value="{{ old('phone_number') }}" required>
                            @error('phone_number')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="profile_picture" class="form-control-label">Profile Picture</label>
                            <input class="form-control" type="file" id="profile_picture" name="profile_picture" accept="image/*">
                            @error('profile_picture')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-control-label">Password</label>
                            <input class="form-control" type="password" id="password" name="password" required>
                            @error('password')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation" class="form-control-label">Confirm Password</label>
                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">Register Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
