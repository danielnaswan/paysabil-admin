@extends('layouts.user_type.vendor')

@section('page-title', 'Dashboard')
@section('page-heading', 'Welcome back, ' . $vendor->business_name)

@section('content')
    <div class="container-fluid py-4">
        {{-- Statistics Cards --}}
        <div class="row">
            {{-- Today's Revenue --}}
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Revenue</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        RM {{ number_format($stats['today']['revenue'], 2) }}
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

            {{-- Today's Orders --}}
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Orders</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $stats['today']['transactions'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-basket text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Monthly Revenue --}}
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Monthly Revenue</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        RM {{ number_format($stats['month']['revenue'], 2) }}
                                        <span
                                            class="text-{{ $stats['month']['revenue_change'] >= 0 ? 'success' : 'danger' }} text-sm font-weight-bolder">
                                            {{ $stats['month']['revenue_change'] >= 0 ? '+' : '' }}{{ $stats['month']['revenue_change'] }}%
                                        </span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Average Rating --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Average Rating</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $stats['overall']['avg_rating'] }}
                                        <span class="text-warning">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $stats['overall']['avg_rating'])
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </span>
                                    </h5>
                                    <span class="text-sm">{{ $stats['overall']['total_reviews'] }} reviews</span>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="ni ni-trophy text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            {{-- Revenue Chart --}}
            <div class="col-lg-7 mb-lg-0 mb-4">
                <div class="card z-index-2">
                    <div class="card-header pb-0">
                        <h6>Revenue Overview</h6>
                        <p class="text-sm">
                            <i class="fa fa-arrow-up text-success"></i>
                            <span class="font-weight-bold">Last 7 days</span> revenue performance
                        </p>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Service Popularity --}}
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Popular Services</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="timeline timeline-one-side">
                            @forelse($chartData['service_popularity'] as $service)
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="ni ni-app text-success text-gradient"></i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $service['name'] }}</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                            {{ $service['orders'] }} orders</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center">
                                    <p class="text-muted">No service data available</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            {{-- Recent Transactions --}}
            <div class="col-lg-7 mb-lg-0 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h6>Recent Transactions</h6>
                            <a href="{{ route('vendor.transactions.index') }}"
                                class="btn btn-outline-primary btn-sm mb-0">View All</a>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Customer</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Service</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Amount</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions->take(5) as $transaction)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        @if ($transaction->student->user->profile_picture_url)
                                                            <img src="{{ $transaction->student->user->profile_picture_url }}"
                                                                class="avatar avatar-sm me-3 border-radius-lg">
                                                        @else
                                                            <img src="{{ asset('assets/img/default-avatar.png') }}"
                                                                class="avatar avatar-sm me-3 border-radius-lg">
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $transaction->student->user->name }}
                                                        </h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $transaction->student->matrix_no }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $transaction->qrCode->service->name }}</p>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $transaction->transaction_date->format('M j, Y H:i') }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold">RM
                                                    {{ number_format($transaction->amount, 2) }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $statusClass =
                                                        [
                                                            'COMPLETED' => 'success',
                                                            'PENDING' => 'warning',
                                                            'FAILED' => 'danger',
                                                            'CANCELLED' => 'secondary',
                                                        ][$transaction->status] ?? 'secondary';
                                                @endphp
                                                <span
                                                    class="badge badge-sm bg-gradient-{{ $statusClass }}">{{ $transaction->status }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <p class="text-muted mb-0">No transactions yet</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Reviews --}}
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h6>Recent Reviews</h6>
                            <a href="{{ route('vendor.feedback.index') }}"
                                class="btn btn-outline-primary btn-sm mb-0">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @forelse($recentReviews as $review)
                            <div class="d-flex mb-3">
                                <div class="avatar avatar-sm me-3 my-auto">
                                    @if ($review->student->user->profile_picture_url)
                                        <img src="{{ $review->student->user->profile_picture_url }}"
                                            class="border-radius-lg">
                                    @else
                                        <img src="{{ asset('assets/img/default-avatar.png') }}" class="border-radius-lg">
                                    @endif
                                </div>
                                <div class="d-flex flex-column justify-content-center flex-grow-1">
                                    <h6 class="mb-0 text-sm">{{ $review->student->user->name }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $review->stars)
                                                    <i class="fas fa-star text-xs"></i>
                                                @else
                                                    <i class="far fa-star text-xs"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span
                                            class="text-xs text-secondary">{{ $review->review_date->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-secondary mb-0 mt-1">
                                        {{ Str::limit($review->review_comment, 60) }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">No reviews yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Quick Actions</h6>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('vendor.services.create') }}" class="btn btn-outline-primary w-100">
                                    <i class="ni ni-app me-2"></i>Add New Service
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('vendor.qrcodes.create') }}" class="btn btn-outline-success w-100">
                                    <i class="ni ni-image me-2"></i>Generate QR Code
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('vendor.transactions.index') }}" class="btn btn-outline-info w-100">
                                    <i class="ni ni-credit-card me-2"></i>View Transactions
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('vendor.profile') }}" class="btn btn-outline-warning w-100">
                                    <i class="ni ni-single-02 me-2"></i>Update Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Revenue Chart
                var ctx = document.getElementById("chart-line").getContext("2d");

                var revenueData = @json($chartData['daily_revenue']);
                var labels = revenueData.map(item => item.date);
                var data = revenueData.map(item => item.revenue);

                var gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);
                gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
                gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
                gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)');

                new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Revenue",
                            tension: 0.4,
                            borderWidth: 0,
                            pointRadius: 5,
                            pointBackgroundColor: "#cb0c9f",
                            pointBorderColor: "transparent",
                            borderColor: "#cb0c9f",
                            backgroundColor: gradientStroke1,
                            fill: true,
                            data: data,
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
                                    callback: function(value) {
                                        return 'RM ' + value.toFixed(0);
                                    }
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
            });
        </script>
    @endpush
@endsection
