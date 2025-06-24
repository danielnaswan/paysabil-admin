@extends('layouts.user_type.vendor')

@section('page-title', 'QR Code Details')
@section('page-heading', 'QR Code #' . $qrCode->id)

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            {{-- QR Code Information --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h6>QR Code Information</h6>
                            <div class="d-flex">
                                @if ($qrCode->status === 'ACTIVE')
                                    <a href="{{ route('vendor.qrcodes.edit', $qrCode->id) }}"
                                        class="btn btn-outline-primary btn-sm me-2">
                                        <i class="fas fa-edit me-1"></i>Edit Expiry
                                    </a>
                                @endif
                                <a href="{{ route('vendor.qrcodes.download', $qrCode->id) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="fas fa-download me-1"></i>Download QR
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">QR Code</label>
                                    <input class="form-control font-family-monospace" type="text"
                                        value="{{ $qrCode->code }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Status</label>
                                    <div>
                                        @php
                                            $statusClass =
                                                [
                                                    'ACTIVE' => 'success',
                                                    'EXPIRED' => 'warning',
                                                    'USED' => 'info',
                                                    'INVALID' => 'danger',
                                                ][$qrCode->status] ?? 'secondary';
                                        @endphp
                                        <span
                                            class="badge bg-gradient-{{ $statusClass }} px-3 py-2">{{ $qrCode->status }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Generated Date</label>
                                    <input class="form-control" type="text"
                                        value="{{ $qrCode->generated_date->format('M j, Y \a\t H:i') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Expiry Date</label>
                                    <input class="form-control {{ $qrCode->expiry_date->isPast() ? 'text-danger' : '' }}"
                                        type="text" value="{{ $qrCode->expiry_date->format('M j, Y \a\t H:i') }}"
                                        readonly>
                                    @if ($qrCode->expiry_date->isPast())
                                        <small class="text-danger">This QR code has expired</small>
                                    @else
                                        <small class="text-success">Expires in
                                            {{ $qrCode->expiry_date->diffForHumans() }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Service Details --}}
                        <hr class="horizontal dark">
                        <h6 class="text-uppercase text-sm">Service Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Service Name</label>
                                    <input class="form-control" type="text" value="{{ $qrCode->service->name }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Price</label>
                                    <input class="form-control" type="text"
                                        value="RM {{ number_format($qrCode->service->price, 2) }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Category</label>
                                    <input class="form-control" type="text" value="{{ $qrCode->service->category }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Preparation Time</label>
                                    <input class="form-control" type="text"
                                        value="{{ $qrCode->service->preparation_time }} minutes" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Description</label>
                                    <textarea class="form-control" rows="3" readonly>{{ $qrCode->service->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Transaction History --}}
                @if ($qrCode->transactions->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header pb-0">
                            <h6>Transaction History</h6>
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
                                                Date</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Amount</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($qrCode->transactions as $transaction)
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
                                                            <h6 class="mb-0 text-sm">
                                                                {{ $transaction->student->user->name }}</h6>
                                                            <p class="text-xs text-secondary mb-0">
                                                                {{ $transaction->student->matrix_no }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $transaction->transaction_date->format('M j, Y') }}</p>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ $transaction->transaction_date->format('H:i') }}</p>
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- QR Code Image & Actions --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>QR Code Image</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img src="{{ route('vendor.qrcodes.image', $qrCode->id) }}" alt="QR Code"
                                class="img-fluid border rounded" style="max-width: 250px;">
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('vendor.qrcodes.image', $qrCode->id) }}" target="_blank"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>View Full Size
                            </a>
                            <a href="{{ route('vendor.qrcodes.download', $qrCode->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-download me-1"></i>Download PNG
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Statistics --}}
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6>Usage Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Total Scans:</span>
                            <span class="text-sm font-weight-bold">{{ $qrCode->transactions->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Completed Orders:</span>
                            <span
                                class="text-sm font-weight-bold">{{ $qrCode->transactions->where('status', 'COMPLETED')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Total Revenue:</span>
                            <span class="text-sm font-weight-bold">RM
                                {{ number_format($qrCode->transactions->where('status', 'COMPLETED')->sum('amount'), 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-sm">Success Rate:</span>
                            <span class="text-sm font-weight-bold">
                                @if ($qrCode->transactions->count() > 0)
                                    {{ round(($qrCode->transactions->where('status', 'COMPLETED')->count() / $qrCode->transactions->count()) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6>Actions</h6>
                    </div>
                    <div class="card-body pt-3">
                        @if ($qrCode->status === 'ACTIVE')
                            <a href="{{ route('vendor.qrcodes.edit', $qrCode->id) }}"
                                class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-edit me-2"></i>Edit Expiry Time
                            </a>
                            <form action="{{ route('vendor.qrcodes.destroy', $qrCode->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100 mb-2"
                                    onclick="return confirm('Are you sure you want to expire this QR code?')">
                                    <i class="fas fa-times me-2"></i>Expire QR Code
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('vendor.qrcodes.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to QR Codes
                        </a>

                        <a href="{{ route('vendor.qrcodes.create') }}?service_id={{ $qrCode->service->id }}"
                            class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>Generate Another
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
