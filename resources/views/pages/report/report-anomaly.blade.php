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

        <!-- Anomaly Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Anomalies</p>
                                    <h5 class="font-weight-bolder mb-0 text-danger">
                                        {{ $anomalyStatistics['total_anomalies'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                    <i class="ni ni-bell-55 text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Students Affected</p>
                                    <h5 class="font-weight-bolder mb-0 text-warning">
                                        {{ $anomalyStatistics['unique_students_affected'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">High Severity</p>
                                    <h5 class="font-weight-bolder mb-0 text-danger">
                                        {{ $anomalyStatistics['severity_breakdown']['high'] ?? 0 }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                    <i class="ni ni-diamond text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Avg Violations</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $anomalyStatistics['average_violations_per_student'] }}
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

        <!-- Severity Breakdown Cards -->
        @if (isset($anomalyStatistics['severity_breakdown']) && array_sum($anomalyStatistics['severity_breakdown']) > 0)
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-frame">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm bg-gradient-danger shadow text-center me-3">
                                    <i class="ni ni-diamond text-white opacity-10"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-sm">High Severity</h6>
                                    <p class="text-sm mb-0 text-muted">4+ transactions per day</p>
                                    <h5 class="font-weight-bolder text-danger mb-0">
                                        {{ $anomalyStatistics['severity_breakdown']['high'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-frame">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm bg-gradient-warning shadow text-center me-3">
                                    <i class="ni ni-time-alarm text-white opacity-10"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-sm">Medium Severity</h6>
                                    <p class="text-sm mb-0 text-muted">3 transactions per day</p>
                                    <h5 class="font-weight-bolder text-warning mb-0">
                                        {{ $anomalyStatistics['severity_breakdown']['medium'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-frame">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm bg-gradient-info shadow text-center me-3">
                                    <i class="ni ni-bullet-list-67 text-white opacity-10"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-sm">Low Severity</h6>
                                    <p class="text-sm mb-0 text-muted">2 transactions per day</p>
                                    <h5 class="font-weight-bolder text-info mb-0">
                                        {{ $anomalyStatistics['severity_breakdown']['low'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Anomaly Report -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-start">
                            <div class="mb-3 mb-md-0">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    Fraud & Anomaly Detection Report
                                </h5>
                                <p class="text-sm mb-0">
                                    Students with multiple transactions on the same day
                                </p>
                                <p class="text-xs text-muted mb-0">
                                    Detection Period: {{ $validated['start_date'] ?? now()->subDays(30)->format('M d, Y') }}
                                    - {{ $validated['end_date'] ?? now()->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-end">
                                <!-- Date and Severity Filter Form -->
                                <form method="GET" action="{{ route('report.anomaly') }}"
                                    class="d-flex align-items-end gap-2" id="filterForm">
                                    <div>
                                        <label for="start_date" class="form-label text-xs mb-1">From Date</label>
                                        <input name="start_date" id="start_date" class="form-control form-control-sm"
                                            type="date" style="min-width: 130px;"
                                            value="{{ $validated['start_date'] ?? now()->subDays(30)->format('Y-m-d') }}">
                                    </div>
                                    <div>
                                        <label for="end_date" class="form-label text-xs mb-1">To Date</label>
                                        <input name="end_date" id="end_date" class="form-control form-control-sm"
                                            type="date" style="min-width: 130px;"
                                            value="{{ $validated['end_date'] ?? now()->format('Y-m-d') }}">
                                    </div>
                                    <div>
                                        <label for="severity" class="form-label text-xs mb-1">Severity</label>
                                        <select name="severity" id="severity" class="form-select form-select-sm"
                                            style="min-width: 120px;">
                                            <option value="all"
                                                {{ ($validated['severity'] ?? 'all') == 'all' ? 'selected' : '' }}>All
                                                Severities</option>
                                            <option value="high"
                                                {{ ($validated['severity'] ?? '') == 'high' ? 'selected' : '' }}>High (4+
                                                trans.)</option>
                                            <option value="medium"
                                                {{ ($validated['severity'] ?? '') == 'medium' ? 'selected' : '' }}>Medium
                                                (3 trans.)</option>
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
                                                href="{{ route('report.anomaly', array_merge(request()->all(), ['export' => 'pdf'])) }}">
                                                <i class="fas fa-file-pdf text-danger me-2"></i> Export as PDF</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.anomaly', array_merge(request()->all(), ['export' => 'excel'])) }}">
                                                <i class="fas fa-file-excel text-success me-2"></i> Export as Excel</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.anomaly', array_merge(request()->all(), ['export' => 'csv'])) }}">
                                                <i class="fas fa-file-csv text-info me-2"></i> Export as CSV</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="anomalyTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Date</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Student</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Matrix No</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Transaction Count</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Amount</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Severity</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Vendors Involved</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enhancedAnomalies as $anomaly)
                                        <tr class="anomaly-row severity-{{ $anomaly['severity'] }}">
                                            <td class="ps-4">
                                                <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">
                                                        {{ \Carbon\Carbon::parse($anomaly['transaction_date'])->format('M d, Y') }}
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ \Carbon\Carbon::parse($anomaly['transaction_date'])->format('l') }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($anomaly['student']->user && $anomaly['student']->user->profile_picture_url)
                                                        <img src="{{ $anomaly['student']->user->profile_picture_url }}"
                                                            class="avatar avatar-sm me-3"
                                                            alt="{{ $anomaly['student']->full_name }}">
                                                    @else
                                                        <img src="{{ asset('assets/img/default-avatar.png') }}"
                                                            class="avatar avatar-sm me-3" alt="Default Avatar">
                                                    @endif
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $anomaly['student']->full_name }}</h6>
                                                        @if ($anomaly['student']->user)
                                                            <p class="text-xs text-secondary mb-0">
                                                                {{ $anomaly['student']->user->email }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $anomaly['student']->matrix_no }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="badge badge-lg bg-gradient-{{ $anomaly['severity'] == 'high' ? 'danger' : ($anomaly['severity'] == 'medium' ? 'warning' : 'info') }}">
                                                        {{ $anomaly['transaction_count'] }} transactions
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-sm font-weight-bold text-danger">
                                                    RM {{ number_format($anomaly['total_amount'], 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-sm bg-gradient-{{ $anomaly['severity'] == 'high' ? 'danger' : ($anomaly['severity'] == 'medium' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($anomaly['severity']) }}
                                                </span>
                                                @if ($anomaly['severity'] == 'high')
                                                    <i class="fas fa-exclamation-triangle text-danger ms-1"
                                                        title="Critical violation"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="vendor-list">
                                                    @foreach ($anomaly['vendors_involved'] as $vendor)
                                                        <span
                                                            class="badge badge-sm bg-gradient-secondary me-1 mb-1">{{ $vendor }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-sm btn-outline-info mb-0"
                                                        onclick="viewTransactionDetails({{ json_encode($anomaly['transactions']) }})"
                                                        title="View Transaction Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('student.show', $anomaly['student']->id) }}"
                                                        class="btn btn-sm btn-outline-primary mb-0"
                                                        title="View Student Profile">
                                                        <i class="fas fa-user"></i>
                                                    </a>
                                                    @if ($anomaly['student']->user && $anomaly['student']->user->email)
                                                        <a href="mailto:{{ $anomaly['student']->user->email }}?subject=Regarding Multiple Transactions on {{ \Carbon\Carbon::parse($anomaly['transaction_date'])->format('M d, Y') }}"
                                                            class="btn btn-sm btn-outline-warning mb-0"
                                                            title="Send Warning Email">
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
                                                    <i class="fas fa-shield-alt text-success fa-3x mb-3"></i>
                                                    <h6 class="text-success">No Anomalies Detected!</h6>
                                                    <p class="text-muted">All transactions appear normal for the selected
                                                        period.</p>
                                                    <p class="text-sm text-muted">The system hasn't detected any students
                                                        with multiple transactions per day.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($enhancedAnomalies->count() > 0)
                            <div class="px-4 pt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-sm text-muted">
                                            Found {{ $enhancedAnomalies->count() }} anomalies affecting
                                            {{ $anomalyStatistics['unique_students_affected'] }} students
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

        <!-- Action Recommendations -->
        @if ($enhancedAnomalies->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card mx-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-clipboard-list text-primary"></i> Recommended Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-sm font-weight-bold text-danger">High Priority Actions:</h6>
                                    <ul class="text-sm">
                                        @if (($anomalyStatistics['severity_breakdown']['high'] ?? 0) > 0)
                                            <li>Investigate high-severity violations immediately
                                                ({{ $anomalyStatistics['severity_breakdown']['high'] }} cases)</li>
                                            <li>Contact affected students to verify legitimate usage</li>
                                        @endif
                                        <li>Review system access controls and authentication</li>
                                        <li>Implement stricter daily transaction limits if needed</li>
                                        <li>Consider temporarily suspending accounts with repeated violations</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-sm font-weight-bold text-warning">Monitoring & Prevention:</h6>
                                    <ul class="text-sm">
                                        <li>Set up automated alerts for future anomalies</li>
                                        <li>Enhance QR code security and expiration times</li>
                                        <li>Review vendor cooperation in anomaly detection</li>
                                        <li>Educate students about proper system usage</li>
                                        <li>Regular audit of transaction patterns</li>
                                    </ul>
                                </div>
                            </div>
                            @if (($anomalyStatistics['severity_breakdown']['high'] ?? 0) > 2)
                                <div class="alert alert-danger mt-3" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Critical Alert:</strong> {{ $anomalyStatistics['severity_breakdown']['high'] }}
                                    high-severity anomalies detected.
                                    Immediate administrative review recommended.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionDetailsModalLabel">
                        <i class="fas fa-list text-primary me-2"></i>Transaction Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="transactionDetailsContent">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize DataTable for better UX if many anomalies
                @if ($enhancedAnomalies->count() > 10)
                    $('#anomalyTable').DataTable({
                        "pageLength": 25,
                        "ordering": true,
                        "searching": true,
                        "lengthChange": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        "order": [
                            [1, "desc"],
                            [4, "desc"]
                        ], // Sort by date desc, then by transaction count desc
                        "language": {
                            "search": "Search anomalies:",
                            "lengthMenu": "Show _MENU_ anomalies per page",
                            "info": "Showing _START_ to _END_ of _TOTAL_ anomalies"
                        },
                        "columnDefs": [{
                                "orderable": false,
                                "targets": [8]
                            } // Disable ordering on action column
                        ]
                    });
                @endif

                // Add severity-based row styling
                document.querySelectorAll('.anomaly-row').forEach(function(row) {
                    if (row.classList.contains('severity-high')) {
                        row.style.backgroundColor = 'rgba(220, 53, 69, 0.1)';
                        row.style.borderLeft = '3px solid #dc3545';
                    } else if (row.classList.contains('severity-medium')) {
                        row.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
                        row.style.borderLeft = '3px solid #ffc107';
                    } else if (row.classList.contains('severity-low')) {
                        row.style.backgroundColor = 'rgba(13, 202, 240, 0.1)';
                        row.style.borderLeft = '3px solid #0dcaf0';
                    }
                });

                // Set max date to today for date inputs
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('start_date').setAttribute('max', today);
                document.getElementById('end_date').setAttribute('max', today);

                // Validate date range
                document.getElementById('start_date').addEventListener('change', function() {
                    const startDate = this.value;
                    const endDateInput = document.getElementById('end_date');
                    endDateInput.setAttribute('min', startDate);

                    if (endDateInput.value && endDateInput.value < startDate) {
                        endDateInput.value = startDate;
                    }
                });
            });

            // Function to view transaction details in modal
            function viewTransactionDetails(transactions) {
                const modalContent = document.getElementById('transactionDetailsContent');

                let html = '<div class="table-responsive">';
                html += '<table class="table table-sm">';
                html += '<thead><tr>';
                html += '<th>Time</th><th>Vendor</th><th>Service</th><th>Amount</th><th>QR Code</th>';
                html += '</tr></thead><tbody>';

                transactions.forEach(function(transaction, index) {
                    const time = new Date(transaction.transaction_date).toLocaleTimeString();
                    const vendor = transaction.vendor ? transaction.vendor.business_name : 'N/A';
                    const service = transaction.qr_code && transaction.qr_code.service ?
                        transaction.qr_code.service.name : transaction.meal_details;
                    const amount = 'RM ' + parseFloat(transaction.amount).toFixed(2);
                    const qrCode = transaction.qr_code ? transaction.qr_code.code.substring(0, 8) + '...' : 'N/A';

                    html += `<tr>
                        <td><span class="badge badge-sm bg-gradient-info">${time}</span></td>
                        <td>${vendor}</td>
                        <td>${service}</td>
                        <td><span class="text-success font-weight-bold">${amount}</span></td>
                        <td><code>${qrCode}</code></td>
                    </tr>`;
                });

                html += '</tbody></table></div>';

                // Add summary
                const totalAmount = transactions.reduce((sum, t) => sum + parseFloat(t.amount), 0);
                const uniqueVendors = [...new Set(transactions.map(t => t.vendor ? t.vendor.business_name : 'Unknown'))];

                html += '<div class="mt-3 p-3 bg-light rounded">';
                html += '<h6 class="text-sm font-weight-bold mb-2">Summary:</h6>';
                html += `<p class="text-sm mb-1"><strong>Total Transactions:</strong> ${transactions.length}</p>`;
                html += `<p class="text-sm mb-1"><strong>Total Amount:</strong> RM ${totalAmount.toFixed(2)}</p>`;
                html += `<p class="text-sm mb-0"><strong>Vendors Involved:</strong> ${uniqueVendors.join(', ')}</p>`;
                html += '</div>';

                modalContent.innerHTML = html;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
                modal.show();
            }
        </script>
    @endpush
@endsection
