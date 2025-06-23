@extends('layouts.user_type.vendor')

@section('page-title', 'Profile')
@section('page-heading', 'My Profile')

@section('content')
    <div class="container-fluid">
        <div class="page-header min-height-300 border-radius-xl mt-4"
            style="background-image: url('{{ asset('assets/img/curved-images/curved0.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-primary opacity-6"></span>
        </div>
        <div class="card card-body blur shadow-blur mx-4 mt-n6 overflow-hidden">
            <div class="row gx-4">
                <div class="col-auto">
                    <div class="avatar avatar-xl position-relative">
                        @if ($vendor->user->profile_picture_url)
                            <img src="{{ $vendor->user->profile_picture_url }}" alt="profile_image"
                                class="w-100 border-radius-lg shadow-sm">
                        @else
                            <img src="{{ asset('assets/img/default-avatar.png') }}" alt="profile_image"
                                class="w-100 border-radius-lg shadow-sm">
                        @endif
                    </div>
                </div>
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">{{ $vendor->business_name }}</h5>
                        <p class="mb-0 font-weight-bold text-sm">{{ $vendor->service_category }}</p>
                        <p class="mb-0 text-sm">{{ $vendor->experience_years }} years experience</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
                    <div class="nav-wrapper position-relative end-0">
                        <div class="row">
                            <div class="col-6 text-center">
                                <h4 class="font-weight-bolder mb-0">{{ $vendor->average_rating }}</h4>
                                <div class="text-warning mb-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $vendor->average_rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-sm">Average Rating</span>
                            </div>
                            <div class="col-6 text-center">
                                <h4 class="font-weight-bolder mb-0">{{ $vendor->total_reviews }}</h4>
                                <span class="text-sm">Total Reviews</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row">
            {{-- Profile Information --}}
            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0">Profile Information</h6>
                            <a href="{{ route('vendor.profile.edit') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <hr class="horizontal gray-light my-4">
                        <ul class="list-group">
                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                <strong class="text-dark">Business Name:</strong> &nbsp; {{ $vendor->business_name }}
                            </li>
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">Email:</strong> &nbsp; {{ $vendor->user->email }}
                            </li>
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">Phone:</strong> &nbsp; {{ $vendor->user->phone_number }}
                            </li>
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">Service Category:</strong> &nbsp; {{ $vendor->service_category }}
                            </li>
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">Experience:</strong> &nbsp; {{ $vendor->experience_years }} years
                            </li>
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">Location:</strong> &nbsp;
                                {{ $vendor->user->location ?? 'Not specified' }}
                            </li>
                            <li class="list-group-item border-0 ps-0 pb-0">
                                <strong class="text-dark">About:</strong> &nbsp;
                                <p class="text-sm mt-2">{{ $vendor->user->about_me ?? 'No description provided.' }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Business Statistics --}}
            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Business Statistics</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex flex-column align-items-center text-center mb-3">
                                    <div
                                        class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md mb-2">
                                        <i class="ni ni-app text-lg opacity-10"></i>
                                    </div>
                                    <h5 class="font-weight-bolder mb-0">{{ $vendor->services->count() }}</h5>
                                    <span class="text-sm">Total Services</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex flex-column align-items-center text-center mb-3">
                                    <div
                                        class="icon icon-shape bg-gradient-success shadow text-center border-radius-md mb-2">
                                        <i class="ni ni-check-bold text-lg opacity-10"></i>
                                    </div>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $vendor->services->where('is_available', true)->count() }}</h5>
                                    <span class="text-sm">Active Services</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex flex-column align-items-center text-center mb-3">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md mb-2">
                                        <i class="ni ni-credit-card text-lg opacity-10"></i>
                                    </div>
                                    {{-- FIXED: Use businessStats instead of undefined variable --}}
                                    <h5 class="font-weight-bolder mb-0">{{ number_format($businessStats['total_orders']) }}
                                    </h5>
                                    <span class="text-sm">Total Orders</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex flex-column align-items-center text-center mb-3">
                                    <div
                                        class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md mb-2">
                                        <i class="ni ni-money-coins text-lg opacity-10"></i>
                                    </div>
                                    {{-- FIXED: Use businessStats instead of calculating in view --}}
                                    <h5 class="font-weight-bolder mb-0">RM
                                        {{ number_format($businessStats['total_revenue'], 2) }}</h5>
                                    <span class="text-sm">Total Revenue</span>
                                </div>
                            </div>
                        </div>

                        <hr class="horizontal gray-light my-4">

                        <div class="d-flex justify-content-between">
                            <span class="text-sm">Profile Completion</span>
                            <span class="text-sm font-weight-bold">
                                @php
                                    $fields = [
                                        'name',
                                        'email',
                                        'phone_number',
                                        'location',
                                        'about_me',
                                        'profile_picture_url',
                                    ];
                                    $completed = collect($fields)
                                        ->filter(function ($field) use ($vendor) {
                                            return !empty($vendor->user->$field);
                                        })
                                        ->count();
                                    $percentage = round(($completed / count($fields)) * 100);
                                @endphp
                                {{ $percentage }}%
                            </span>
                        </div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-gradient-primary" role="progressbar"
                                style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Reviews --}}
            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0">Recent Reviews</h6>
                            <a href="{{ route('vendor.feedback.index') }}" class="btn btn-outline-primary btn-sm">View
                                All</a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @forelse($vendor->ratings->take(3) as $rating)
                            <div class="d-flex mb-3">
                                <div class="avatar avatar-sm me-3">
                                    @if ($rating->student->user->profile_picture_url)
                                        <img src="{{ $rating->student->user->profile_picture_url }}"
                                            class="border-radius-lg">
                                    @else
                                        <img src="{{ asset('assets/img/default-avatar.png') }}" class="border-radius-lg">
                                    @endif
                                </div>
                                <div class="d-flex flex-column justify-content-center flex-grow-1">
                                    <h6 class="mb-0 text-sm">{{ $rating->student->user->name }}</h6>
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="text-warning me-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $rating->stars)
                                                    <i class="fas fa-star text-xs"></i>
                                                @else
                                                    <i class="far fa-star text-xs"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span
                                            class="text-xs text-secondary">{{ $rating->review_date->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-secondary mb-0">{{ Str::limit($rating->review_comment, 60) }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">No reviews yet</p>
                                <small class="text-muted">Customer reviews will appear here</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Services Overview --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h6>My Services</h6>
                            <a href="{{ route('vendor.services.index') }}" class="btn btn-outline-primary btn-sm">Manage
                                Services</a>
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
                                            Orders</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vendor->services->take(5) as $service)
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
                                                {{-- FIXED: Simple count instead of complex relationship query --}}
                                                <span class="text-xs font-weight-bold">-</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span
                                                    class="badge badge-sm bg-gradient-{{ $service->is_available ? 'success' : 'secondary' }}">
                                                    {{ $service->is_available ? 'Available' : 'Unavailable' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <p class="text-muted mb-0">No services created yet</p>
                                                <a href="{{ route('vendor.services.create') }}"
                                                    class="btn btn-primary btn-sm mt-2">
                                                    <i class="fas fa-plus me-1"></i>Create Your First Service
                                                </a>
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
