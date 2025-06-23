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
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        <!-- Feedback Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Feedbacks</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $feedbackSummary['total_feedbacks'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-chat-round text-lg opacity-10" aria-hidden="true"></i>
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
                                    <h5 class="font-weight-bolder mb-0 text-warning">
                                        {{ $feedbackSummary['average_rating'] }} / 5.0
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="ni ni-trophy text-lg opacity-10" aria-hidden="true"></i>
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
                                    <h5 class="font-weight-bolder mb-0 text-info">
                                        {{ $feedbackSummary['response_rate'] }}%
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-send text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Positive Reviews</p>
                                    <h5 class="font-weight-bolder mb-0 text-success">
                                        {{ $feedbackSummary['sentiment_breakdown']['positive'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="ni ni-like-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sentiment Breakdown Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card card-frame">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm bg-gradient-success shadow text-center me-3">
                                <i class="ni ni-like-2 text-white opacity-10"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-sm">Positive (4-5 ⭐)</h6>
                                <p class="text-sm mb-0 text-muted">Satisfied customers</p>
                                <h5 class="font-weight-bolder text-success mb-0">
                                    {{ $feedbackSummary['sentiment_breakdown']['positive'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-frame">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm bg-gradient-warning shadow text-center me-3">
                                <i class="ni ni-satisfied text-white opacity-10"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-sm">Neutral (3 ⭐)</h6>
                                <p class="text-sm mb-0 text-muted">Average experience</p>
                                <h5 class="font-weight-bolder text-warning mb-0">
                                    {{ $feedbackSummary['sentiment_breakdown']['neutral'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-frame">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm bg-gradient-danger shadow text-center me-3">
                                <i class="ni ni-fat-remove text-white opacity-10"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-sm">Negative (1-2 ⭐)</h6>
                                <p class="text-sm mb-0 text-muted">Needs improvement</p>
                                <h5 class="font-weight-bolder text-danger mb-0">
                                    {{ $feedbackSummary['sentiment_breakdown']['negative'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution Chart -->
        @if ($feedbackSummary['total_feedbacks'] > 0)
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-bar text-primary me-2"></i>Rating Distribution
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="ratingDistributionChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-line text-info me-2"></i>Rating Trends
                            </h6>
                        </div>
                        <div class="card-body">
                            @if (isset($trendAnalysis['monthly_trends']) && count($trendAnalysis['monthly_trends']) > 1)
                                <div class="chart">
                                    <canvas id="trendChart" height="300"></canvas>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-chart-line text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">Insufficient data for trend analysis</p>
                                    <small class="text-muted">Need at least 2 months of data</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Feedback Report -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-start">
                            <div class="mb-3 mb-md-0">
                                <h5 class="mb-0">
                                    <i class="fas fa-comments text-primary me-2"></i>
                                    {{ $vendorModel->business_name }} - Customer Feedback Report
                                </h5>
                                <p class="text-sm mb-0">
                                    Service Category: {{ $vendorModel->service_category }}
                                </p>
                                @if ($feedbackSummary['latest_feedback_date'])
                                    <p class="text-xs text-muted mb-0">
                                        Latest feedback:
                                        {{ \Carbon\Carbon::parse($feedbackSummary['latest_feedback_date'])->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-end">
                                <!-- Filter Form -->
                                <form method="GET" action="{{ route('report.feedback.vendor', $vendorModel->id) }}"
                                    class="d-flex align-items-end gap-2" id="filterForm">
                                    <div>
                                        <label for="rating_filter" class="form-label text-xs mb-1">Rating Filter</label>
                                        <select name="rating_filter" id="rating_filter"
                                            class="form-select form-select-sm" style="min-width: 130px;">
                                            <option value="all"
                                                {{ ($validated['rating_filter'] ?? 'all') == 'all' ? 'selected' : '' }}>All
                                                Ratings</option>
                                            <option value="positive"
                                                {{ ($validated['rating_filter'] ?? '') == 'positive' ? 'selected' : '' }}>
                                                Positive (4-5⭐)</option>
                                            <option value="neutral"
                                                {{ ($validated['rating_filter'] ?? '') == 'neutral' ? 'selected' : '' }}>
                                                Neutral (3⭐)</option>
                                            <option value="negative"
                                                {{ ($validated['rating_filter'] ?? '') == 'negative' ? 'selected' : '' }}>
                                                Negative (1-2⭐)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="start_date" class="form-label text-xs mb-1">From Date</label>
                                        <input name="start_date" id="start_date" class="form-control form-control-sm"
                                            type="date" style="min-width: 130px;"
                                            value="{{ $validated['start_date'] ?? '' }}">
                                    </div>
                                    <div>
                                        <label for="end_date" class="form-label text-xs mb-1">To Date</label>
                                        <input name="end_date" id="end_date" class="form-control form-control-sm"
                                            type="date" style="min-width: 130px;"
                                            value="{{ $validated['end_date'] ?? '' }}">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-filter"></i> Apply Filter
                                    </button>
                                </form>

                                <!-- Export Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-success dropdown-toggle" type="button"
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.feedback.vendor', array_merge(['vendor' => $vendorModel->id], request()->all(), ['export' => 'pdf'])) }}">
                                                <i class="fas fa-file-pdf text-danger me-2"></i> Export as PDF</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.feedback.vendor', array_merge(['vendor' => $vendorModel->id], request()->all(), ['export' => 'excel'])) }}">
                                                <i class="fas fa-file-excel text-success me-2"></i> Export as Excel</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('report.feedback.vendor', array_merge(['vendor' => $vendorModel->id], request()->all(), ['export' => 'csv'])) }}">
                                                <i class="fas fa-file-csv text-info me-2"></i> Export as CSV</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="feedbackTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Date</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Student</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Rating</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Feedback</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Vendor Response</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Response Status</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($feedbacks as $feedback)
                                        <tr class="feedback-row rating-{{ $feedback->stars }}">
                                            <td class="ps-4">
                                                <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">
                                                        {{ $feedback->review_date->format('M d, Y') }}</h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ $feedback->review_date->diffForHumans() }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($feedback->student && $feedback->student->user && $feedback->student->user->profile_picture_url)
                                                        <img src="{{ $feedback->student->user->profile_picture_url }}"
                                                            class="avatar avatar-sm me-3"
                                                            alt="{{ $feedback->student->full_name }}">
                                                    @else
                                                        <img src="{{ asset('assets/img/default-avatar.png') }}"
                                                            class="avatar avatar-sm me-3" alt="Default Avatar">
                                                    @endif
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">
                                                            {{ $feedback->student->full_name ?? 'Anonymous' }}</h6>
                                                        @if ($feedback->student && $feedback->student->user)
                                                            <p class="text-xs text-secondary mb-0">
                                                                {{ $feedback->student->matrix_no }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rating-display me-2">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= $feedback->stars)
                                                                <i class="fas fa-star text-warning"></i>
                                                            @else
                                                                <i class="far fa-star text-muted"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span
                                                        class="badge badge-sm bg-gradient-{{ $feedback->stars >= 4 ? 'success' : ($feedback->stars >= 3 ? 'warning' : 'danger') }}">
                                                        {{ $feedback->stars }}/5
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="feedback-content" style="max-width: 300px;">
                                                    @if ($feedback->review_comment)
                                                        <p class="text-sm mb-0">
                                                            {{ Str::limit($feedback->review_comment, 100) }}
                                                            @if (strlen($feedback->review_comment) > 100)
                                                                <button class="btn btn-link btn-sm p-0 ms-1"
                                                                    onclick="showFullFeedback('{{ $feedback->id }}', '{{ addslashes($feedback->review_comment) }}')"
                                                                    title="Read full comment">
                                                                    <i class="fas fa-expand-alt"></i>
                                                                </button>
                                                            @endif
                                                        </p>
                                                    @else
                                                        <p class="text-muted text-sm mb-0 fst-italic">No comment provided
                                                        </p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="response-content" style="max-width: 250px;">
                                                    @if ($feedback->vendor_response)
                                                        <p class="text-sm mb-0">
                                                            {{ Str::limit($feedback->vendor_response, 80) }}
                                                            @if (strlen($feedback->vendor_response) > 80)
                                                                <button class="btn btn-link btn-sm p-0 ms-1"
                                                                    onclick="showFullResponse('{{ $feedback->id }}', '{{ addslashes($feedback->vendor_response) }}')"
                                                                    title="Read full response">
                                                                    <i class="fas fa-expand-alt"></i>
                                                                </button>
                                                            @endif
                                                        </p>
                                                        <p class="text-xs text-muted mb-0">
                                                            {{ $feedback->response_date ? $feedback->response_date->format('M d, Y') : 'Date unknown' }}
                                                        </p>
                                                    @else
                                                        <p class="text-muted text-sm mb-0 fst-italic">No response yet</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($feedback->vendor_response)
                                                    <span class="badge badge-sm bg-gradient-success">
                                                        <i class="fas fa-check me-1"></i>Responded
                                                    </span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-warning">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                    @if ($feedback->stars <= 2)
                                                        <br><small class="text-danger">Urgent</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-sm btn-outline-info mb-0"
                                                        onclick="viewFeedbackDetails('{{ $feedback->id }}', {{ json_encode($feedback) }})"
                                                        title="View Full Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if ($feedback->student)
                                                        <a href="{{ route('student.show', $feedback->student->id) }}"
                                                            class="btn btn-sm btn-outline-primary mb-0"
                                                            title="View Student Profile">
                                                            <i class="fas fa-user"></i>
                                                        </a>
                                                    @endif
                                                    @if (!$feedback->vendor_response && $feedback->student && $feedback->student->user)
                                                        <a href="mailto:{{ $feedback->student->user->email }}?subject=Response to your feedback for {{ $vendorModel->business_name }}"
                                                            class="btn btn-sm btn-outline-success mb-0"
                                                            title="Send Response Email">
                                                            <i class="fas fa-reply"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-comments text-muted fa-3x mb-3"></i>
                                                    <h6 class="text-muted">No Feedback Found</h6>
                                                    <p class="text-muted">No customer feedback found for the selected
                                                        criteria.</p>
                                                    <p class="text-sm text-muted">Encourage customers to leave reviews to
                                                        improve service quality.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($feedbacks->count() > 0)
                            <div class="px-4 pt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-sm text-muted">
                                            Showing {{ $feedbacks->count() }} feedback entries for
                                            {{ $vendorModel->business_name }}
                                        </p>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <p class="text-sm text-muted">
                                            Report generated on {{ now()->format('M d, Y \a\t H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Insights -->
        @if ($feedbacks->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card mx-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-lightbulb text-warning"></i> Performance Insights & Recommendations
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-sm font-weight-bold">Customer Satisfaction Analysis:</h6>
                                    <ul class="text-sm">
                                        <li>Overall satisfaction: {{ $feedbackSummary['average_rating'] }}/5.0
                                            @if ($feedbackSummary['average_rating'] >= 4.0)
                                                <span class="text-success">(Excellent)</span>
                                            @elseif($feedbackSummary['average_rating'] >= 3.0)
                                                <span class="text-warning">(Good)</span>
                                            @else
                                                <span class="text-danger">(Needs Improvement)</span>
                                            @endif
                                        </li>
                                        <li>Customer engagement: {{ $feedbackSummary['total_feedbacks'] }} total reviews
                                        </li>
                                        <li>Response rate: {{ $feedbackSummary['response_rate'] }}%
                                            @if ($feedbackSummary['response_rate'] >= 80)
                                                <span class="text-success">(Excellent)</span>
                                            @elseif($feedbackSummary['response_rate'] >= 50)
                                                <span class="text-warning">(Good)</span>
                                            @else
                                                <span class="text-danger">(Poor)</span>
                                            @endif
                                        </li>
                                        <li>Trend direction:
                                            @if (isset($trendAnalysis['trend_direction']))
                                                <span
                                                    class="text-{{ $trendAnalysis['trend_direction'] == 'improving' ? 'success' : ($trendAnalysis['trend_direction'] == 'declining' ? 'danger' : 'info') }}">
                                                    {{ ucfirst($trendAnalysis['trend_direction']) }}
                                                </span>
                                            @else
                                                <span class="text-muted">Insufficient data</span>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-sm font-weight-bold">Recommended Actions:</h6>
                                    <ul class="text-sm">
                                        @if ($feedbackSummary['sentiment_breakdown']['negative'] > 0)
                                            <li class="text-danger">Address
                                                {{ $feedbackSummary['sentiment_breakdown']['negative'] }} negative
                                                review(s) urgently</li>
                                        @endif
                                        @if ($feedbackSummary['response_rate'] < 70)
                                            <li class="text-warning">Improve response rate - currently at
                                                {{ $feedbackSummary['response_rate'] }}%</li>
                                        @endif
                                        @if ($feedbackSummary['average_rating'] < 4.0)
                                            <li class="text-info">Focus on service quality improvements</li>
                                        @endif
                                        @if ($feedbackSummary['sentiment_breakdown']['positive'] > $feedbackSummary['sentiment_breakdown']['negative'])
                                            <li class="text-success">Leverage positive reviews for marketing</li>
                                        @endif
                                        <li>Regular follow-ups with customers for continuous improvement</li>
                                    </ul>
                                </div>
                            </div>
                            @if ($feedbackSummary['sentiment_breakdown']['negative'] > 3 || $feedbackSummary['average_rating'] < 3.0)
                                <div class="alert alert-warning mt-3" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Action Required:</strong> High number of negative reviews detected.
                                    Consider implementing immediate service improvements and responding to unhappy
                                    customers.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Feedback Details Modal -->
    <div class="modal fade" id="feedbackDetailsModal" tabindex="-1" aria-labelledby="feedbackDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackDetailsModalLabel">
                        <i class="fas fa-comment-dots text-primary me-2"></i>Feedback Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="feedbackDetailsContent">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Text Modal -->
    <div class="modal fade" id="fullTextModal" tabindex="-1" aria-labelledby="fullTextModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fullTextModalLabel">Full Text</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="fullTextContent">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize DataTable for better UX if many feedbacks
                @if ($feedbacks->count() > 10)
                    $('#feedbackTable').DataTable({
                        "pageLength": 25,
                        "ordering": true,
                        "searching": true,
                        "lengthChange": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        "order": [
                            [1, "desc"]
                        ], // Sort by date desc
                        "language": {
                            "search": "Search feedback:",
                            "lengthMenu": "Show _MENU_ feedback entries per page",
                            "info": "Showing _START_ to _END_ of _TOTAL_ feedback entries"
                        },
                        "columnDefs": [{
                                "orderable": false,
                                "targets": [7]
                            } // Disable ordering on action column
                        ]
                    });
                @endif

                // Add rating-based row styling
                document.querySelectorAll('.feedback-row').forEach(function(row) {
                    if (row.classList.contains('rating-5') || row.classList.contains('rating-4')) {
                        row.style.borderLeft = '3px solid #28a745'; // Green for positive
                    } else if (row.classList.contains('rating-3')) {
                        row.style.borderLeft = '3px solid #ffc107'; // Yellow for neutral
                    } else if (row.classList.contains('rating-2') || row.classList.contains('rating-1')) {
                        row.style.borderLeft = '3px solid #dc3545'; // Red for negative
                        row.style.backgroundColor = 'rgba(220, 53, 69, 0.05)';
                    }
                });

                // Set max date to today for date inputs
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('start_date').setAttribute('max', today);
                document.getElementById('end_date').setAttribute('max', today);

                // Validate date range
                document.getElementById('start_date').addEventListener('change', function() {
                    const startDate = this.value;
                    const endDateInput = document.getElementById('end_date');
                    endDateInput.setAttribute('min', startDate);

                    if (endDateInput.value && endDateInput.value < startDate) {
                        endDateInput.value = startDate;
                    }
                });

                // Rating Distribution Chart
                @if ($feedbackSummary['total_feedbacks'] > 0 && isset($feedbackSummary['rating_distribution']))
                    const ratingData = @json($feedbackSummary['rating_distribution']);
                    const labels = Object.keys(ratingData).map(rating => rating + ' Star' + (rating == 1 ? '' : 's'));
                    const data = Object.values(ratingData);
                    const colors = ['#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997'];

                    const ctx1 = document.getElementById('ratingDistributionChart').getContext('2d');
                    new Chart(ctx1, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: colors,
                                borderColor: colors,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                                            return context.label + ': ' + context.parsed + ' (' +
                                                percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                @endif

                // Trend Chart
                @if (isset($trendAnalysis['monthly_trends']) && count($trendAnalysis['monthly_trends']) > 1)
                    const trendData = @json($trendAnalysis['monthly_trends']);
                    const trendLabels = trendData.map(item => item.month);
                    const trendRatings = trendData.map(item => item.average_rating);
                    const trendCounts = trendData.map(item => item.total_ratings);

                    const ctx2 = document.getElementById('trendChart').getContext('2d');
                    new Chart(ctx2, {
                        type: 'line',
                        data: {
                            labels: trendLabels,
                            datasets: [{
                                label: 'Average Rating',
                                data: trendRatings,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1,
                                yAxisID: 'y'
                            }, {
                                label: 'Number of Reviews',
                                data: trendCounts,
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                tension: 0.1,
                                yAxisID: 'y1'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Month'
                                    }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: 'Average Rating'
                                    },
                                    min: 0,
                                    max: 5
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: 'Number of Reviews'
                                    },
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                }
                            }
                        }
                    });
                @endif
            });

            // Function to view full feedback details in modal
            function viewFeedbackDetails(feedbackId, feedback) {
                const modalContent = document.getElementById('feedbackDetailsContent');

                const stars = '★'.repeat(feedback.stars) + '☆'.repeat(5 - feedback.stars);
                const reviewDate = new Date(feedback.review_date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                const responseDate = feedback.response_date ?
                    new Date(feedback.response_date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) : 'No response yet';

                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-sm font-weight-bold mb-2">Customer Information:</h6>
                            <p class="text-sm mb-1"><strong>Name:</strong> ${feedback.student ? feedback.student.full_name : 'Anonymous'}</p>
                            <p class="text-sm mb-1"><strong>Matrix No:</strong> ${feedback.student ? feedback.student.matrix_no : 'N/A'}</p>
                            <p class="text-sm mb-3"><strong>Review Date:</strong> ${reviewDate}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-sm font-weight-bold mb-2">Rating Details:</h6>
                            <p class="text-sm mb-1"><strong>Rating:</strong> <span class="text-warning">${stars}</span> (${feedback.stars}/5)</p>
                            <p class="text-sm mb-1"><strong>Category:</strong> ${feedback.stars >= 4 ? 'Positive' : (feedback.stars >= 3 ? 'Neutral' : 'Negative')}</p>
                            <p class="text-sm mb-3"><strong>Modified:</strong> ${feedback.is_modified ? 'Yes' : 'No'}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-sm font-weight-bold mb-2">Customer Feedback:</h6>
                            <div class="p-3 bg-light rounded mb-3">
                                <p class="text-sm mb-0">${feedback.review_comment || 'No comment provided'}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-sm font-weight-bold mb-2">Vendor Response:</h6>
                            <div class="p-3 ${feedback.vendor_response ? 'bg-success' : 'bg-warning'} bg-opacity-10 rounded mb-3">
                                <p class="text-sm mb-1">${feedback.vendor_response || 'No response provided yet'}</p>
                                ${feedback.vendor_response ? `<small class="text-muted">Response Date: ${responseDate}</small>` : ''}
                            </div>
                        </div>
                    </div>
                `;

                modalContent.innerHTML = html;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('feedbackDetailsModal'));
                modal.show();
            }

            // Function to show full feedback text
            function showFullFeedback(feedbackId, fullText) {
                document.getElementById('fullTextModalLabel').textContent = 'Full Customer Feedback';
                document.getElementById('fullTextContent').innerHTML = `<p class="text-sm">${fullText}</p>`;

                const modal = new bootstrap.Modal(document.getElementById('fullTextModal'));
                modal.show();
            }

            // Function to show full response text
            function showFullResponse(feedbackId, fullText) {
                document.getElementById('fullTextModalLabel').textContent = 'Full Vendor Response';
                document.getElementById('fullTextContent').innerHTML = `<p class="text-sm">${fullText}</p>`;

                const modal = new bootstrap.Modal(document.getElementById('fullTextModal'));
                modal.show();
            }
        </script>
    @endpush
@endsection
