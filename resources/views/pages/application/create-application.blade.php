@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Create New Application</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('application.store') }}" method="POST" enctype="multipart/form-data" role="form text-left">
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="student_id" class="form-control-label">Select Student</label>
                            <select class="form-control" id="student_id" name="student_id" required>
                                <option value="">Choose a student...</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->full_name }} ({{ $student->matrix_no }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title" class="form-control-label">Application Title</label>
                            <input class="form-control" type="text" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description" class="form-control-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="document" class="form-control-label">Supporting Document (PDF)</label>
                            <input class="form-control" type="file" id="document" name="document" accept="application/pdf" required>
                            <small class="text-muted">Maximum file size: 10MB</small>
                            @error('document')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('application.index') }}" class="btn btn-light m-0 me-2">Cancel</a>
                    <button type="submit" class="btn bg-gradient-primary m-0">Create Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection