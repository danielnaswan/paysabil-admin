@extends('layouts.user_type.auth')
{{-- {{dd($qrCode)}} --}}

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Edit QR Code</h6>
                            <div class="ms-auto">
                                <span class="badge badge-lg {{ $qrCode->status_badge_class }}">
                                    {{ $qrCode->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-4 p-3">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <span class="alert-text text-white">{{ $errors->first() }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('qrcode.update', $qrCode->id) }}" method="POST" role="form">
                            @csrf
                            @method('PUT')

                            <!-- Read-only Service Information -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Service Information
                                        (Read Only)</h6>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">
                                            <i class="fas fa-store me-1"></i>Vendor
                                        </label>
                                        <input class="form-control" type="text"
                                            value="{{ $qrCode->vendor->business_name }} ({{ $qrCode->vendor->service_category }})"
                                            disabled>
                                        <small class="form-text text-muted">Cannot be changed after QR code creation</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">
                                            <i class="fas fa-utensils me-1"></i>Service/Menu
                                        </label>
                                        <input class="form-control" type="text"
                                            value="{{ $qrCode->service->name }} - RM{{ number_format($qrCode->service->price, 2) }}"
                                            disabled>
                                        <small class="form-text text-muted">{{ $qrCode->service->category }} •
                                            {{ $qrCode->service->preparation_time }} minutes</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">
                                            <i class="fas fa-calendar me-1"></i>Generated Date
                                        </label>
                                        <input class="form-control" type="text"
                                            value="{{ $qrCode->getFormattedGeneratedDate() }}" disabled>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">
                                            <i class="fas fa-barcode me-1"></i>QR Code
                                        </label>
                                        <input class="form-control" type="text" value="{{ $qrCode->code }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <hr class="horizontal dark">

                            <!-- Editable Fields -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Editable Settings
                                    </h6>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-control-label">
                                            <i class="fas fa-toggle-on me-1"></i>Status *
                                        </label>
                                        <select class="form-control" id="status" name="status" required
                                            onchange="handleStatusChange()">
                                            <option value="ACTIVE" {{ $qrCode->status === 'ACTIVE' ? 'selected' : '' }}>
                                                Active - Can be scanned
                                            </option>
                                            <option value="EXPIRED" {{ $qrCode->status === 'EXPIRED' ? 'selected' : '' }}>
                                                Expired - Cannot be scanned
                                            </option>
                                            <option value="USED" {{ $qrCode->status === 'USED' ? 'selected' : '' }}>
                                                Used - Already redeemed
                                            </option>
                                            <option value="INVALID" {{ $qrCode->status === 'INVALID' ? 'selected' : '' }}>
                                                Invalid - Permanently disabled
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">
                                            Current status: <span
                                                class="badge {{ $qrCode->status_badge_class }}">{{ $qrCode->status }}</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expiry-hours" class="form-control-label">
                                            <i class="fas fa-clock me-1"></i>Validity Period (Hours from Generation) *
                                        </label>
                                        <select class="form-control" id="expiry-hours" name="expiry_hours" required>
                                            <option value="1"
                                                {{ $qrCode->getLifetimeInHours() == 1 ? 'selected' : '' }}>1 Hour</option>
                                            <option value="6"
                                                {{ $qrCode->getLifetimeInHours() == 6 ? 'selected' : '' }}>6 Hours</option>
                                            <option value="12"
                                                {{ $qrCode->getLifetimeInHours() == 12 ? 'selected' : '' }}>12 Hours
                                            </option>
                                            <option value="24"
                                                {{ $qrCode->getLifetimeInHours() == 24 ? 'selected' : '' }}>1 Day (24
                                                Hours)</option>
                                            <option value="48"
                                                {{ $qrCode->getLifetimeInHours() == 48 ? 'selected' : '' }}>2 Days (48
                                                Hours)</option>
                                            <option value="72"
                                                {{ $qrCode->getLifetimeInHours() == 72 ? 'selected' : '' }}>3 Days (72
                                                Hours)</option>
                                            <option value="168"
                                                {{ $qrCode->getLifetimeInHours() == 168 ? 'selected' : '' }}>1 Week (168
                                                Hours)</option>
                                        </select>
                                        <small class="form-text text-muted">
                                            Current expiry: <span
                                                class="text-{{ $qrCode->is_expired ? 'danger' : 'success' }}">
                                                {{ $qrCode->getFormattedExpiryDate() }}
                                            </span>
                                            @if ($qrCode->is_expired)
                                                <span class="text-danger">(Expired)</span>
                                            @else
                                                <span class="text-info">({{ $qrCode->time_remaining }})</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Warning Messages -->
                            <div id="status-warnings" class="mt-3"></div>

                            <!-- Usage Information -->
                            @if ($qrCode->transactions()->exists())
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="alert alert-info text-white">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-info-circle me-1"></i>Usage Information
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <strong>Total Scans:</strong> {{ $qrCode->transactions()->count() }}
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Successful:</strong>
                                                    {{ $qrCode->transactions()->where('status', 'COMPLETED')->count() }}
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Revenue:</strong> RM
                                                    {{ number_format($qrCode->transactions()->where('status', 'COMPLETED')->sum('amount'), 2) }}
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Last Scan:</strong>
                                                    {{ $qrCode->transactions()->latest()->first()?->transaction_date->format('d M Y, H:i') ?? 'Never' }}
                                                </div>
                                            </div>
                                            <hr>
                                            <small class="text-muted">
                                                <i class="fas fa-exclamation-triangle me-1 text-white"></i>
                                                <span class="text-white">Changing status may affect ongoing transactions
                                                    and user experience.</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('qrcode.show', $qrCode->id) }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <a href="{{ route('qrcode.index') }}" class="btn btn-light me-2">
                                    <i class="fas fa-arrow-left me-1"></i>Back to List
                                </a>
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="fas fa-save me-1"></i>Update QR Code
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Current QR Code Preview -->
                <div class="card">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Current QR Code</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="qr-preview-container p-3 bg-light rounded">
                            <img src="{{ route('qrcode.image', $qrCode) }}" alt="QR Code Preview" class="img-fluid"
                                style="max-width: 200px;">
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('qrcode.download', $qrCode) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                            <button onclick="printPreview()" class="btn btn-sm btn-info">
                                <i class="fas fa-print me-1"></i>Print
                            </button>
                        </div>
                        <p class="text-xs text-muted mt-2 mb-0">
                            Scan this QR code to test functionality
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if ($qrCode->status === 'ACTIVE' && !$qrCode->is_expired)
                                <button type="button" class="btn btn-warning btn-sm" onclick="expireNow()">
                                    <i class="fas fa-clock me-1"></i>Expire Now
                                </button>
                            @endif

                            @if ($qrCode->status === 'EXPIRED' && !$qrCode->transactions()->where('status', 'COMPLETED')->exists())
                                <button type="button" class="btn btn-success btn-sm" onclick="reactivate()">
                                    <i class="fas fa-play me-1"></i>Reactivate
                                </button>
                            @endif

                            @if ($qrCode->status !== 'INVALID')
                                <button type="button" class="btn btn-danger btn-sm" onclick="invalidate()">
                                    <i class="fas fa-ban me-1"></i>Mark as Invalid
                                </button>
                            @endif

                            <hr class="horizontal dark">

                            <a href="{{ route('vendor.show', $qrCode->vendor_id) }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-store me-1"></i>View Vendor
                            </a>

                            <a href="{{ route('services.show', $qrCode->service_id) }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-utensils me-1"></i>View Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function handleStatusChange() {
                const status = document.getElementById('status').value;
                const warningsDiv = document.getElementById('status-warnings');

                warningsDiv.innerHTML = '';

                switch (status) {
                    case 'EXPIRED':
                        warningsDiv.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This QR code will no longer be scannable once marked as expired.
                </div>
            `;
                        break;
                    case 'USED':
                        warningsDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Info:</strong> Marking as "Used" indicates this QR code has been successfully redeemed.
                </div>
            `;
                        break;
                    case 'INVALID':
                        warningsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-ban me-2"></i>
                    <strong>Danger:</strong> This will permanently disable the QR code. This action should only be used for security reasons.
                </div>
            `;
                        break;
                    case 'ACTIVE':
                        if ({{ $qrCode->is_expired ? 'true' : 'false' }}) {
                            warningsDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Note:</strong> This QR code has already expired. You may need to extend the validity period.
                    </div>
                `;
                        }
                        break;
                }
            }

            function expireNow() {
                if (confirm('Are you sure you want to expire this QR code immediately?')) {
                    document.getElementById('status').value = 'EXPIRED';
                    handleStatusChange();
                }
            }

            function reactivate() {
                if (confirm('Are you sure you want to reactivate this QR code?')) {
                    document.getElementById('status').value = 'ACTIVE';
                    document.getElementById('expiry-hours').value = '24'; // Reset to 24 hours
                    handleStatusChange();
                }
            }

            function invalidate() {
                if (confirm(
                        'Are you sure you want to mark this QR code as invalid? This action indicates a security issue and cannot be undone easily.'
                        )) {
                    document.getElementById('status').value = 'INVALID';
                    handleStatusChange();
                }
            }

            function printPreview() {
                const printWindow = window.open('', '', 'width=600,height=600');
                const qrCodeSrc = '{{ route('qrcode.image', $qrCode) }}';

                printWindow.document.write(`
        <html>
            <head>
                <title>QR Code Preview - {{ $qrCode->service->name }}</title>
                <style>
                    body {
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    .preview-container {
                        text-align: center;
                        border: 2px solid #000;
                        padding: 20px;
                        border-radius: 8px;
                    }
                    img { max-width: 250px; margin: 10px 0; }
                    h3 { margin: 10px 0; }
                    p { margin: 5px 0; font-size: 14px; }
                </style>
            </head>
            <body>
                <div class="preview-container">
                    <h3>{{ $qrCode->vendor->business_name }}</h3>
                    <p>{{ $qrCode->service->name }}</p>
                    <img src="${qrCodeSrc}" alt="QR Code" />
                    <p>RM {{ number_format($qrCode->service->price, 2) }}</p>
                    <p>Valid until: {{ $qrCode->getFormattedExpiryDate() }}</p>
                    <p style="font-size: 12px;">Code: {{ $qrCode->code }}</p>
                </div>
            </body>
        </html>
    `);

                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            }

            // Initialize status change handler
            document.addEventListener('DOMContentLoaded', function() {
                handleStatusChange();

                // Validate form before submission
                document.querySelector('form').addEventListener('submit', function(e) {
                    const status = document.getElementById('status').value;
                    if (status === 'INVALID') {
                        if (!confirm(
                                'You are about to mark this QR code as INVALID. This is typically used for security issues. Are you absolutely sure?'
                                )) {
                            e.preventDefault();
                            return false;
                        }
                    }

                    // Show loading state
                    const submitBtn = document.getElementById('submit-btn');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
                });
            });
        </script>
    @endpush
@endsection
