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

        <!-- Financial Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Revenue</p>
                                    <h5 class="font-weight-bolder mb-0 text-success">
                                        RM {{ number_format($financialSummary['total_revenue'], 2) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Transactions</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $financialSummary['total_transactions'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Average Transaction</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        RM {{ number_format($financialSummary['average_transaction_value'], 2) }}
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
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Unique Students</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $financialSummary['unique_students'] }}
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
        </div>

        <!-- Main Transaction Report -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-start">
                            <div class="mb-3 mb-md-0">
                                <h5 class="mb-0">{{ $vendorModel->business_name }} - Financial Report</h5>
                                <p class="text-sm mb-0">
                                    Period: {{ $periodInfo['description'] }}
                                </p>
                                <p class="text-xs text-muted mb-0">
                                    Service Category: {{ $vendorModel->service_category }}
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-end">
                                <!-- Date Filter Form -->
                                <form method="GET" action="{{ route('report.financial.vendor', $vendorModel->id) }}"
                                    class="d-flex align-items-end gap-2" id="filterForm">
                                    <div>
                                        <label for="filter_type" class="form-label text-xs mb-1">Filter Type</label>
                                        <select name="filter_type" id="filter_type" class="form-select form-select-sm"
                                            style="min-width: 120px;" onchange="toggleDateInputs()">
                                            <option value="month" {{ request('month') ? 'selected' : '' }}>Monthly</option>
                                            <option value="custom" {{ (request('start_date') && request('end_date')) ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Monthly Filter -->
                                    <div id="monthFilter" style="{{ request('month') || (!request('start_date') && !request('end_date')) ? '' : 'display: none;' }}">
                                        <label for="month" class="form-label text-xs mb-1">Month</label>
                                        <input name="month" id="month" class="form-control form-control-sm monthpicker" 
                                            placeholder="Select month" type="text" style="min-width: 150px;"
                                            value="{{ request('month', now()->format('Y-m')) }}">
                                    </div>
                                    
                                    <!-- Custom Range Filter -->
                                    <div id="customFilter" style="{{ (request('start_date') && request('end_date')) ? '' : 'display: none;' }}">
                                        <div class="d-flex gap-1">
                                            <div>
                                                <label for="start_date" class="form-label text-xs mb-1">From</label>
                                                <input name="start_date" id="start_date" class="form-control form-control-sm" 
                                                    type="date" style="min-width: 130px;"
                                                    value="{{ request('start_date') }}">
                                            </div>
                                            <div>
                                                <label for="end_date" class="form-label text-xs mb-1">To</label>
                                                <input name="end_date" id="end_date" class="form-control form-control-sm" 
                                                    type="date" style="min-width: 130px;"
                                                    value="{{ request('end_date') }}">
                                            </div>
                                        </div>
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
                                                href="{{ route('report.financial.vendor', array_merge(['vendor' => $vendorModel->id], request()->all(), ['export' => 'pdf'])) }}">
                                                <i class="fas fa-file-pdf text-danger me-2"></i> Export as PDF</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.financial.vendor', array_merge(['vendor' => $vendorModel->id], request()->all(), ['export' => 'excel'])) }}">
                                                <i class="fas fa-file-excel text-success me-2"></i> Export as Excel</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.financial.vendor', array_merge(['vendor' => $vendorModel->id], request()->all(), ['export' => 'csv'])) }}">
                                                <i class="fas fa-file-csv text-info me-2"></i> Export as CSV</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="transactionTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date & Time</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Student</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Matrix No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Service/Meal</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Amount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">QR Code</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactionData as $transaction)
                                        <tr>
                                            <td class="ps-4">
                                                <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $transaction->transaction_date->format('M d, Y') }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $transaction->transaction_date->format('H:i') }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $transaction->student->full_name ?? 'N/A' }}</h6>
                                                    @if($transaction->student && $transaction->student->user)
                                                        <p class="text-xs text-secondary mb-0">{{ $transaction->student->user->email }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $transaction->student->matrix_no ?? 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">
                                                        {{ $transaction->qrCode?->service?->name ?? $transaction->meal_details }}
                                                    </h6>
                                                    @if($transaction->qrCode?->service?->category)
                                                        <p class="text-xs text-secondary mb-0">{{ $transaction->qrCode->service->category }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-sm font-weight-bold text-success">
                                                    RM {{ number_format($transaction->amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-sm bg-gradient-{{ $transaction->status === 'COMPLETED' ? 'success' : ($transaction->status === 'PENDING' ? 'warning' : 'danger') }}">
                                                    {{ $transaction->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->qrCode)
                                                    <span class="text-xs font-weight-bold text-secondary">
                                                        {{ substr($transaction->qrCode->code, 0, 8) }}...
                                                    </span>
                                                @else
                                                    <span class="text-xs text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-receipt text-muted fa-3x mb-3"></i>
                                                    <h6 class="text-muted">No Transactions Found</h6>
                                                    <p class="text-muted">No transactions found for the selected period.</p>
                                                    <p class="text-sm text-muted">Try adjusting your date filter or contact students to encourage participation.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($transactionData->count() > 0)
                            <div class="px-4 pt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-sm text-muted">
                                            Showing {{ $transactionData->count() }} transactions for {{ $vendorModel->business_name }}
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

        <!-- Daily Breakdown Chart (if data available) -->
        @if($transactionData->count() > 0 && count($financialSummary['daily_breakdown']) > 1)
            <div class="row">
                <div class="col-12">
                    <div class="card mx-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-line text-primary"></i> Daily Revenue Breakdown
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="dailyRevenueChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Insights and Recommendations -->
        @if($transactionData->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card mx-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-lightbulb text-warning"></i> Financial Insights
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-sm font-weight-bold">Revenue Analysis:</h6>
                                    <ul class="text-sm">
                                        <li>Peak transaction day: {{ $transactionData->groupBy(function($t) { return $t->transaction_date->format('Y-m-d'); })->sortByDesc(function($group) { return $group->count(); })->keys()->first() ? \Carbon\Carbon::parse($transactionData->groupBy(function($t) { return $t->transaction_date->format('Y-m-d'); })->sortByDesc(function($group) { return $group->count(); })->keys()->first())->format('M d, Y') : 'N/A' }}</li>
                                        <li>Average transactions per day: {{ $financialSummary['total_transactions'] > 0 && count($financialSummary['daily_breakdown']) > 0 ? round($financialSummary['total_transactions'] / count($financialSummary['daily_breakdown']), 1) : 0 }}</li>
                                        <li>Student retention: {{ $financialSummary['unique_students'] }} unique customers</li>
                                        <li>Revenue efficiency: RM {{ $financialSummary['average_transaction_value'] }} per transaction</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-sm font-weight-bold">Business Recommendations:</h6>
                                    <ul class="text-sm">
                                        @if($financialSummary['average_transaction_value'] < 5)
                                            <li>Consider promoting higher-value meal combinations</li>
                                        @endif
                                        @if($financialSummary['unique_students'] < 50)
                                            <li>Focus on attracting new customers through marketing</li>
                                        @endif
                                        @if(count($financialSummary['daily_breakdown']) > 0)
                                            <li>Optimize staffing based on peak transaction periods</li>
                                        @endif
                                        <li>Maintain consistent service quality to retain customers</li>
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize month picker
                if (document.querySelector('.monthpicker')) {
                    flatpickr('.monthpicker', {
                        dateFormat: "Y-m",
                        plugins: [
                            new monthSelectPlugin({
                                shorthand: true,
                                dateFormat: "Y-m",
                                altFormat: "F Y"
                            })
                        ]
                    });
                }

                // Toggle date input types
                window.toggleDateInputs = function() {
                    const filterType = document.getElementById('filter_type').value;
                    const monthFilter = document.getElementById('monthFilter');
                    const customFilter = document.getElementById('customFilter');
                    
                    if (filterType === 'month') {
                        monthFilter.style.display = '';
                        customFilter.style.display = 'none';
                        // Clear custom date inputs
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';
                    } else {
                        monthFilter.style.display = 'none';
                        customFilter.style.display = '';
                        // Clear month input
                        document.getElementById('month').value = '';
                    }
                };

                // Initialize DataTable for better UX if many transactions
                @if($transactionData->count() > 10)
                    $('#transactionTable').DataTable({
                        "pageLength": 25,
                        "ordering": true,
                        "searching": true,
                        "lengthChange": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        "order": [[1, "desc"]], // Sort by date desc
                        "language": {
                            "search": "Search transactions:",
                            "lengthMenu": "Show _MENU_ transactions per page",
                            "info": "Showing _START_ to _END_ of _TOTAL_ transactions"
                        }
                    });
                @endif

                // Daily Revenue Chart
                @if($transactionData->count() > 0 && count($financialSummary['daily_breakdown']) > 1)
                    const dailyData = @json($financialSummary['daily_breakdown']);
                    const labels = Object.keys(dailyData).map(date => {
                        return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    });
                    const revenues = Object.values(dailyData).map(day => day.revenue);
                    const counts = Object.values(dailyData).map(day => day.count);

                    const ctx = document.getElementById('dailyRevenueChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Daily Revenue (RM)',
                                data: revenues,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1,
                                yAxisID: 'y'
                            }, {
                                label: 'Transaction Count',
                                data: counts,
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                tension: 0.1,
                                yAxisID: 'y1'
                            }]
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Date'
                                    }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: 'Revenue (RM)'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: 'Transaction Count'
                                    },
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                }
                            }
                        }
                    });
                @endif
            });
        </script>
    @endpush
@endsection