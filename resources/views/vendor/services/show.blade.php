@extends('layouts.user_type.vendor')

@section('page-title', 'Service Details')
@section('page-heading', $service->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            {{-- Service Information --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h6>Service Information</h6>
                            <div class="d-flex">
                                <a href="{{ route('vendor.services.edit', $service->id) }}"
                                    class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fas fa-edit me-1"></i>Edit Service
                                </a>
                                <form method="POST"
                                    action="{{ route('vendor.services.toggle-availability', $service->id) }}"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-outline-{{ $service->is_available ? 'warning' : 'success' }} btn-sm">
                                        <i class="fas fa-{{ $service->is_available ? 'pause' : 'play' }} me-1"></i>
                                        {{ $service->is_available ? 'Mark Unavailable' : 'Mark Available' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Service Name</label>
                                    <input class="form-control" type="text" value="{{ $service->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Category</label>
                                    <input class="form-control" type="text" value="{{ $service->category }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Price</label>
                                    <input class="form-control" type="text"
                                        value="RM {{ number_format($service->price, 2) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Preparation Time</label>
                                    <input class="form-control" type="text"
                                        value="{{ $service->preparation_time }} minutes" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Status</label>
                                    <div>
                                        <span
                                            class="badge bg-gradient-{{ $service->is_available ? 'success' : 'secondary' }} px-3 py-2">
                                            {{ $service->is_available ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Description</label>
                                    <textarea class="form-control" rows="4" readonly>{{ $service->description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Created Date</label>
                                    <input class="form-control" type="text"
                                        value="{{ $service->created_at->format('M j, Y \a\t H:i') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Last Updated</label>
                                    <input class="form-control" type="text"
                                        value="{{ $service->updated_at->format('M j, Y \a\t H:i') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- QR Codes for this Service --}}
                @if ($service->qrCodes->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h6>QR Codes for this Service</h6>
                                <a href="{{ route('vendor.qrcodes.create') }}?service_id={{ $service->id }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Generate New QR
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                QR Code</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Generated</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Expires</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($service->qrCodes->take(5) as $qrCode)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm font-family-monospace">
                                                                {{ $qrCode->code }}</h6>
                                                            <p class="text-xs text-secondary mb-0">ID: {{ $qrCode->id }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-xs font-weight-bold">{{ $qrCode->generated_date->format('M j, Y') }}</span>
                                                    <br>
                                                    <span
                                                        class="text-xs text-secondary">{{ $qrCode->generated_date->format('H:i') }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-xs font-weight-bold {{ $qrCode->expiry_date->isPast() ? 'text-danger' : '' }}">
                                                        {{ $qrCode->expiry_date->format('M j, Y') }}
                                                    </span>
                                                    <br>
                                                    <span
                                                        class="text-xs text-secondary">{{ $qrCode->expiry_date->format('H:i') }}</span>
                                                </td>
                                                <td class="align-middle text-center">
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
                                                        class="badge badge-sm bg-gradient-{{ $statusClass }}">{{ $qrCode->status }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="{{ route('vendor.qrcodes.show', $qrCode->id) }}"
                                                        class="text-secondary font-weight-bold text-xs me-2"
                                                        data-toggle="tooltip" data-original-title="View QR code">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('vendor.qrcodes.download', $qrCode->id) }}"
                                                        class="text-primary font-weight-bold text-xs" data-toggle="tooltip"
                                                        data-original-title="Download QR code">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($service->qrCodes->count() > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ route('vendor.qrcodes.index') }}" class="btn btn-outline-primary btn-sm">
                                        View All QR Codes ({{ $service->qrCodes->count() }} total)
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Recent Transactions --}}
                @if ($recentTransactions->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header pb-0">
                            <h6>Recent Transactions</h6>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Customer</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
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
                                        @foreach ($recentTransactions as $transaction)
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
                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-xs font-weight-bold">{{ $transaction->transaction_date->format('M j, Y') }}</span>
                                                    <br>
                                                    <span
                                                        class="text-xs text-secondary">{{ $transaction->transaction_date->format('H:i') }}</span>
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

            {{-- Statistics & Actions --}}
            <div class="col-md-4">
                {{-- Service Statistics --}}
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Service Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Total Orders:</span>
                            <span class="text-sm font-weight-bold">{{ number_format($service->total_orders ?? 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Total Revenue:</span>
                            <span class="text-sm font-weight-bold">RM
                                {{ number_format($service->total_revenue ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Active QR Codes:</span>
                            <span
                                class="text-sm font-weight-bold">{{ $service->qrCodes->where('status', 'ACTIVE')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Total QR Codes:</span>
                            <span class="text-sm font-weight-bold">{{ $service->qrCodes->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-sm">Average Order Value:</span>
                            <span class="text-sm font-weight-bold">
                                @if ($service->total_orders > 0)
                                    RM {{ number_format(($service->total_revenue ?? 0) / $service->total_orders, 2) }}
                                @else
                                    RM 0.00
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6>Quick Actions</h6>
                    </div>
                    <div class="card-body pt-3">
                        <a href="{{ route('vendor.services.edit', $service->id) }}"
                            class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-edit me-2"></i>Edit Service
                        </a>
                        <a href="{{ route('vendor.qrcodes.create') }}?service_id={{ $service->id }}"
                            class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-qrcode me-2"></i>Generate QR Code
                        </a>
                        <form method="POST" action="{{ route('vendor.services.toggle-availability', $service->id) }}"
                            class="mb-2">
                            @csrf
                            <button type="submit"
                                class="btn btn-outline-{{ $service->is_available ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-{{ $service->is_available ? 'pause' : 'play' }} me-2"></i>
                                {{ $service->is_available ? 'Mark Unavailable' : 'Mark Available' }}
                            </button>
                        </form>
                        <a href="{{ route('vendor.services.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Back to Services
                        </a>
                    </div>
                </div>

                {{-- Service Performance --}}
                @if ($service->total_orders > 0)
                    <div class="card mt-4">
                        <div class="card-header pb-0">
                            <h6>Performance Insights</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $performanceScore = 0;
                                if ($service->total_orders > 0) {
                                    $performanceScore += min(($service->total_orders / 10) * 25, 25); // Max 25 points for orders
                                    $performanceScore += min(($service->total_revenue / 100) * 25, 25); // Max 25 points for revenue
                                    $performanceScore += $service->is_available ? 25 : 0; // 25 points if available
                                    $performanceScore +=
                                        $service->qrCodes->where('status', 'ACTIVE')->count() > 0 ? 25 : 0; // 25 points if has active QR
                                }
                                $performanceScore = round($performanceScore);
                            @endphp

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-sm">Performance Score</span>
                                    <span class="text-sm font-weight-bold">{{ $performanceScore }}%</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-gradient-{{ $performanceScore >= 75 ? 'success' : ($performanceScore >= 50 ? 'warning' : 'danger') }}"
                                        role="progressbar" style="width: {{ $performanceScore }}%"></div>
                                </div>
                                <small class="text-muted">
                                    @if ($performanceScore >= 75)
                                        Excellent performance! Keep it up.
                                    @elseif($performanceScore >= 50)
                                        Good performance. Consider generating more QR codes.
                                    @else
                                        Consider promoting this service or adjusting pricing.
                                    @endif
                                </small>
                            </div>

                            @if ($service->total_orders >= 5)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-thumbs-up text-success me-2"></i>
                                    <span class="text-sm">Popular service with {{ $service->total_orders }} orders</span>
                                </div>
                            @endif

                            @if ($service->is_available)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span class="text-sm">Service is currently available</span>
                                </div>
                            @else
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-pause-circle text-warning me-2"></i>
                                    <span class="text-sm">Service is temporarily unavailable</span>
                                </div>
                            @endif

                            @if ($service->qrCodes->where('status', 'ACTIVE')->count() > 0)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-qrcode text-info me-2"></i>
                                    <span class="text-sm">{{ $service->qrCodes->where('status', 'ACTIVE')->count() }}
                                        active QR code(s)</span>
                                </div>
                            @else
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    <span class="text-sm">No active QR codes - generate one to accept orders</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Danger Zone --}}
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6 class="text-danger">Danger Zone</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <small>
                                <strong>Warning:</strong> Deleting a service cannot be undone. Make sure you no longer need
                                this service.
                            </small>
                        </div>

                        <form action="{{ route('vendor.services.destroy', $service->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fas fa-trash me-2"></i>Delete Service
                            </button>
                        </form>
                        <small class="text-muted d-block mt-2">
                            Note: Services with active QR codes or recent transactions cannot be deleted.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
