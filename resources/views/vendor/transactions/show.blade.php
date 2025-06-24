@extends('layouts.user_type.vendor')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">Transaction Details</h6>
                            <a href="{{ route('vendor.transactions.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Information -->
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Transaction Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <small class="text-uppercase text-secondary text-xs font-weight-bold">Transaction
                                        ID</small>
                                    <p class="text-dark font-weight-bold mb-0">#{{ $transaction->id }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <small class="text-uppercase text-secondary text-xs font-weight-bold">Date &
                                        Time</small>
                                    <p class="text-dark font-weight-bold mb-0">
                                        {{ $transaction->transaction_date->format('M d, Y \a\t H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <small class="text-uppercase text-secondary text-xs font-weight-bold">Status</small>
                                    <div>
                                        @if ($transaction->status == 'COMPLETED')
                                            <span class="badge badge-sm bg-gradient-success">Completed</span>
                                        @elseif($transaction->status == 'PENDING')
                                            <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                        @elseif($transaction->status == 'FAILED')
                                            <span class="badge badge-sm bg-gradient-danger">Failed</span>
                                        @else
                                            <span
                                                class="badge badge-sm bg-gradient-secondary">{{ $transaction->status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <small class="text-uppercase text-secondary text-xs font-weight-bold">Amount</small>
                                    <p class="text-dark font-weight-bold mb-0 text-success">RM
                                        {{ number_format($transaction->amount, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item mb-3">
                                    <small class="text-uppercase text-secondary text-xs font-weight-bold">Service</small>
                                    <p class="text-dark font-weight-bold mb-0">
                                        {{ $transaction->qrCode->service->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if ($transaction->meal_details)
                                <div class="col-12">
                                    <div class="info-item mb-3">
                                        <small class="text-uppercase text-secondary text-xs font-weight-bold">Meal
                                            Details</small>
                                        <p class="text-dark mb-0">{{ $transaction->meal_details }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Information -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Student Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if ($transaction->student && $transaction->student->user && $transaction->student->user->profile_picture_url)
                                <img src="{{ $transaction->student->user->profile_picture_url }}" alt="student profile"
                                    class="avatar avatar-lg me-3">
                            @else
                                <div class="avatar avatar-lg bg-gradient-secondary me-3">
                                    <i class="ni ni-single-02"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0">{{ $transaction->student->user->name ?? 'N/A' }}</h6>
                                <p class="text-sm text-secondary mb-0">{{ $transaction->student->matrix_no ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="info-item mb-3">
                            <small class="text-uppercase text-secondary text-xs font-weight-bold">Email</small>
                            <p class="text-dark mb-0">{{ $transaction->student->user->email ?? 'N/A' }}</p>
                        </div>

                        <div class="info-item mb-3">
                            <small class="text-uppercase text-secondary text-xs font-weight-bold">Phone</small>
                            <p class="text-dark mb-0">{{ $transaction->student->user->phone_number ?? 'N/A' }}</p>
                        </div>

                        @if ($transaction->student && $transaction->student->user && $transaction->student->user->location)
                            <div class="info-item mb-3">
                                <small class="text-uppercase text-secondary text-xs font-weight-bold">Location</small>
                                <p class="text-dark mb-0">{{ $transaction->student->user->location }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Information -->
        @if ($transaction->qrCode)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>QR Code Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-item mb-3">
                                        <small class="text-uppercase text-secondary text-xs font-weight-bold">QR Code
                                            ID</small>
                                        <p class="text-dark font-weight-bold mb-0">{{ $transaction->qrCode->code }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item mb-3">
                                        <small class="text-uppercase text-secondary text-xs font-weight-bold">Generated
                                            Date</small>
                                        <p class="text-dark mb-0">
                                            {{ $transaction->qrCode->generated_date->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item mb-3">
                                        <small class="text-uppercase text-secondary text-xs font-weight-bold">Expiry
                                            Date</small>
                                        <p class="text-dark mb-0">
                                            {{ $transaction->qrCode->expiry_date->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item mb-3">
                                        <small class="text-uppercase text-secondary text-xs font-weight-bold">QR
                                            Status</small>
                                        <div>
                                            @if ($transaction->qrCode->status == 'ACTIVE')
                                                <span class="badge badge-sm bg-gradient-success">Active</span>
                                            @elseif($transaction->qrCode->status == 'USED')
                                                <span class="badge badge-sm bg-gradient-info">Used</span>
                                            @elseif($transaction->qrCode->status == 'EXPIRED')
                                                <span class="badge badge-sm bg-gradient-secondary">Expired</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Service Details -->
        @if ($transaction->qrCode && $transaction->qrCode->service)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Service Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-item mb-3">
                                        <small class="text-uppercase text-secondary text-xs font-weight-bold">Service
                                            Name</small>
                                        <p class="text-dark font-weight-bold mb-0">
                                            {{ $transaction->qrCode->service->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item mb-3">
                                        <small
                                            class="text-uppercase text-secondary text-xs font-weight-bold">Category</small>
                                        <p class="text-dark mb-0">{{ $transaction->qrCode->service->category }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item mb-3">
                                        <small class="text-uppercase text-secondary text-xs font-weight-bold">Price</small>
                                        <p class="text-dark mb-0">RM
                                            {{ number_format($transaction->qrCode->service->price, 2) }}</p>
                                    </div>
                                </div>
                                @if ($transaction->qrCode->service->description)
                                    <div class="col-12">
                                        <div class="info-item mb-3">
                                            <small
                                                class="text-uppercase text-secondary text-xs font-weight-bold">Description</small>
                                            <p class="text-dark mb-0">{{ $transaction->qrCode->service->description }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .info-item {
            padding: 0.5rem;
            border-left: 3px solid #e9ecef;
            margin-bottom: 1rem;
        }

        .info-item:hover {
            border-left-color: #5e72e4;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
    </style>
@endsection
