@extends('layouts.user_type.auth')

@section('content')
    <div>
        @if (session('success'))
            <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                <span class="alert-text text-white">
                    {{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="m-3 alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-text text-white">
                    {{ $errors->first() }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Applications</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $statistics['total'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-folder-17 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Pending Review</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $statistics['pending'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="ni ni-time-alarm text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Approved</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $statistics['approved'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Overdue</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $statistics['overdue'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                    <i class="ni ni-sound-wave text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">All Applications</h5>
                                <p class="text-sm mb-0">Manage student applications and review status</p>
                            </div>
                            <div class="d-flex">
                                <form method="GET" action="{{ route('application.index') }}" class="d-flex me-3">
                                    <select name="status" class="form-select form-select-sm me-2"
                                        onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>
                                            Rejected</option>
                                    </select>
                                    <input type="text" name="search" placeholder="Search student..."
                                        value="{{ request('search') }}" class="form-control form-control-sm me-2">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
                                </form>
                                <a href="{{ route('application.create') }}" class="btn bg-gradient-primary btn-sm mb-0"
                                    type="button">
                                    <i class="fas fa-plus me-2"></i>New Application
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        @if ($applications->count() > 0)
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Student
                                            </th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Title
                                            </th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Status
                                            </th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Submission Date
                                            </th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Priority
                                            </th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Document
                                            </th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($applications as $application)
                                            <tr class="{{ $application->is_overdue ? 'table-warning' : '' }}">
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            @if ($application->student->user->profile_picture_url)
                                                                <img src="{{ $application->student->user->profile_picture_url }}"
                                                                    class="avatar avatar-sm me-3 border-radius-lg">
                                                            @else
                                                                <img src="{{ asset('assets/img/default-avatar.png') }}"
                                                                    class="avatar avatar-sm me-3 border-radius-lg"
                                                                    alt="Default Avatar">
                                                            @endif
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">
                                                                {{ $application->student->full_name }}</h6>
                                                            <p class="text-xs text-secondary mb-0">
                                                                <i class="fas fa-id-card me-1"></i>
                                                                {{ $application->student->matrix_no }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ Str::limit($application->title, 40) }}</p>
                                                    @if ($application->description)
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ Str::limit($application->description, 60) }}</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($application->status === 'APPROVED')
                                                        <span class="badge badge-sm bg-gradient-success">Approved</span>
                                                    @elseif($application->status === 'PENDING')
                                                        <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                                    @elseif($application->status === 'REJECTED')
                                                        <span class="badge badge-sm bg-gradient-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-xs font-weight-bold">
                                                            {{ $application->submission_date->format('d M Y') }}
                                                        </span>
                                                        <span class="text-xs text-secondary">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $application->submission_date->format('H:i') }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($application->is_overdue)
                                                        <span class="badge badge-sm bg-gradient-danger">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                                                        </span>
                                                        <small class="text-xs text-danger d-block mt-1">
                                                            {{ $application->days_pending }} days
                                                        </small>
                                                    @else
                                                        <span class="text-xs text-secondary">
                                                            {{ $application->days_pending }} days old
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($application->document_url)
                                                        <a href="{{ route('application.downloadDocument', $application) }}"
                                                            class="text-xs font-weight-bold mb-0 text-primary"
                                                            target="_blank">
                                                            <i class="fas fa-file-pdf text-danger me-1"></i>Download
                                                        </a>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $application->document_size_human }}</p>
                                                    @else
                                                        <span class="text-xs text-secondary">No document</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex">
                                                        <a class="btn btn-link text-info px-2 mb-0"
                                                            href="{{ route('application.show', $application->id) }}"
                                                            title="View Details">
                                                            <i class="fas fa-eye text-info me-1"></i>View
                                                        </a>
                                                        @if ($application->canBeReviewed())
                                                            <a class="btn btn-link text-dark px-2 mb-0"
                                                                href="{{ route('application.edit', $application->id) }}"
                                                                title="Review Application">
                                                                <i class="fas fa-pencil-alt text-dark me-1"></i>Review
                                                            </a>
                                                        @endif
                                                        @if ($application->status !== 'APPROVED')
                                                            <form
                                                                action="{{ route('application.destroy', $application->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Are you sure you want to delete this application?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-link text-danger px-2 mb-0"
                                                                    title="Delete Application">
                                                                    <i class="far fa-trash-alt text-danger me-1"></i>Delete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $applications->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-folder-open text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3">No Applications Found</h5>
                                    <p class="text-sm text-muted">
                                        @if (request()->hasAny(['status', 'search']))
                                            No applications match your current filters.
                                            <a href="{{ route('application.index') }}" class="text-primary">Clear
                                                filters</a>
                                        @else
                                            Start by creating your first application.
                                        @endif
                                    </p>
                                    <a href="{{ route('application.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-2"></i>Add Application
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions (for pending applications) -->
        @if ($applications->where('status', 'PENDING')->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card mb-4 mx-4">
                        <div class="card-header pb-0">
                            <div class="d-flex flex-row justify-content-between">
                                <div>
                                    <h5 class="mb-0">Bulk Actions</h5>
                                    <p class="text-sm mb-0">Approve multiple pending applications at once</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('application.bulkApprove') }}" method="POST" id="bulkApproveForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="bulk_remarks" class="text-sm">Admin Remarks (Optional)</label>
                                            <textarea class="form-control" name="admin_remarks" rows="2" placeholder="Add remarks for bulk approval..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" class="btn bg-gradient-success btn-sm"
                                            onclick="selectPendingApplications()">
                                            <i class="fas fa-check me-2"></i>Bulk Approve Pending
                                        </button>
                                    </div>
                                </div>
                                <div id="selectedApplications"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function selectPendingApplications() {
            const pendingApplications = @json($applications->where('status', 'PENDING')->pluck('id'));

            if (pendingApplications.length === 0) {
                alert('No pending applications to approve.');
                return;
            }

            if (confirm(`Are you sure you want to approve ${pendingApplications.length} pending applications?`)) {
                // Add hidden inputs for application IDs
                const selectedDiv = document.getElementById('selectedApplications');
                selectedDiv.innerHTML = '';

                pendingApplications.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'application_ids[]';
                    input.value = id;
                    selectedDiv.appendChild(input);
                });

                document.getElementById('bulkApproveForm').submit();
            }
        }
    </script>
@endsection
