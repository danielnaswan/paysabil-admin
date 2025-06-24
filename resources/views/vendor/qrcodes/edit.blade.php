@extends('layouts.user_type.vendor')

@section('page-title', 'Edit QR Code')
@section('page-heading', 'Edit QR Code Expiry')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <form method="POST" action="{{ route('vendor.qrcodes.update', $qrCode->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <p class="mb-0">Edit QR Code Expiry</p>
                                <button type="submit" class="btn btn-primary btn-sm ms-auto">Update QR Code</button>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Current QR Code Info --}}
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">QR Code: {{ $qrCode->code }}</h6>
                                        <p class="mb-1">Service: <strong>{{ $qrCode->service->name }}</strong></p>
                                        <p class="mb-0">Current Status:
                                            @php
                                                $statusClass =
                                                    [
                                                        'ACTIVE' => 'success',
                                                        'EXPIRED' => 'warning',
                                                        'USED' => 'info',
                                                        'INVALID' => 'danger',
                                                    ][$qrCode->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-gradient-{{ $statusClass }}">{{ $qrCode->status }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <small class="text-muted">Generated:
                                            {{ $qrCode->generated_date->format('M j, Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>

                            <p class="text-uppercase text-sm">Update Expiry Time</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="current_expiry" class="form-control-label">Current Expiry Date</label>
                                        <input
                                            class="form-control {{ $qrCode->expiry_date->isPast() ? 'text-danger' : 'text-success' }}"
                                            type="text" id="current_expiry"
                                            value="{{ $qrCode->expiry_date->format('M j, Y \a\t H:i') }}" readonly>
                                        @if ($qrCode->expiry_date->isPast())
                                            <small class="text-danger">This QR code has already expired</small>
                                        @else
                                            <small class="text-success">Expires in
                                                {{ $qrCode->expiry_date->diffForHumans() }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expiry_hours" class="form-control-label">New Expiry Time (Hours from
                                            now) *</label>
                                        <select class="form-control @error('expiry_hours') is-invalid @enderror"
                                            name="expiry_hours" id="expiry_hours" required>
                                            <option value="">Select new expiry time...</option>
                                            <option value="1" {{ old('expiry_hours') == '1' ? 'selected' : '' }}>1 Hour
                                            </option>
                                            <option value="2" {{ old('expiry_hours') == '2' ? 'selected' : '' }}>2
                                                Hours</option>
                                            <option value="4" {{ old('expiry_hours') == '4' ? 'selected' : '' }}>4
                                                Hours</option>
                                            <option value="8" {{ old('expiry_hours') == '8' ? 'selected' : '' }}>8
                                                Hours</option>
                                            <option value="12" {{ old('expiry_hours') == '12' ? 'selected' : '' }}>12
                                                Hours</option>
                                            <option value="24" {{ old('expiry_hours') == '24' ? 'selected' : '' }}>1
                                                Day</option>
                                            <option value="48" {{ old('expiry_hours') == '48' ? 'selected' : '' }}>2
                                                Days</option>
                                            <option value="72" {{ old('expiry_hours') == '72' ? 'selected' : '' }}>3
                                                Days</option>
                                            <option value="168" {{ old('expiry_hours') == '168' ? 'selected' : '' }}>1
                                                Week</option>
                                        </select>
                                        @error('expiry_hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">The QR code will expire after the selected time from
                                            now</small>
                                    </div>
                                </div>
                            </div>

                            {{-- New Expiry Preview --}}
                            <div id="expiry-preview" class="d-none">
                                <hr class="horizontal dark">
                                <div class="alert alert-success">
                                    <h6 class="mb-1">New Expiry Date Preview</h6>
                                    <p class="mb-0">QR code will expire on: <strong id="preview-expiry-date">-</strong>
                                    </p>
                                </div>
                            </div>

                            {{-- Service Details (Read-only) --}}
                            <hr class="horizontal dark">
                            <p class="text-uppercase text-sm">Service Information (Read-only)</p>
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
                        </div>
                    </form>
                </div>
            </div>

            {{-- Information Panel --}}
            <div class="col-md-4">
                {{-- QR Code Image --}}
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>QR Code Image</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img src="{{ route('vendor.qrcodes.image', $qrCode->id) }}" alt="QR Code"
                                class="img-fluid border rounded" style="max-width: 200px;">
                        </div>
                    </div>
                </div>

                {{-- Update Information --}}
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6>Update Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-warning shadow border-radius-md me-3">
                                <i class="ni ni-time-alarm text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Expiry Extension</h6>
                                <p class="text-sm mb-0">You can only extend the expiry time. The new expiry will be
                                    calculated from the current time.</p>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-info shadow border-radius-md me-3">
                                <i class="ni ni-bulb-61 text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Active Status Required</h6>
                                <p class="text-sm mb-0">Only active QR codes can be edited. Expired or used QR codes cannot
                                    be modified.</p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="icon icon-shape bg-gradient-success shadow border-radius-md me-3">
                                <i class="ni ni-check-bold text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Immediate Effect</h6>
                                <p class="text-sm mb-0">Changes take effect immediately after saving. The QR code will
                                    remain usable until the new expiry time.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6>Quick Actions</h6>
                    </div>
                    <div class="card-body pt-3">
                        <a href="{{ route('vendor.qrcodes.show', $qrCode->id) }}"
                            class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-eye me-2"></i>View QR Details
                        </a>
                        <a href="{{ route('vendor.qrcodes.download', $qrCode->id) }}"
                            class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-download me-2"></i>Download QR Code
                        </a>
                        <a href="{{ route('vendor.qrcodes.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Back to QR Codes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const expirySelect = document.getElementById('expiry_hours');
                const expiryPreview = document.getElementById('expiry-preview');
                const previewDate = document.getElementById('preview-expiry-date');

                expirySelect.addEventListener('change', function() {
                    if (this.value) {
                        const hours = parseInt(this.value);
                        const now = new Date();
                        const expiryDate = new Date(now.getTime() + (hours * 60 * 60 * 1000));

                        const options = {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        };

                        previewDate.textContent = expiryDate.toLocaleDateString('en-US', options);
                        expiryPreview.classList.remove('d-none');
                    } else {
                        expiryPreview.classList.add('d-none');
                    }
                });
            });
        </script>
    @endpush
@endsection
