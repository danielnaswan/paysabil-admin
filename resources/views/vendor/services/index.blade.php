@extends('layouts.user_type.vendor')

@section('page-title', 'My Services')
@section('page-heading', 'Manage Services')

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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Services</p>
                                    <h5 class="font-weight-bolder mb-0">{{ $services->count() }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-app text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Services</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $services->where('is_available', true)->count() }}</h5>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Orders</p>
                                    <h5 class="font-weight-bolder mb-0">{{ $services->sum('total_orders') }}</h5>
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

            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Revenue</p>
                                    <h5 class="font-weight-bolder mb-0">RM
                                        {{ number_format($services->sum('total_revenue'), 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Services Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">My Services</h5>
                            </div>
                            <div class="d-flex">
                                <a href="{{ route('vendor.services.create') }}" class="btn btn-primary btn-sm me-2">
                                    <i class="fas fa-plus me-1"></i>Add New Service
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Service</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Category</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Price</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Prep Time</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Orders</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Revenue</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($services as $service)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $service->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ Str::limit($service->description, 50) }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $service->category }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold">RM
                                                    {{ number_format($service->price, 2) }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-xs font-weight-bold">{{ $service->preparation_time }}
                                                    min</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span
                                                    class="text-xs font-weight-bold">{{ number_format($service->total_orders ?? 0) }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-xs font-weight-bold">RM
                                                    {{ number_format($service->total_revenue ?? 0, 2) }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <form method="POST"
                                                    action="{{ route('vendor.services.toggle-availability', $service->id) }}"
                                                    style="display: inline;">
                                                    @csrf
                                                    <button type="submit"
                                                        class="badge badge-sm bg-gradient-{{ $service->is_available ? 'success' : 'secondary' }} border-0">
                                                        {{ $service->is_available ? 'Available' : 'Unavailable' }}
                                                    </button>
                                                </form>
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
                                                                href="{{ route('vendor.services.show', $service->id) }}">
                                                                <i class="fas fa-eye me-2"></i>View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('vendor.services.edit', $service->id) }}">
                                                                <i class="fas fa-edit me-2"></i>Edit Service
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('vendor.qrcodes.create') }}?service_id={{ $service->id }}">
                                                                <i class="fas fa-qrcode me-2"></i>Generate QR Code
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('vendor.services.destroy', $service->id) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this service?')">
                                                                    <i class="fas fa-trash me-2"></i>Delete Service
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="ni ni-app text-6xl text-muted mb-3"></i>
                                                    <h6 class="text-muted mb-2">No services created yet</h6>
                                                    <p class="text-sm text-muted mb-3">Start by creating your first service
                                                        to attract customers</p>
                                                    <a href="{{ route('vendor.services.create') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fas fa-plus me-1"></i>Create Your First Service
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Tips --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Service Management Tips</h6>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex mb-3">
                                    <div class="icon icon-shape bg-gradient-success shadow border-radius-md me-3">
                                        <i class="ni ni-bulb-61 text-lg opacity-10 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Keep Descriptions Clear</h6>
                                        <p class="text-sm mb-0">Write detailed descriptions to help customers understand
                                            your services better.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex mb-3">
                                    <div class="icon icon-shape bg-gradient-info shadow border-radius-md me-3">
                                        <i class="ni ni-time-alarm text-lg opacity-10 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Set Realistic Prep Times</h6>
                                        <p class="text-sm mb-0">Accurate preparation times help set proper customer
                                            expectations.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex mb-3">
                                    <div class="icon icon-shape bg-gradient-warning shadow border-radius-md me-3">
                                        <i class="ni ni-chart-bar-32 text-lg opacity-10 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Monitor Performance</h6>
                                        <p class="text-sm mb-0">Track your service performance and adjust prices based on
                                            demand.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
