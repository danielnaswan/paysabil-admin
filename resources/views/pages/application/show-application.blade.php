@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Application Details</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-4">
                        <h5 class="mb-3">{{ $application->title }}</h5>
                        <div class="mb-3">
                            <span class="badge badge-sm bg-{{ $application->status === 'APPROVED' ? 'success' : ($application->status === 'REJECTED' ? 'danger' : 'warning') }}">
                                {{ $application->status }}
                            </span>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Student Information</h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $application->student->full_name }}</p>
                                <p class="mb-1"><strong>Matrix No:</strong> {{ $application->student->matrix_no }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $application->student->user->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Application Information</h6>
                                <p class="mb-1"><strong>Submission Date:</strong> {{ $application->submission_date->format('d/m/Y H:i') }}</p>
                                @if($application->reviewed_at)
                                <p class="mb-1"><strong>Review Date:</strong> {{ $application->reviewed_at->format('d/m/Y H:i') }}</p>
                                <p class="mb-1"><strong>Reviewed By:</strong> {{ $application->reviewer ? $application->reviewer->name : 'N/A' }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6>Description</h6>
                            <p class="mb-0">{{ $application->description ?: 'No description provided.' }}</p>
                        </div>

                        @if($application->admin_remarks)
                        <div class="mb-4">
                            <h6>Admin Remarks</h6>
                            <p class="mb-0">{{ $application->admin_remarks }}</p>
                        </div>
                        @endif

                        <div class="mb-4">
                            <h6>Supporting Document</h6>
                            <div class="d-flex align-items-center">
                                <a href="{{ $application->document_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-file-pdf me-2"></i>View PDF Document
                                </a>
                                <span class="text-sm text-muted ms-2">{{ $application->document_name }} ({{ $application->getDocumentSizeForHumans() }})</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('application.index') }}" class="btn btn-secondary me-2">Back to List</a>
                        <a href="{{ route('application.edit', $application->id) }}" class="btn btn-primary">Edit Application</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection