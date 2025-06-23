@extends('layouts.user_type.auth')

@section('content')

    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Revenue</p>
                                <h5 class="font-weight-bolder mb-0">
                                    RM {{ number_format($statistics['today_revenue'], 2) }}
                                    @if ($statistics['revenue_change'] > 0)
                                        <span
                                            class="text-success text-sm font-weight-bolder">+{{ $statistics['revenue_change'] }}%</span>
                                    @elseif($statistics['revenue_change'] < 0)
                                        <span
                                            class="text-danger text-sm font-weight-bolder">{{ $statistics['revenue_change'] }}%</span>
                                    @else
                                        <span class="text-secondary text-sm font-weight-bolder">0%</span>
                                    @endif
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Students</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($statistics['active_students']) }}
                                    <span
                                        class="text-info text-sm font-weight-bolder">{{ $statistics['participation_rate'] }}%</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Transactions</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($statistics['today_transactions']) }}
                                    @if ($statistics['transaction_change'] > 0)
                                        <span
                                            class="text-success text-sm font-weight-bolder">+{{ $statistics['transaction_change'] }}%</span>
                                    @elseif($statistics['transaction_change'] < 0)
                                        <span
                                            class="text-danger text-sm font-weight-bolder">{{ $statistics['transaction_change'] }}%</span>
                                    @else
                                        <span class="text-secondary text-sm font-weight-bolder">0%</span>
                                    @endif
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pending Applications</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($statistics['pending_applications']) }}
                                    @if ($systemHealth['overdue_applications'] > 0)
                                        <span
                                            class="text-warning text-sm font-weight-bolder">{{ $systemHealth['overdue_applications'] }}
                                            overdue</span>
                                    @else
                                        <span class="text-success text-sm font-weight-bolder">On track</span>
                                    @endif
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row mt-4">
        <div class="col-lg-5 mb-lg-0 mb-4">
            <div class="card z-index-2">
                <div class="card-body p-3">
                    <div class="bg-gradient-dark border-radius-lg py-3 pe-1 mb-3">
                        <div class="chart">
                            <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                    <h6 class="ms-2 mt-4 mb-0">Daily Transactions</h6>
                    <p class="text-sm ms-2">
                        <span class="font-weight-bolder">{{ $statistics['today_transactions'] }}</span> transactions today
                    </p>
                    <div class="container border-radius-lg">
                        <div class="row">
                            <div class="col-3 py-3 ps-0">
                                <div class="d-flex mb-2">
                                    <div
                                        class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="ni ni-single-02 text-white"></i>
                                    </div>
                                    <p class="text-xs mt-1 mb-0 font-weight-bold">Students</p>
                                </div>
                                <h4 class="font-weight-bolder">{{ number_format($statistics['active_students']) }}</h4>
                                <div class="progress w-75">
                                    <div class="progress-bar bg-dark"
                                        style="width: {{ $statistics['participation_rate'] }}%" role="progressbar"></div>
                                </div>
                            </div>
                            <div class="col-3 py-3 ps-0">
                                <div class="d-flex mb-2">
                                    <div
                                        class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="ni ni-shop text-white"></i>
                                    </div>
                                    <p class="text-xs mt-1 mb-0 font-weight-bold">Vendors</p>
                                </div>
                                <h4 class="font-weight-bolder">{{ $statistics['total_vendors'] }}</h4>
                                <div class="progress w-75">
                                    <div class="progress-bar bg-dark w-100" role="progressbar"></div>
                                </div>
                            </div>
                            <div class="col-3 py-3 ps-0">
                                <div class="d-flex mb-2">
                                    <div
                                        class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-warning text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="ni ni-money-coins text-white"></i>
                                    </div>
                                    <p class="text-xs mt-1 mb-0 font-weight-bold">Revenue</p>
                                </div>
                                <h4 class="font-weight-bolder">RM {{ number_format($statistics['month_revenue']) }}</h4>
                                <div class="progress w-75">
                                    <div class="progress-bar bg-dark w-75" role="progressbar"></div>
                                </div>
                            </div>
                            <div class="col-3 py-3 ps-0">
                                <div class="d-flex mb-2">
                                    <div
                                        class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="ni ni-mobile-button text-white"></i>
                                    </div>
                                    <p class="text-xs mt-1 mb-0 font-weight-bold">QR Codes</p>
                                </div>
                                <h4 class="font-weight-bolder">{{ $statistics['active_qr_codes'] }}</h4>
                                <div class="progress w-75">
                                    <div class="progress-bar bg-dark"
                                        style="width: {{ $systemHealth['qr_code_active_rate'] }}%" role="progressbar">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>Revenue Overview</h6>
                    <p class="text-sm">
                        @if ($statistics['revenue_change'] > 0)
                            <i class="fa fa-arrow-up text-success"></i>
                            <span class="font-weight-bold">{{ $statistics['revenue_change'] }}% more</span> than last
                            month
                        @elseif($statistics['revenue_change'] < 0)
                            <i class="fa fa-arrow-down text-danger"></i>
                            <span class="font-weight-bold">{{ abs($statistics['revenue_change']) }}% less</span> than last
                            month
                        @else
                            <i class="fa fa-minus text-secondary"></i>
                            <span class="font-weight-bold">Same as</span> last month
                        @endif
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Vendors and Recent Activities --}}
    <div class="row my-4">
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>Top Performing Vendors</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-check text-info" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">{{ count($topVendors) }}</span> active vendors this
                                month
                            </p>
                        </div>
                        <div class="col-lg-6 col-5 my-auto text-end">
                            <a href="{{ route('vendor.index') }}" class="btn btn-outline-primary btn-sm mb-0">View
                                All</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        @if (count($topVendors) > 0)
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Vendor</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Transactions</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Revenue</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topVendors as $vendor)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <div
                                                            class="avatar avatar-sm me-3 bg-gradient-{{ ['primary', 'info', 'success', 'warning', 'danger'][$loop->index % 5] }}">
                                                            <span class="text-white text-sm font-weight-bold">
                                                                {{ strtoupper(substr($vendor['name'], 0, 2)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $vendor['name'] }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $vendor['category'] }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold">{{ $vendor['transactions'] }}
                                                    meals</span>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold">RM
                                                    {{ number_format($vendor['revenue'], 2) }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span
                                                        class="me-2 text-xs font-weight-bold">{{ number_format($vendor['rating'], 1) }}</span>
                                                    <div>
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= $vendor['rating'])
                                                                <i class="fa fa-star text-warning"></i>
                                                            @else
                                                                <i class="fa fa-star text-secondary opacity-5"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-4">
                                <i class="ni ni-shop text-muted text-4xl"></i>
                                <h6 class="text-muted mt-2">No vendor activity this month</h6>
                                <p class="text-sm text-muted">Vendors will appear here once they start receiving orders</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Recent Activities</h6>
                    <p class="text-sm">
                        <i class="fa fa-clock text-info" aria-hidden="true"></i>
                        <span class="font-weight-bold">Live</span> system updates
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="timeline timeline-one-side">
                        @forelse($recentActivities as $activity)
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }} text-gradient"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $activity['title'] }}</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">{{ $activity['time'] }}
                                    </p>
                                    @if (isset($activity['description']))
                                        <p class="text-xs text-muted mt-1 mb-0">{{ $activity['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="ni ni-notification-70 text-muted text-3xl"></i>
                                <h6 class="text-muted mt-2">No recent activities</h6>
                                <p class="text-sm text-muted">Activities will appear here as users interact with the system
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- System Health Dashboard --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>System Health Overview</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-heartbeat text-success" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">System Status:</span>
                                @if ($systemHealth['transaction_success_rate'] >= 95)
                                    <span class="text-success">Excellent</span>
                                @elseif($systemHealth['transaction_success_rate'] >= 90)
                                    <span class="text-info">Good</span>
                                @elseif($systemHealth['transaction_success_rate'] >= 80)
                                    <span class="text-warning">Fair</span>
                                @else
                                    <span class="text-danger">Needs Attention</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-lg-6 col-5 my-auto text-end">
                            <a href="{{ route('report.index') }}" class="btn btn-outline-info btn-sm mb-0">View
                                Reports</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-3 pb-2">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-plain">
                                <div class="card-body text-center">
                                    <div class="icon icon-shape icon-lg bg-gradient-success shadow mx-auto">
                                        <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-success font-weight-bolder mt-3">
                                        {{ $systemHealth['transaction_success_rate'] }}%</h5>
                                    <p class="text-sm">Transaction Success Rate</p>
                                    <div class="progress">
                                        <div class="progress-bar bg-gradient-success" role="progressbar"
                                            style="width: {{ $systemHealth['transaction_success_rate'] }}%"
                                            aria-valuenow="{{ $systemHealth['transaction_success_rate'] }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-plain">
                                <div class="card-body text-center">
                                    <div class="icon icon-shape icon-lg bg-gradient-info shadow mx-auto">
                                        <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-info font-weight-bolder mt-3">
                                        {{ $systemHealth['student_eligibility_rate'] }}%</h5>
                                    <p class="text-sm">Student Eligibility Rate</p>
                                    <div class="progress">
                                        <div class="progress-bar bg-gradient-info" role="progressbar"
                                            style="width: {{ $systemHealth['student_eligibility_rate'] }}%"
                                            aria-valuenow="{{ $systemHealth['student_eligibility_rate'] }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-plain">
                                <div class="card-body text-center">
                                    <div class="icon icon-shape icon-lg bg-gradient-warning shadow mx-auto">
                                        <i class="ni ni-mobile-button text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-warning font-weight-bolder mt-3">
                                        {{ $systemHealth['qr_code_active_rate'] }}%</h5>
                                    <p class="text-sm">Active QR Codes Rate</p>
                                    <div class="progress">
                                        <div class="progress-bar bg-gradient-warning" role="progressbar"
                                            style="width: {{ $systemHealth['qr_code_active_rate'] }}%"
                                            aria-valuenow="{{ $systemHealth['qr_code_active_rate'] }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-plain">
                                <div class="card-body text-center">
                                    <div
                                        class="icon icon-shape icon-lg bg-gradient-{{ $systemHealth['overdue_applications'] > 0 ? 'danger' : 'primary' }} shadow mx-auto">
                                        <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                    <h5
                                        class="text-{{ $systemHealth['overdue_applications'] > 0 ? 'danger' : 'primary' }} font-weight-bolder mt-3">
                                        {{ $systemHealth['pending_applications'] }}
                                    </h5>
                                    <p class="text-sm">Pending Applications</p>
                                    @if ($systemHealth['overdue_applications'] > 0)
                                        <small class="text-danger">{{ $systemHealth['overdue_applications'] }}
                                            overdue</small>
                                    @else
                                        <small class="text-success">All on track</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.onload = function() {
            // Daily transactions chart (Bar chart)
            var ctx = document.getElementById("chart-bars").getContext("2d");

            // Data from PHP
            var dailyLabels = @json($chartData['daily']['labels']);
            var dailyTransactions = @json($chartData['daily']['transactions']);

            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: "Transactions",
                        tension: 0.4,
                        borderWidth: 0,
                        borderRadius: 4,
                        borderSkipped: false,
                        backgroundColor: "#fff",
                        data: dailyTransactions,
                        maxBarThickness: 6
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                            },
                            ticks: {
                                suggestedMin: 0,
                                suggestedMax: Math.max(...dailyTransactions) + 10,
                                beginAtZero: true,
                                padding: 15,
                                font: {
                                    size: 14,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                                color: "#fff"
                            },
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false
                            },
                            ticks: {
                                display: false
                            },
                        },
                    },
                },
            });

            // Revenue overview chart (Line chart)
            var ctx2 = document.getElementById("chart-line").getContext("2d");

            // Data from PHP
            var monthlyLabels = @json($chartData['monthly']['labels']);
            var monthlyRevenue = @json($chartData['monthly']['revenue']);
            var monthlyTransactions = @json($chartData['monthly']['transactions']);

            var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);
            gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
            gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
            gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)');

            var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);
            gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
            gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
            gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)');

            new Chart(ctx2, {
                type: "line",
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: "Revenue (RM)",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#cb0c9f",
                        borderWidth: 3,
                        backgroundColor: gradientStroke1,
                        fill: true,
                        data: monthlyRevenue,
                        maxBarThickness: 6
                    }, {
                        label: "Transactions",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#3A416F",
                        borderWidth: 3,
                        backgroundColor: gradientStroke2,
                        fill: true,
                        data: monthlyTransactions,
                        maxBarThickness: 6
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                padding: 10,
                                color: '#b2b9bf',
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                color: '#b2b9bf',
                                padding: 20,
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                            }
                        },
                    },
                },
            });
        }
    </script>
@endpush
