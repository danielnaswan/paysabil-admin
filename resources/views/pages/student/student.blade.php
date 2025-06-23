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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Students</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $students->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Eligible Students</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $students->filter(function ($student) {return $student->is_eligible;})->count() }}
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
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Pending Applications</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $students->filter(function ($student) {return $student->application_status === 'PENDING';})->count() }}
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
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Today</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $students->filter(function ($student) {return $student->transaction_count_today > 0;})->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">All Students</h5>
                                <p class="text-sm mb-0">Manage student accounts and track their eligibility status</p>
                            </div>
                            <a href="{{ route('student.create') }}" class="btn bg-gradient-primary btn-sm mb-0"
                                type="button">
                                <i class="fas fa-plus me-2"></i>New Student
                            </a>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Student
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Matrix No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Contact
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Status
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Profile
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        @if ($student->user->profile_picture_url)
                                                            <img src="{{ $student->user->profile_picture_url }}"
                                                                class="avatar avatar-sm me-3 border-radius-lg">
                                                        @else
                                                            <img src="{{ asset('assets/img/default-avatar.png') }}"
                                                                class="avatar avatar-sm me-3 border-radius-lg">
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $student->full_name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            Joined {{ $student->created_at->format('M Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $student->matrix_no }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-xs font-weight-bold mb-1">
                                                        <i class="fas fa-envelope me-1"></i>{{ $student->user->email }}
                                                    </span>
                                                    @if ($student->user->phone_number)
                                                        <span class="text-xs text-secondary">
                                                            <i
                                                                class="fas fa-phone me-1"></i>{{ $student->user->phone_number }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @if ($student->application)
                                                        @if ($student->application->status === 'APPROVED')
                                                            <span
                                                                class="badge badge-sm bg-gradient-success">Eligible</span>
                                                        @elseif($student->application->status === 'PENDING')
                                                            <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                                        @elseif($student->application->status === 'REJECTED')
                                                            <span class="badge badge-sm bg-gradient-danger">Rejected</span>
                                                        @endif
                                                        @if ($student->transaction_count_today > 0)
                                                            <small class="text-success text-xs mt-1">
                                                                <i class="fas fa-check-circle me-1"></i>Active today
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-sm bg-gradient-secondary">No
                                                            Application</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="progress-wrapper w-75 mx-auto">
                                                    <div class="progress-info">
                                                        <div class="progress-percentage">
                                                            <span
                                                                class="text-xs font-weight-bold">{{ $student->profile_completion_percentage }}%</span>
                                                        </div>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-info w-{{ $student->profile_completion_percentage }}"
                                                            role="progressbar"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex">
                                                    <a class="btn btn-link text-info px-2 mb-0"
                                                        href="{{ route('student.show', $student->id) }}"
                                                        title="View Details">
                                                        <i class="fas fa-eye text-info me-1"></i>View
                                                    </a>
                                                    <a class="btn btn-link text-dark px-2 mb-0"
                                                        href="{{ route('student.edit', $student->id) }}"
                                                        title="Edit Student">
                                                        <i class="fas fa-pencil-alt text-dark me-1"></i>Edit
                                                    </a>
                                                    <form action="{{ route('student.destroy', $student->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete {{ $student->full_name }}? This action cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger px-2 mb-0"
                                                            title="Delete Student">
                                                            <i class="far fa-trash-alt text-danger me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                                                    <h5 class="text-muted mt-3">No Students Found</h5>
                                                    <p class="text-sm text-muted">Start by adding your first student to the
                                                        system.</p>
                                                    <a href="{{ route('student.create') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fas fa-plus me-2"></i>Add Student
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
