@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Edit Student</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('student.update', $student->id) }}" method="POST" enctype="multipart/form-data" role="form text-left">
                @csrf
                @method('PUT')
                
                @if($errors->any())
                    <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
                        <span class="alert-text text-white">
                        {{$errors->first()}}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif
                @if(session('success'))
                    <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
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
                            <input class="form-control" type="text" id="full-name" name="full_name" value="{{ old('full_name', $student->full_name) }}" required>
                            @error('full_name')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="matrix-no" class="form-control-label">Matrix Number</label>
                            <input class="form-control" type="text" id="matrix-no" name="matrix_no" value="{{ old('matrix_no', $student->matrix_no) }}" required>
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
                            <input class="form-control" type="email" id="user-email" name="email" value="{{ old('email', $student->user->email) }}" required>
                            @error('email')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-control-label">Phone Number</label>
                            <input class="form-control" type="tel" id="phone" name="phone_number" value="{{ old('phone_number', $student->user->phone_number) }}" required>
                            @error('phone_number')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
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
                @if($student->profile_picture_url)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Current Profile Picture</label>
                            <div class="mt-2">
                                <img src="{{ $student->profile_picture_url }}" alt="Current Profile Picture" class="avatar avatar-xl">
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('student.index') }}" class="btn btn-light m-0 me-2">Cancel</a>
                    <button type="submit" class="btn bg-gradient-primary m-0">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection