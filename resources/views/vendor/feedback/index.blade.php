@extends('layouts.user_type.vendor')

@section('page-title', 'Reviews & Feedback')
@section('page-heading', 'Customer Reviews')

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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Reviews</p>
                                    <h5 class="font-weight-bolder mb-0">{{ number_format($stats['total_reviews']) }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-chat-round text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Average Rating</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $stats['average_rating'] }}
                                        <span class="text-warning">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $stats['average_rating'])
                                                    <i class="fas fa-star text-xs"></i>
                                                @else
                                                    <i class="far fa-star text-xs"></i>
                                                @endif
                                            @endfor
                                        </span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="ni ni-trophy text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Response Rate</p>
                                    <h5 class="font-weight-bolder mb-0">{{ $stats['response_rate'] }}%</h5>
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

            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">30-Day Trend</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        <span class="text-{{ $stats['rating_trend'] >= 0 ? 'success' : 'danger' }}">
                                            {{ $stats['rating_trend'] >= 0 ? '+' : '' }}{{ $stats['rating_trend'] }}%
                                        </span>
                                    </h5>
                                    <small class="text-xs text-secondary">{{ $stats['last_30_days_avg'] }} avg
                                        rating</small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-chart-bar-32 text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rating Distribution --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Rating Distribution</h6>
                    </div>
                    <div class="card-body pt-3">
                        @for ($i = 5; $i >= 1; $i--)
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-3" style="width: 60px;">
                                    <span class="text-sm">{{ $i }} star{{ $i > 1 ? 's' : '' }}</span>
                                </div>
                                <div class="progress flex-grow-1 me-3" style="height: 8px;">
                                    <div class="progress-bar bg-gradient-primary" role="progressbar"
                                        style="width: {{ $stats['rating_distribution'][$i]['percentage'] }}%"></div>
                                </div>
                                <div style="width: 50px;">
                                    <span class="text-sm">{{ $stats['rating_distribution'][$i]['count'] }}</span>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters and Search --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">Customer Reviews</h5>
                            </div>
                            <div class="d-flex">
                                <a href="{{ route('vendor.feedback.export', request()->query()) }}"
                                    class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fas fa-download me-1"></i>Export CSV
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Form --}}
                    <div class="card-body">
                        <form method="GET" action="{{ route('vendor.feedback.index') }}" class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Rating</label>
                                <select name="rating" class="form-select form-select-sm">
                                    <option value="">All Ratings</option>
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}"
                                            {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }}
                                            Star{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Response</label>
                                <select name="has_response" class="form-select form-select-sm">
                                    <option value="">All Reviews</option>
                                    <option value="yes" {{ request('has_response') == 'yes' ? 'selected' : '' }}>
                                        Responded</option>
                                    <option value="no" {{ request('has_response') == 'no' ? 'selected' : '' }}>Not
                                        Responded</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                    value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Customer name or review content..." value="{{ request('search') }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-sm me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('vendor.feedback.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reviews List --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body px-0 pt-0 pb-2">
                        @forelse($reviews as $review)
                            <div class="border-bottom p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex">
                                        <div class="avatar avatar-sm me-3">
                                            @if ($review->student->user->profile_picture_url)
                                                <img src="{{ $review->student->user->profile_picture_url }}"
                                                    class="border-radius-lg">
                                            @else
                                                <img src="{{ asset('assets/img/default-avatar.png') }}"
                                                    class="border-radius-lg">
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $review->student->user->name }}</h6>
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="text-warning me-2">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $review->stars)
                                                            <i class="fas fa-star text-sm"></i>
                                                        @else
                                                            <i class="far fa-star text-sm"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span
                                                    class="text-sm text-secondary">{{ $review->review_date->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm mb-0">{{ $review->review_comment }}</p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        @if ($review->vendor_response)
                                            <span class="badge bg-gradient-success">Responded</span>
                                        @else
                                            <a href="{{ route('vendor.feedback.respond', $review->id) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                Respond
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @if ($review->vendor_response)
                                    <div class="ms-5 mt-3 p-3 bg-light border-radius-md">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 text-sm">Your Response</h6>
                                                <p class="text-sm mb-0">{{ $review->vendor_response }}</p>
                                                <small
                                                    class="text-xs text-secondary">{{ $review->response_date->diffForHumans() }}</small>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-link text-secondary p-0" type="button"
                                                    data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('vendor.feedback.respond', $review->id) }}">
                                                            <i class="fas fa-edit me-2"></i>Edit Response
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form
                                                            action="{{ route('vendor.feedback.delete-response', $review->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Are you sure you want to delete this response?')">
                                                                <i class="fas fa-trash me-2"></i>Delete Response
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <p class="text-muted mb-0">No reviews found</p>
                                <small class="text-muted">Customer reviews will appear here</small>
                            </div>
                        @endforelse

                        {{-- Pagination --}}
                        @if ($reviews->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $reviews->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
