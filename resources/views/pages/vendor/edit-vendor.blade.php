@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Edit Vendor</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('vendor.update', $vendor->id) }}" method="POST" enctype="multipart/form-data" role="form text-left">
                @csrf
                @method('PUT')

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
                            <input class="form-control" type="text" id="business-name" name="business_name" value="{{ old('business_name', $vendor->business_name) }}" required>
                            @error('business_name')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="service-category" class="form-control-label">Service Category</label>
                            <input class="form-control" type="text" id="service-category" name="service_category" value="{{ old('service_category', $vendor->service_category) }}" required>
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
                            <input class="form-control" type="number" id="experience-years" name="experience_years" value="{{ old('experience_years', $vendor->experience_years) }}" required min="0">
                            @error('experience_years')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user-email" class="form-control-label">Email</label>
                            <input class="form-control" type="email" id="user-email" name="email" value="{{ old('email', $vendor->user->email) }}" required>
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
                            <input class="form-control" type="tel" id="phone" name="phone_number" value="{{ old('phone_number', $vendor->user->phone_number) }}" required>
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

                @if($vendor->profile_picture_url)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Current Profile Picture</label>
                            <div class="mt-2">
                                <img src="{{ $vendor->profile_picture_url }}" alt="Current Profile Picture" class="avatar avatar-xl">
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('vendor.index') }}" class="btn btn-light m-0 me-2">Cancel</a>
                    <button type="submit" class="btn bg-gradient-primary m-0">Update Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection