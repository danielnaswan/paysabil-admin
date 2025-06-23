@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="alert-text text-white">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        <!-- Vendor Profile Section -->
        <div class="card mb-4">
            <div class="card-header pb-0 px-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Vendor Profile</h6>
                    <div>
                        <a href="{{ route('vendor.edit', $vendor->id) }}" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-pencil-alt me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('vendor.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-4 p-3">
                <div class="row">
                    <div class="col-md-4 text-center">
                        @if ($vendor->user && $vendor->user->profile_picture_url)
                            <img src="{{ $vendor->user->profile_picture_url }}" alt="vendor profile"
                                class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{ asset('assets/img/default-avatar.png') }}" alt="default profile"
                                class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                        <div class="mt-3">
                            @if ($vendor->average_rating > 0)
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $vendor->average_rating)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                    <span
                                        class="ms-2 text-sm font-weight-bold">{{ number_format($vendor->average_rating, 1) }}</span>
                                </div>
                                <p class="text-xs text-muted">({{ $vendor->total_reviews }}
                                    {{ Str::plural('review', $vendor->total_reviews) }})</p>
                            @else
                                <p class="text-sm text-muted">No ratings yet</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4 class="font-weight-bold mb-3">{{ $vendor->business_name }}</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <span class="text-sm font-weight-bold text-dark">Service Category:</span>
                                    <p class="text-sm mb-0">{{ $vendor->service_category }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <span class="text-sm font-weight-bold text-dark">Experience:</span>
                                    <p class="text-sm mb-0">{{ $vendor->experience_years }}
                                        {{ Str::plural('year', $vendor->experience_years) }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <span class="text-sm font-weight-bold text-dark">Member Since:</span>
                                    <p class="text-sm mb-0">{{ $vendor->created_at->format('F j, Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if ($vendor->user)
                                    <div class="info-item mb-3">
                                        <span class="text-sm font-weight-bold text-dark">Email:</span>
                                        <p class="text-sm mb-0">{{ $vendor->user->email }}</p>
                                    </div>
                                    <div class="info-item mb-3">
                                        <span class="text-sm font-weight-bold text-dark">Phone:</span>
                                        <p class="text-sm mb-0">{{ $vendor->user->phone_number ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="info-item mb-3">
                                        <span class="text-sm font-weight-bold text-dark">Account Status:</span>
                                        <span class="badge badge-sm bg-gradient-success">Active</span>
                                    </div>
                                @else
                                    <div class="info-item mb-3">
                                        <span class="text-sm font-weight-bold text-danger">Account Status:</span>
                                        <span class="badge badge-sm bg-gradient-danger">No User Account</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Services</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $statistics['total_services'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="fas fa-utensils text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Services</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $statistics['active_services'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="fas fa-check-circle text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Revenue</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        RM {{ number_format($statistics['total_revenue'], 2) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="fas fa-chart-line text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Monthly Revenue</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        RM {{ number_format($statistics['monthly_revenue'], 2) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="fas fa-calendar-alt text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services/Menu Section -->
        <div class="card">
            <div class="card-header pb-0 px-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Services & Menu Items</h6>
                    <a href="{{ route('services.create', ['vendor' => $vendor->id]) }}"
                        class="btn btn-sm bg-gradient-primary">
                        <i class="fas fa-plus me-2"></i>Add New Menu Item
                    </a>
                </div>
            </div>
            <div class="card-body pt-4 p-3">
                @if ($vendor->services->count() > 0)
                    <div class="row">
                        @foreach ($vendor->services as $service)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="mb-0">{{ $service->name }}</h6>
                                            <span
                                                class="badge bg-{{ $service->is_available ? 'success' : 'danger' }} badge-sm">
                                                {{ $service->is_available ? 'Available' : 'Unavailable' }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-muted mb-2">{{ Str::limit($service->description, 80) }}</p>

                                        <div class="mb-3">
                                            <div class="row text-sm">
                                                <div class="col-6">
                                                    <span class="text-dark font-weight-bold">Category:</span>
                                                    <p class="mb-1">{{ $service->category }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-dark font-weight-bold">Price:</span>
                                                    <p class="mb-1">RM {{ number_format($service->price, 2) }}</p>
                                                </div>
                                            </div>
                                            <div class="row text-sm">
                                                <div class="col-6">
                                                    <span class="text-dark font-weight-bold">Prep Time:</span>
                                                    <p class="mb-1">{{ $service->preparation_time }} mins</p>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-dark font-weight-bold">Orders:</span>
                                                    <p class="mb-1">{{ $service->completedTransactions->count() ?? 0 }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('services.edit', $service->id) }}"
                                                    class="btn btn-outline-dark btn-sm">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <form action="{{ route('services.destroy', $service->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this menu item?')"
                                                        title="Delete Service">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <a href="{{ route('qrcode.create') }}?service_id={{ $service->id }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-qrcode me-1"></i>Generate QR
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg mb-3">
                            <i class="fas fa-utensils opacity-10"></i>
                        </div>
                        <h5 class="text-muted">No Menu Items Yet</h5>
                        <p class="text-sm text-muted mb-4">Get started by adding your first menu item or service.</p>
                        <a href="{{ route('services.create', ['vendor' => $vendor->id]) }}"
                            class="btn bg-gradient-primary">
                            <i class="fas fa-plus me-2"></i>Add First Menu Item
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Ratings Section -->
        @if ($vendor->ratings->count() > 0)
            <div class="card mt-4">
                <div class="card-header pb-0 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">Recent Reviews & Ratings</h6>
                        <a href="{{ route('report.feedback', $vendor->id) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-chart-bar me-2"></i>View All Feedback
                        </a>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    @foreach ($vendor->ratings->take(5) as $rating)
                        <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-shrink-0">
                                @if ($rating->student && $rating->student->user && $rating->student->user->profile_picture_url)
                                    <img src="{{ $rating->student->user->profile_picture_url }}"
                                        class="avatar avatar-sm rounded-circle me-3" alt="student">
                                @else
                                    <img src="{{ asset('assets/img/default-avatar.png') }}"
                                        class="avatar avatar-sm rounded-circle me-3" alt="default">
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="mb-0 me-2">{{ $rating->student->full_name ?? 'Anonymous' }}</h6>
                                    <div class="d-flex me-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $rating->stars)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ $rating->review_date->diffForHumans() }}</small>
                                </div>
                                @if ($rating->review_comment)
                                    <p class="text-sm mb-1">{{ $rating->review_comment }}</p>
                                @endif
                                @if ($rating->vendor_response)
                                    <div class="bg-light rounded p-2 mt-2">
                                        <small class="text-dark font-weight-bold">Vendor Response:</small>
                                        <p class="text-sm mb-0">{{ $rating->vendor_response }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
