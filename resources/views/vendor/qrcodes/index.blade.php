@extends('layouts.user_type.vendor')

@section('page-title', 'QR Codes')
@section('page-heading', 'QR Code Management')

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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total QR Codes</p>
                                    <h5 class="font-weight-bolder mb-0">{{ $stats['total_qr_codes'] }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-image text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Active QR Codes</p>
                                    <h5 class="font-weight-bolder mb-0">{{ $stats['active_qr_codes'] }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="ni ni-check-bold text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Expired</p>
                                    <h5 class="font-weight-bolder mb-0">{{ $stats['expired_qr_codes'] }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="ni ni-time-alarm text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Used</p>
                                    <h5 class="font-weight-bolder mb-0">{{ $stats['used_qr_codes'] }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-basket text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- QR Codes Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">QR Codes</h5>
                            </div>
                            <div class="d-flex">
                                <a href="{{ route('vendor.qrcodes.create') }}" class="btn btn-primary btn-sm me-2">
                                    <i class="fas fa-plus me-1"></i>Generate QR Code
                                </a>
                                <button class="btn btn-outline-danger btn-sm" onclick="bulkExpire()">
                                    <i class="fas fa-times me-1"></i>Bulk Expire
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">QR
                                            Code</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Service</th>
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
                                    @forelse($qrCodes as $qrCode)
                                        <tr>
                                            <td class="ps-4">
                                                @if ($qrCode->status === 'ACTIVE')
                                                    <input type="checkbox" name="qr_code_ids[]" value="{{ $qrCode->id }}"
                                                        class="form-check-input qr-checkbox">
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm font-family-monospace">{{ $qrCode->code }}
                                                        </h6>
                                                        <p class="text-xs text-secondary mb-0">ID: {{ $qrCode->id }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $qrCode->service->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">RM
                                                            {{ number_format($qrCode->service->price, 2) }}</p>
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
                                                <div class="dropdown">
                                                    <button class="btn btn-link text-secondary mb-0" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('vendor.qrcodes.show', $qrCode->id) }}">
                                                                <i class="fas fa-eye me-2"></i>View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('vendor.qrcodes.image', $qrCode->id) }}"
                                                                target="_blank">
                                                                <i class="fas fa-image me-2"></i>View QR Image
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('vendor.qrcodes.download', $qrCode->id) }}">
                                                                <i class="fas fa-download me-2"></i>Download QR
                                                            </a>
                                                        </li>
                                                        @if ($qrCode->status === 'ACTIVE')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('vendor.qrcodes.edit', $qrCode->id) }}">
                                                                    <i class="fas fa-edit me-2"></i>Edit Expiry
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <form
                                                                    action="{{ route('vendor.qrcodes.destroy', $qrCode->id) }}"
                                                                    method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="dropdown-item text-warning"
                                                                        onclick="return confirm('Are you sure you want to expire this QR code?')">
                                                                        <i class="fas fa-times me-2"></i>Expire QR Code
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="ni ni-image text-6xl text-muted mb-3"></i>
                                                    <h6 class="text-muted mb-2">No QR codes generated yet</h6>
                                                    <p class="text-sm text-muted mb-3">Generate QR codes for your services
                                                        to start accepting orders</p>
                                                    <a href="{{ route('vendor.qrcodes.create') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fas fa-plus me-1"></i>Generate Your First QR Code
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if ($qrCodes->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $qrCodes->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Expire Form --}}
    <form id="bulkExpireForm" action="{{ route('vendor.qrcodes.bulk-expire') }}" method="POST" style="display: none;">
        @csrf
        <div id="selectedQrCodes"></div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Select All functionality
                const selectAllCheckbox = document.getElementById('selectAll');
                const qrCheckboxes = document.querySelectorAll('.qr-checkbox');

                selectAllCheckbox.addEventListener('change', function() {
                    qrCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                // Update Select All based on individual checkboxes
                qrCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const checkedCount = document.querySelectorAll('.qr-checkbox:checked').length;
                        selectAllCheckbox.checked = checkedCount === qrCheckboxes.length;
                        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount <
                            qrCheckboxes.length;
                    });
                });
            });

            function bulkExpire() {
                const selectedCheckboxes = document.querySelectorAll('.qr-checkbox:checked');

                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one QR code to expire.');
                    return;
                }

                if (!confirm(`Are you sure you want to expire ${selectedCheckboxes.length} QR code(s)?`)) {
                    return;
                }

                const form = document.getElementById('bulkExpireForm');
                const container = document.getElementById('selectedQrCodes');
                container.innerHTML = '';

                selectedCheckboxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'qr_code_ids[]';
                    input.value = checkbox.value;
                    container.appendChild(input);
                });

                form.submit();
            }
        </script>
    @endpush
@endsection
