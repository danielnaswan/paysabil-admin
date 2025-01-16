@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Register New Student</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('student.store') }}" method="POST" enctype="multipart/form-data" role="form text-left">
                @csrf
                @if($errors->any())
                    <div class="mt-3  alert alert-primary alert-dismissible fade show" role="alert">
                        <span class="alert-text text-white">
                        {{$errors->first()}}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif
                @if(session('success'))
                    <div class="m-3  alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                        <span class="alert-text text-white">
                        {{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="full-name" class="form-control-label">Full Name</label>
                            <input class="form-control" type="text" id="full-name" name="full_name" value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="matrix-no" class="form-control-label">Matrix Number</label>
                            <input class="form-control" type="text" id="matrix-no" name="matrix_no" value="{{ old('matrix_no') }}" required>
                            @error('matrix_no')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user-email" class="form-control-label">Email</label>
                            <input class="form-control" type="email" id="user-email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-control-label">Phone Number</label>
                            <input class="form-control" type="tel" id="phone" name="phone_number" value="{{ old('phone_number') }}" required>
                            @error('phone_number')
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="profile_picture" class="form-control-label">Profile Picture</label>
                            <input class="form-control" type="file" id="profile_picture" name="profile_picture" accept="image/*">
                            @error('profile_picture')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">Register Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection