@extends('layouts.user_type.auth')

@section('content')

    <div>
        @if (session('success'))
            <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                <span class="alert-text text-white">
                    {{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="m-3 alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-text text-white">
                    {{ $errors->first() }}</span>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Vendors</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $vendors->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="fas fa-store opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Services</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $vendors->sum('services_count') }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="fas fa-utensils opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Avg Rating</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $vendors->count() > 0 ? number_format($vendors->avg('average_rating'), 1) : '0.0' }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="fas fa-star opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Reviews</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $vendors->sum('total_reviews') }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="fas fa-comments opacity-10" aria-hidden="true"></i>
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
                                <h5 class="mb-0">All Vendors</h5>
                                <p class="text-sm mb-0">Manage registered vendors and their information</p>
                            </div>
                            <a href="{{ route('vendor.create') }}" class="btn bg-gradient-primary btn-sm mb-0"
                                type="button">
                                <i class="fas fa-plus me-2"></i>New Vendor
                            </a>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Vendor
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Business
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Contact
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Category
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Rating
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Status
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vendors as $vendor)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        @if ($vendor->user && $vendor->user->profile_picture_url)
                                                            <img src="{{ $vendor->user->profile_picture_url }}"
                                                                class="avatar avatar-sm me-3 border-radius-lg">
                                                        @else
                                                            <img src="{{ asset('assets/img/default-avatar.png') }}"
                                                                class="avatar avatar-sm me-3 border-radius-lg">
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $vendor->business_name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            <i class="fas fa-briefcase me-1"></i>
                                                            {{ $vendor->experience_years }} years experience
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $vendor->business_name }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-xs font-weight-bold mb-1">
                                                        <i
                                                            class="fas fa-envelope me-1"></i>{{ $vendor->user->email ?? 'N/A' }}
                                                    </span>
                                                    @if ($vendor->user && $vendor->user->phone_number)
                                                        <span class="text-xs text-secondary">
                                                            <i
                                                                class="fas fa-phone me-1"></i>{{ $vendor->user->phone_number }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-sm bg-gradient-secondary">{{ $vendor->service_category }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= $vendor->average_rating)
                                                                <i class="fas fa-star text-warning me-1"
                                                                    style="font-size: 0.75rem;"></i>
                                                            @else
                                                                <i class="far fa-star text-warning me-1"
                                                                    style="font-size: 0.75rem;"></i>
                                                            @endif
                                                        @endfor
                                                        <span
                                                            class="text-xs font-weight-bold ms-1">{{ number_format($vendor->average_rating, 1) }}</span>
                                                    </div>
                                                    <span class="text-xs text-secondary mt-1">
                                                        ({{ $vendor->total_reviews }} reviews)
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @if ($vendor->user)
                                                        <span class="badge badge-sm bg-gradient-success">Active</span>
                                                    @else
                                                        <span class="badge badge-sm bg-gradient-danger">Inactive</span>
                                                    @endif
                                                    @if ($vendor->services_count > 0)
                                                        <small class="text-success text-xs mt-1">
                                                            <i
                                                                class="fas fa-check-circle me-1"></i>{{ $vendor->services_count }}
                                                            services
                                                        </small>
                                                    @else
                                                        <small class="text-warning text-xs mt-1">
                                                            <i class="fas fa-exclamation-circle me-1"></i>No services
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex">
                                                    <a class="btn btn-link text-info px-2 mb-0"
                                                        href="{{ route('vendor.show', $vendor->id) }}"
                                                        title="View Details">
                                                        <i class="fas fa-eye text-info me-1"></i>View
                                                    </a>
                                                    <a class="btn btn-link text-dark px-2 mb-0"
                                                        href="{{ route('vendor.edit', $vendor->id) }}"
                                                        title="Edit Vendor">
                                                        <i class="fas fa-pencil-alt text-dark me-1"></i>Edit
                                                    </a>
                                                    <form action="{{ route('vendor.destroy', $vendor->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete {{ $vendor->business_name }}? This action cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger px-2 mb-0"
                                                            title="Delete Vendor">
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
                                                    <i class="fas fa-store text-muted" style="font-size: 3rem;"></i>
                                                    <h5 class="text-muted mt-3">No Vendors Found</h5>
                                                    <p class="text-sm text-muted">Start by adding your first vendor to the
                                                        system.</p>
                                                    <a href="{{ route('vendor.create') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fas fa-plus me-2"></i>Add Vendor
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
    </div>

@endsection
