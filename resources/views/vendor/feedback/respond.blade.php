@extends('layouts.user_type.vendor')

@section('content')
    <div class="container-fluid py-4">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">
                                @if ($review->vendor_response)
                                    Edit Response to Review
                                @else
                                    Respond to Review
                                @endif
                            </h6>
                            <a href="{{ route('vendor.feedback.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Feedback
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Review Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Original Review</h6>
                    </div>
                    <div class="card-body">
                        <!-- Student Info -->
                        <div class="d-flex align-items-center mb-4">
                            @if ($review->student && $review->student->user && $review->student->user->profile_picture_url)
                                <img src="{{ $review->student->user->profile_picture_url }}" alt="student profile"
                                    class="avatar avatar-lg me-3">
                            @else
                                <div class="avatar avatar-lg bg-gradient-secondary me-3">
                                    <i class="ni ni-single-02"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $review->student->user->name ?? 'Anonymous' }}</h6>
                                <p class="text-sm text-secondary mb-1">{{ $review->student->matrix_no ?? 'N/A' }}</p>
                                <p class="text-xs text-muted mb-0">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ $review->review_date->format('M d, Y \a\t H:i') }}
                                </p>
                            </div>
                            <div class="text-end">
                                <!-- Rating Stars -->
                                <div class="rating-stars mb-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->stars)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="fas fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span
                                    class="badge badge-sm 
                                @if ($review->stars >= 4) bg-gradient-success
                                @elseif($review->stars >= 3) bg-gradient-warning
                                @else bg-gradient-danger @endif">
                                    {{ $review->stars }}/5 Stars
                                </span>
                            </div>
                        </div>

                        <!-- Review Comment -->
                        <div class="review-comment p-3 bg-gray-100 border-radius-md">
                            @if ($review->review_comment)
                                <p class="mb-0">"{{ $review->review_comment }}"</p>
                            @else
                                <p class="text-muted fst-italic mb-0">No comment provided with this review.</p>
                            @endif
                        </div>

                        <!-- Current Response (if exists) -->
                        @if ($review->vendor_response)
                            <div class="current-response mt-4">
                                <h6 class="text-primary">Your Current Response:</h6>
                                <div class="p-3 bg-primary-light border-radius-md border-start border-primary border-3">
                                    <p class="mb-2">{{ $review->vendor_response }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Responded on {{ $review->response_date->format('M d, Y \a\t H:i') }}
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Response Form -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>
                            @if ($review->vendor_response)
                                Edit Your Response
                            @else
                                Write Your Response
                            @endif
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($review->vendor_response)
                            <!-- Update Response Form -->
                            <form role="form" method="POST"
                                action="{{ route('vendor.feedback.updateResponse', $review->id) }}">
                                @csrf
                                @method('PUT')
                            @else
                                <!-- New Response Form -->
                                <form role="form" method="POST"
                                    action="{{ route('vendor.feedback.storeResponse', $review->id) }}">
                                    @csrf
                        @endif

                        <div class="mb-3">
                            <label for="vendor_response" class="form-label">
                                Your Response <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('vendor_response') is-invalid @enderror" name="vendor_response"
                                id="vendor_response" rows="6" placeholder="Write a professional and helpful response to this review..."
                                maxlength="1000" required>{{ old('vendor_response', $review->vendor_response) }}</textarea>
                            <div class="form-text">
                                <span id="charCount">0</span>/1000 characters
                            </div>
                            @error('vendor_response')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Response Guidelines -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Response Tips:</h6>
                            <ul class="mb-0 small">
                                <li>Thank the customer for their feedback</li>
                                <li>Address specific concerns mentioned</li>
                                <li>Keep it professional and courteous</li>
                                <li>Offer solutions or improvements</li>
                                <li>Invite them to return or contact you directly</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn bg-gradient-primary">
                                <i class="fas fa-reply me-2"></i>
                                @if ($review->vendor_response)
                                    Update Response
                                @else
                                    Submit Response
                                @endif
                            </button>

                            @if ($review->vendor_response)
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteResponse()">
                                    <i class="fas fa-trash me-2"></i>
                                    Delete Response
                                </button>
                            @endif
                        </div>
                        </form>

                        @if ($review->vendor_response)
                            <!-- Delete Response Form (hidden) -->
                            <form id="deleteForm" method="POST"
                                action="{{ route('vendor.feedback.deleteResponse', $review->id) }}" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Review History (if multiple reviews from same student) -->
        @if ($review->student)
            @php
                $otherReviews = App\Models\Rating::where('vendor_id', Auth::user()->vendor->id)
                    ->where('student_id', $review->student->id)
                    ->where('id', '!=', $review->id)
                    ->orderBy('review_date', 'desc')
                    ->take(3)
                    ->get();
            @endphp

            @if ($otherReviews->count() > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h6>Other Reviews from {{ $review->student->user->name ?? 'This Student' }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($otherReviews as $otherReview)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <small
                                                            class="text-muted">{{ $otherReview->review_date->format('M d, Y') }}</small>
                                                        <div class="rating-stars">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= $otherReview->stars)
                                                                    <i class="fas fa-star text-warning text-xs"></i>
                                                                @else
                                                                    <i class="fas fa-star text-muted text-xs"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    @if ($otherReview->review_comment)
                                                        <p class="text-sm mb-0">
                                                            {{ Str::limit($otherReview->review_comment, 80) }}</p>
                                                    @else
                                                        <p class="text-sm text-muted fst-italic mb-0">No comment</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <script>
        // Character counter
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('vendor_response');
            const charCount = document.getElementById('charCount');

            function updateCharCount() {
                const currentLength = textarea.value.length;
                charCount.textContent = currentLength;

                if (currentLength > 900) {
                    charCount.classList.add('text-warning');
                } else if (currentLength > 950) {
                    charCount.classList.remove('text-warning');
                    charCount.classList.add('text-danger');
                } else {
                    charCount.classList.remove('text-warning', 'text-danger');
                }
            }

            textarea.addEventListener('input', updateCharCount);
            updateCharCount(); // Initial count
        });

        // Confirm delete response
        function confirmDeleteResponse() {
            if (confirm('Are you sure you want to delete your response? This action cannot be undone.')) {
                document.getElementById('deleteForm').submit();
            }
        }

        // Auto-save draft (optional enhancement)
        let autoSaveTimeout;
        document.getElementById('vendor_response').addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(function() {
                // Here you could implement auto-save functionality
                console.log('Auto-saving draft...');
            }, 2000);
        });
    </script>

    <style>
        .bg-primary-light {
            background-color: rgba(94, 114, 228, 0.1) !important;
        }

        .rating-stars i {
            margin-right: 2px;
        }

        .review-comment {
            position: relative;
        }

        .review-comment::before {
            content: '"';
            font-size: 2rem;
            color: #dee2e6;
            position: absolute;
            top: -10px;
            left: 10px;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .form-control:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
        }

        .alert-info {
            border-left: 4px solid #17a2b8;
        }

        .alert-info ul {
            padding-left: 1rem;
        }
    </style>
@endsection
