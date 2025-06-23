@extends('layouts.user_type.auth')

@section('content')
    <div>
        @if (session('success'))
            <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                <span class="alert-text text-white">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="m-3 alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-text text-white">{{ $errors->first() }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total QR Codes</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $statistics['total'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-mobile-button text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Active</p>
                                    <h5 class="font-weight-bolder mb-0 text-success">
                                        {{ $statistics['active'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
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
                                    <h5 class="font-weight-bolder mb-0 text-warning">
                                        {{ $statistics['expired'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="ni ni-time-alarm text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Expiring Soon</p>
                                    <h5 class="font-weight-bolder mb-0 text-danger">
                                        {{ $statistics['expiring_soon'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                    <i class="ni ni-bell-55 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">QR Code Management</h5>
                                <p class="text-sm mb-0">Manage and track QR code validity and status</p>
                            </div>
                            <div class="d-flex">
                                <a href="{{ route('qrcode.create') }}" class="btn bg-gradient-primary btn-sm mb-0 me-2"
                                    type="button">
                                    <i class="fas fa-plus me-1"></i>Generate New
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary btn-sm mb-0 dropdown-toggle" type="button"
                                        id="bulkActions" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog me-1"></i>Bulk Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="bulkExpire()">
                                                <i class="fas fa-clock me-2"></i>Expire Selected
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card-body px-4 pt-3 pb-0">
                        <form method="GET" action="{{ route('qrcode.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">All Status</option>
                                    <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="EXPIRED" {{ request('status') === 'EXPIRED' ? 'selected' : '' }}>Expired
                                    </option>
                                    <option value="USED" {{ request('status') === 'USED' ? 'selected' : '' }}>Used
                                    </option>
                                    <option value="INVALID" {{ request('status') === 'INVALID' ? 'selected' : '' }}>
                                        Invalid</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="vendor_id" class="form-control form-control-sm">
                                    <option value="">All Vendors</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}"
                                            {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->business_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="expired" value="1"
                                        {{ request('expired') ? 'checked' : '' }} id="expiredFilter">
                                    <label class="form-check-label text-sm" for="expiredFilter">
                                        Show Expired Only
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="expiring_soon" value="1"
                                        {{ request('expiring_soon') ? 'checked' : '' }} id="expiringSoonFilter">
                                    <label class="form-check-label text-sm" for="expiringSoonFilter">
                                        Expiring Soon
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <form id="bulkForm" method="POST" action="{{ route('qrcode.bulk-expire') }}">
                                @csrf
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                                            </th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                QR Code</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Vendor</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Service</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Status</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Validity</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($qrCodes as $qrCode)
                                            <tr
                                                class="{{ $qrCode->is_expired ? 'table-warning' : ($qrCode->isExpiringSoon(24) ? 'table-info' : '') }}">
                                                <td class="ps-4">
                                                    <input type="checkbox" name="qr_code_ids[]"
                                                        value="{{ $qrCode->id }}" class="qr-checkbox">
                                                </td>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <img src="{{ route('qrcode.image', $qrCode) }}"
                                                                class="avatar avatar-sm me-3 border-radius-lg"
                                                                alt="QR Code">
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ substr($qrCode->code, 0, 8) }}...
                                                            </h6>
                                                            <p class="text-xs text-secondary mb-0">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                Generated {{ $qrCode->getFormattedGeneratedDate() }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="mb-0 text-sm">{{ $qrCode->vendor->business_name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $qrCode->vendor->service_category }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="mb-0 text-sm">{{ $qrCode->service->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">RM
                                                            {{ number_format($qrCode->service->price, 2) }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        @if ($qrCode->status === 'ACTIVE')
                                                            <span class="badge badge-sm bg-gradient-success">Active</span>
                                                        @elseif($qrCode->status === 'EXPIRED')
                                                            <span class="badge badge-sm bg-gradient-danger">Expired</span>
                                                        @elseif($qrCode->status === 'USED')
                                                            <span class="badge badge-sm bg-gradient-info">Used</span>
                                                        @elseif($qrCode->status === 'INVALID')
                                                            <span
                                                                class="badge badge-sm bg-gradient-secondary">Invalid</span>
                                                        @elseif($qrCode->status === 'PENDING')
                                                            <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                                        @endif

                                                        @if ($qrCode->isExpiringSoon(24))
                                                            <small class="text-danger text-xs mt-1">
                                                                <i class="fas fa-exclamation-circle me-1"></i>Expiring
                                                                soon!
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="mb-0 text-sm">{{ $qrCode->getFormattedExpiryDate() }}
                                                        </h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $qrCode->time_remaining }}</p>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex">
                                                        <a class="btn btn-link text-info px-2 mb-0"
                                                            href="{{ route('qrcode.show', $qrCode->id) }}"
                                                            title="View Details">
                                                            <i class="fas fa-eye text-info me-1"></i>View
                                                        </a>
                                                        <a class="btn btn-link text-dark px-2 mb-0"
                                                            href="{{ route('qrcode.download', $qrCode) }}"
                                                            title="Download">
                                                            <i class="fas fa-download text-dark me-1"></i>Download
                                                        </a>
                                                        <form action="{{ route('qrcode.destroy', $qrCode->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this QR Code? This action cannot be undone.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-link text-danger px-2 mb-0"
                                                                title="Delete QR Code">
                                                                <i class="far fa-trash-alt text-danger me-1"></i>Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="fas fa-qrcode text-muted" style="font-size: 3rem;"></i>
                                                        <h5 class="text-muted mt-3">No QR Codes Found</h5>
                                                        <p class="text-sm text-muted">Generate your first QR code to get
                                                            started</p>
                                                        <a href="{{ route('qrcode.create') }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="fas fa-plus me-2"></i>Generate QR Code
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </form>
                        </div>

                        @if ($qrCodes->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $qrCodes->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleAll(source) {
                const checkboxes = document.querySelectorAll('.qr-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = source.checked;
                });
            }

            function bulkExpire() {
                const selectedIds = Array.from(document.querySelectorAll('.qr-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedIds.length === 0) {
                    alert('Please select at least one QR code');
                    return;
                }

                if (confirm(`Are you sure you want to expire ${selectedIds.length} QR code(s)?`)) {
                    document.getElementById('bulkForm').submit();
                }
            }

            // Auto-refresh for expiring codes
            setTimeout(() => {
                if ({{ $statistics['expiring_soon'] }} > 0) {
                    location.reload();
                }
            }, 300000); // Refresh every 5 minutes if there are expiring codes
        </script>
    @endpush
@endsection
