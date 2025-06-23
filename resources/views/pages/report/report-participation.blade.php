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
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </span>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Eligible</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $statistics['total_eligible'] }}
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Students</p>
                                    <h5 class="font-weight-bolder mb-0 text-success">
                                        {{ $statistics['active_students'] }}
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Inactive Students</p>
                                    <h5 class="font-weight-bolder mb-0 text-warning">
                                        {{ $statistics['inactive_students'] }}
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Participation Rate</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $statistics['participation_rate'] }}%
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

        <!-- Filter and Export Section -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-start">
                            <div class="mb-3 mb-md-0">
                                <h5 class="mb-0">Student Participation Report</h5>
                                <p class="text-sm mb-0">
                                    Students with no transactions in the last {{ $daysThreshold }} days
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-end">
                                <!-- Filter Form -->
                                <form method="GET" action="{{ route('report.participation') }}"
                                    class="d-flex align-items-end gap-2">
                                    <div>
                                        <label for="days_threshold" class="form-label text-xs mb-1">Inactive Period</label>
                                        <select name="days_threshold" id="days_threshold" class="form-select form-select-sm"
                                            style="min-width: 120px;">
                                            <option value="3" {{ $daysThreshold == 3 ? 'selected' : '' }}>3 days
                                            </option>
                                            <option value="5" {{ $daysThreshold == 5 ? 'selected' : '' }}>5 days
                                            </option>
                                            <option value="7" {{ $daysThreshold == 7 ? 'selected' : '' }}>7 days
                                            </option>
                                            <option value="14" {{ $daysThreshold == 14 ? 'selected' : '' }}>14 days
                                            </option>
                                            <option value="30" {{ $daysThreshold == 30 ? 'selected' : '' }}>30 days
                                            </option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-filter"></i> Apply Filter
                                    </button>
                                </form>

                                <!-- Export Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-success dropdown-toggle" type="button"
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.participation', ['days_threshold' => $daysThreshold, 'export' => 'pdf']) }}">
                                                <i class="fas fa-file-pdf text-danger me-2"></i> Export as PDF</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.participation', ['days_threshold' => $daysThreshold, 'export' => 'excel']) }}">
                                                <i class="fas fa-file-excel text-success me-2"></i> Export as Excel</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.participation', ['days_threshold' => $daysThreshold, 'export' => 'csv']) }}">
                                                <i class="fas fa-file-csv text-info me-2"></i> Export as CSV</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="participationTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Photo</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Name</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Matrix No</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Email</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Phone</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Application Status</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Last Transaction</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center ps-2">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inactiveStudents as $student)
                                        <tr>
                                            <td class="ps-4">
                                                <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                            </td>
                                            <td>
                                                @if ($student->user && $student->user->profile_picture_url)
                                                    <img src="{{ $student->user->profile_picture_url }}"
                                                        class="avatar avatar-sm me-3" alt="{{ $student->full_name }}">
                                                @else
                                                    <img src="{{ asset('assets/img/default-avatar.png') }}"
                                                        class="avatar avatar-sm me-3" alt="Default Avatar">
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $student->full_name }}</h6>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $student->matrix_no }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $student->user ? $student->user->email : 'N/A' }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $student->user && $student->user->phone_number ? $student->user->phone_number : 'N/A' }}
                                                </p>
                                            </td>
                                            <td>
                                                @if ($student->application)
                                                    <span
                                                        class="badge badge-sm bg-gradient-{{ $student->application->status === 'APPROVED' ? 'success' : ($student->application->status === 'PENDING' ? 'warning' : 'danger') }}">
                                                        {{ $student->application->status }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-secondary">No
                                                        Application</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $lastTransaction = $student
                                                        ->transactions()
                                                        ->latest('transaction_date')
                                                        ->first();
                                                @endphp
                                                @if ($lastTransaction)
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        {{ $lastTransaction->transaction_date->format('M d, Y') }}
                                                    </span>
                                                    <br>
                                                    <span class="text-xs text-muted">
                                                        {{ $lastTransaction->transaction_date->diffForHumans() }}
                                                    </span>
                                                @else
                                                    <span class="text-secondary text-xs font-weight-bold">Never</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('student.show', $student->id) }}"
                                                        class="btn btn-sm btn-outline-info mb-0"
                                                        title="View Student Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if ($student->user && $student->user->email)
                                                        <a href="mailto:{{ $student->user->email }}"
                                                            class="btn btn-sm btn-outline-primary mb-0"
                                                            title="Send Email">
                                                            <i class="fas fa-envelope"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-users text-success fa-3x mb-3"></i>
                                                    <h6 class="text-success">Excellent Participation!</h6>
                                                    <p class="text-muted">All eligible students have been active in the
                                                        last {{ $daysThreshold }} days.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($inactiveStudents->count() > 0)
                            <div class="px-4 pt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-sm text-muted">
                                            Showing {{ $inactiveStudents->count() }} inactive students out of
                                            {{ $statistics['total_eligible'] }} eligible students
                                        </p>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <p class="text-sm text-muted">
                                            Report generated on {{ now()->format('M d, Y \a\t H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations Card -->
        @if ($inactiveStudents->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card mx-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-lightbulb text-warning"></i> Recommendations
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-sm font-weight-bold">Immediate Actions:</h6>
                                    <ul class="text-sm">
                                        <li>Contact inactive students via email or phone</li>
                                        <li>Check if students are facing any issues with the system</li>
                                        <li>Verify if students are still enrolled and eligible</li>
                                        <li>Send reminders about the meal program benefits</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-sm font-weight-bold">Long-term Strategies:</h6>
                                    <ul class="text-sm">
                                        <li>Improve awareness campaigns about the program</li>
                                        <li>Gather feedback on program satisfaction</li>
                                        <li>Review eligibility criteria if participation is low</li>
                                        <li>Consider incentives for regular participation</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Initialize DataTable if you want advanced features
            $(document).ready(function() {
                if ($('#participationTable tbody tr').length > 10) {
                    $('#participationTable').DataTable({
                        "pageLength": 25,
                        "ordering": true,
                        "searching": true,
                        "lengthChange": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        "language": {
                            "search": "Search students:",
                            "lengthMenu": "Show _MENU_ students per page",
                            "info": "Showing _START_ to _END_ of _TOTAL_ inactive students",
                            "emptyTable": "All students are currently active"
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
