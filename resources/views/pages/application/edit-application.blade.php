@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Review Application</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('application.update', $application->id) }}" method="POST" enctype="multipart/form-data" role="form text-left">
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

                <!-- Student Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Student Name</label>
                            <input class="form-control" type="text" value="{{ $application->student->full_name }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Matrix Number</label>
                            <input class="form-control" type="text" value="{{ $application->student->matrix_no }}" disabled>
                        </div>
                    </div>
                </div>

                <!-- Application Details -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title" class="form-control-label">Application Title</label>
                            <input class="form-control" type="text" id="title" name="title" value="{{ old('title', $application->title) }}" required>
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
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $application->description) }}</textarea>
                            @error('description')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Admin Remarks -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="admin_remarks" class="form-control-label">Admin Remarks</label>
                            <textarea class="form-control" id="admin_remarks" name="admin_remarks" rows="4" required>{{ old('admin_remarks', $application->admin_remarks) }}</textarea>
                            @error('admin_remarks')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Document Information -->
                @if($application->document_url)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Current Document</label>
                            <div class="mt-2">
                                <a href="{{ $application->document_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-file-pdf me-2"></i>View Current PDF
                                </a>
                                <span class="text-sm text-muted ms-2">{{ $application->document_name }} ({{ $application->getDocumentSizeForHumans() }})</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-control-label">Application Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="PENDING" {{ $application->status === 'PENDING' ? 'selected' : '' }}>Pending</option>
                                <option value="APPROVED" {{ $application->status === 'APPROVED' ? 'selected' : '' }}>Approved</option>
                                <option value="REJECTED" {{ $application->status === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="document" class="form-control-label">Update Document (Optional)</label>
                            <input class="form-control" type="file" id="document" name="document" accept="application/pdf">
                            <small class="text-muted">Maximum file size: 10MB</small>
                            @error('document')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('application.index') }}" class="btn btn-light m-0 me-2">Cancel</a>
                    <button type="submit" class="btn bg-gradient-primary m-0">Update Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
