@extends('layouts.user_type.vendor')

@section('page-title', 'Transactions')
@section('page-heading', 'Transaction History')

@section('content')
    <div class="container-fluid py-4">
        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Transactions</p>
                                    <h5 class="font-weight-bolder mb-0">{{ number_format($stats['total_transactions']) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-basket text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Revenue</p>
                                    <h5 class="font-weight-bolder mb-0">RM {{ number_format($stats['total_revenue'], 2) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Average Order</p>
                                    <h5 class="font-weight-bolder mb-0">RM
                                        {{ number_format($stats['average_order_value'], 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-chart-bar-32 text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Success Rate</p>
                                    <h5 class="font-weight-bolder mb-0">{{ $stats['completion_rate'] }}%</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="ni ni-trophy text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters and Search --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">Transactions</h5>
                            </div>
                            <div class="d-flex">
                                <a href="{{ route('vendor.transactions.export', request()->query()) }}"
                                    class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fas fa-download me-1"></i>Export CSV
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Form --}}
                    <div class="card-body">
                        <form method="GET" action="{{ route('vendor.transactions.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="FAILED" {{ request('status') == 'FAILED' ? 'selected' : '' }}>Failed
                                    </option>
                                    <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                    value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Student name, matrix no, service..." value="{{ request('search') }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-sm me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('vendor.transactions.index') }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Transaction</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
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
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Date</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">#{{ $transaction->id }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $transaction->transaction_date->format('M j, Y H:i') }}</p>
                                                    </div>
                                                </div>
                                            </td>
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
                                                    {{ Str::limit($transaction->meal_details, 30) }}</p>
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
                                            <td class="align-middle text-center">
                                                <span
                                                    class="text-secondary text-xs font-weight-bold">{{ $transaction->transaction_date->format('M j, Y') }}</span>
                                                <br>
                                                <span
                                                    class="text-secondary text-xs">{{ $transaction->transaction_date->format('H:i') }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('vendor.transactions.show', $transaction->id) }}"
                                                    class="text-secondary font-weight-bold text-xs" data-toggle="tooltip"
                                                    data-original-title="View transaction">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <p class="text-muted mb-0">No transactions found</p>
                                                <small class="text-muted">Transactions will appear here once customers
                                                    start purchasing your services</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if ($transactions->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
