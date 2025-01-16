@extends('layouts.user_type.auth')

@section('content')
<div>
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
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">All Applications</h5>
                        </div>
                        <a href="{{ route('application.create') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+ New Application</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">TITLE</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">STUDENT</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">STATUS</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">SUBMISSION DATE</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">DOCUMENT</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $application)
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $application->title }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $application->student->full_name }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $application->student->matrix_no }}</p>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-{{ $application->status === 'APPROVED' ? 'success' : ($application->status === 'REJECTED' ? 'danger' : 'warning') }}">
                                            {{ $application->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $application->submission_date->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td>
                                        <a href="{{ $application->document_url }}" target="_blank" class="text-xs font-weight-bold mb-0">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>View PDF
                                        </a>
                                        <p class="text-xs text-secondary mb-0">{{ $application->getDocumentSizeForHumans() }}</p>
                                    </td>
                                    <td class="align-middle">
                                        <div class="ms-auto">
                                            <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('application.show', $application->id) }}">
                                                <i class="fas fa-eye text-dark me-2"></i>View
                                            </a>
                                            <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('application.edit', $application->id) }}">
                                                <i class="fas fa-pencil-alt text-dark me-2"></i>Edit
                                            </a>
                                            <form action="{{ route('application.destroy', $application->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this application?')">
                                                    <i class="far fa-trash-alt me-2"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection