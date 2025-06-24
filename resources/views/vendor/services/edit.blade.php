@extends('layouts.user_type.vendor')

@section('page-title', 'Edit Service')
@section('page-heading', 'Edit ' . $service->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <form method="POST" action="{{ route('vendor.services.update', $service->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <p class="mb-0">Edit Service</p>
                                <button type="submit" class="btn btn-primary btn-sm ms-auto">Update Service</button>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Current Service Info --}}
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">{{ $service->name }}</h6>
                                        <p class="mb-1">Category: <strong>{{ $service->category }}</strong></p>
                                        <p class="mb-0">Current Status:
                                            <span
                                                class="badge bg-gradient-{{ $service->is_available ? 'success' : 'secondary' }}">
                                                {{ $service->is_available ? 'Available' : 'Unavailable' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <p class="mb-1"><strong>RM {{ number_format($service->price, 2) }}</strong></p>
                                        <small class="text-muted">{{ $service->preparation_time }} minutes prep</small>
                                    </div>
                                </div>
                            </div>

                            <p class="text-uppercase text-sm">Service Information</p>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Service Name *</label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text"
                                            name="name" id="name" value="{{ old('name', $service->name) }}"
                                            placeholder="e.g., Nasi Lemak Special" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category" class="form-control-label">Category *</label>
                                        <select class="form-control @error('category') is-invalid @enderror" name="category"
                                            id="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Main Course"
                                                {{ old('category', $service->category) == 'Main Course' ? 'selected' : '' }}>
                                                Main Course</option>
                                            <option value="Side Dish"
                                                {{ old('category', $service->category) == 'Side Dish' ? 'selected' : '' }}>
                                                Side Dish</option>
                                            <option value="Appetizer"
                                                {{ old('category', $service->category) == 'Appetizer' ? 'selected' : '' }}>
                                                Appetizer</option>
                                            <option value="Dessert"
                                                {{ old('category', $service->category) == 'Dessert' ? 'selected' : '' }}>
                                                Dessert</option>
                                            <option value="Beverage"
                                                {{ old('category', $service->category) == 'Beverage' ? 'selected' : '' }}>
                                                Beverage</option>
                                            <option value="Snack"
                                                {{ old('category', $service->category) == 'Snack' ? 'selected' : '' }}>
                                                Snack</option>
                                            <option value="Combo"
                                                {{ old('category', $service->category) == 'Combo' ? 'selected' : '' }}>
                                                Combo</option>
                                            <option value="Other"
                                                {{ old('category', $service->category) == 'Other' ? 'selected' : '' }}>
                                                Other</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price" class="form-control-label">Price (RM) *</label>
                                        <input class="form-control @error('price') is-invalid @enderror" type="number"
                                            name="price" id="price" min="0.01" max="999.99" step="0.01"
                                            value="{{ old('price', $service->price) }}" placeholder="0.00" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="preparation_time" class="form-control-label">Preparation Time (Minutes)
                                            *</label>
                                        <select class="form-control @error('preparation_time') is-invalid @enderror"
                                            name="preparation_time" id="preparation_time" required>
                                            <option value="">Select preparation time...</option>
                                            <option value="5"
                                                {{ old('preparation_time', $service->preparation_time) == '5' ? 'selected' : '' }}>
                                                5 minutes</option>
                                            <option value="10"
                                                {{ old('preparation_time', $service->preparation_time) == '10' ? 'selected' : '' }}>
                                                10 minutes</option>
                                            <option value="15"
                                                {{ old('preparation_time', $service->preparation_time) == '15' ? 'selected' : '' }}>
                                                15 minutes</option>
                                            <option value="20"
                                                {{ old('preparation_time', $service->preparation_time) == '20' ? 'selected' : '' }}>
                                                20 minutes</option>
                                            <option value="30"
                                                {{ old('preparation_time', $service->preparation_time) == '30' ? 'selected' : '' }}>
                                                30 minutes</option>
                                            <option value="45"
                                                {{ old('preparation_time', $service->preparation_time) == '45' ? 'selected' : '' }}>
                                                45 minutes</option>
                                            <option value="60"
                                                {{ old('preparation_time', $service->preparation_time) == '60' ? 'selected' : '' }}>
                                                1 hour</option>
                                            <option value="90"
                                                {{ old('preparation_time', $service->preparation_time) == '90' ? 'selected' : '' }}>
                                                1.5 hours</option>
                                            <option value="120"
                                                {{ old('preparation_time', $service->preparation_time) == '120' ? 'selected' : '' }}>
                                                2 hours</option>
                                        </select>
                                        @error('preparation_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_available" class="form-control-label">Availability</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_available"
                                                id="is_available" value="1"
                                                {{ old('is_available', $service->is_available) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_available">
                                                Service is available for orders
                                            </label>
                                        </div>
                                        <small class="text-muted">Uncheck if the service is temporarily unavailable</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="form-control-label">Description *</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description"
                                            rows="4"
                                            placeholder="Describe your service in detail... (ingredients, portion size, special features, etc.)" required>{{ old('description', $service->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">
                                            <span id="char-count">{{ strlen($service->description) }}</span>/500
                                            characters
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Service Preview --}}
                            <div id="service-preview">
                                <hr class="horizontal dark">
                                <p class="text-uppercase text-sm">Service Preview</p>
                                <div class="alert alert-primary">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-1" id="preview-name">{{ $service->name }}</h6>
                                            <p class="mb-1"><strong>Category:</strong> <span
                                                    id="preview-category">{{ $service->category }}</span></p>
                                            <p class="mb-1"><strong>Price:</strong> RM <span
                                                    id="preview-price">{{ number_format($service->price, 2) }}</span></p>
                                            <p class="mb-1"><strong>Prep Time:</strong> <span
                                                    id="preview-prep">{{ $service->preparation_time }}</span> minutes</p>
                                            <p class="mb-0"><strong>Status:</strong> <span id="preview-status"
                                                    class="badge bg-{{ $service->is_available ? 'success' : 'secondary' }}">{{ $service->is_available ? 'Available' : 'Unavailable' }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small class="text-muted">This is how your updated service will appear to
                                                customers</small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <p class="mb-0"><strong>Description:</strong></p>
                                        <p class="text-sm mb-0" id="preview-description">{{ $service->description }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Update History --}}
                            <hr class="horizontal dark">
                            <p class="text-uppercase text-sm">Update Information</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Created Date</label>
                                        <input class="form-control" type="text"
                                            value="{{ $service->created_at->format('M j, Y \a\t H:i') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Last Updated</label>
                                        <input class="form-control" type="text"
                                            value="{{ $service->updated_at->format('M j, Y \a\t H:i') }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Information Panel --}}
            <div class="col-md-4">
                {{-- Current Service Stats --}}
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Current Service Performance</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Total Orders:</span>
                            <span class="text-sm font-weight-bold">{{ number_format($service->total_orders ?? 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Total Revenue:</span>
                            <span class="text-sm font-weight-bold">RM
                                {{ number_format($service->total_revenue ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm">Active QR Codes:</span>
                            <span
                                class="text-sm font-weight-bold">{{ $service->qrCodes->where('status', 'ACTIVE')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-sm">Last Order:</span>
                            <span class="text-sm font-weight-bold">
                                @if ($service->transactions()->exists())
                                    {{ $service->transactions()->latest('transaction_date')->first()->transaction_date->diffForHumans() }}
                                @else
                                    Never
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Update Tips --}}
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6>Update Tips</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-info shadow border-radius-md me-3">
                                <i class="ni ni-bulb-61 text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Price Changes</h6>
                                <p class="text-sm mb-0">Price updates will only affect new QR codes. Existing QR codes will
                                    keep the original price.</p>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-warning shadow border-radius-md me-3">
                                <i class="ni ni-time-alarm text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Preparation Time</h6>
                                <p class="text-sm mb-0">Realistic prep times help set proper customer expectations and
                                    reduce complaints.</p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="icon icon-shape bg-gradient-success shadow border-radius-md me-3">
                                <i class="ni ni-check-bold text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Availability Status</h6>
                                <p class="text-sm mb-0">Toggle availability to temporarily disable orders without affecting
                                    existing QR codes.</p>
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
                        <a href="{{ route('vendor.services.show', $service->id) }}"
                            class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-eye me-2"></i>View Service Details
                        </a>
                        <a href="{{ route('vendor.qrcodes.create') }}?service_id={{ $service->id }}"
                            class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-qrcode me-2"></i>Generate QR Code
                        </a>
                        <form method="POST" action="{{ route('vendor.services.toggle-availability', $service->id) }}"
                            class="mb-2">
                            @csrf
                            <button type="submit"
                                class="btn btn-outline-{{ $service->is_available ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-{{ $service->is_available ? 'pause' : 'play' }} me-2"></i>
                                {{ $service->is_available ? 'Mark Unavailable' : 'Mark Available' }}
                            </button>
                        </form>
                        <a href="{{ route('vendor.services.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Back to Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Form elements
                const nameInput = document.getElementById('name');
                const categorySelect = document.getElementById('category');
                const priceInput = document.getElementById('price');
                const prepTimeSelect = document.getElementById('preparation_time');
                const availableCheck = document.getElementById('is_available');
                const descriptionTextarea = document.getElementById('description');

                // Preview elements
                const previewName = document.getElementById('preview-name');
                const previewCategory = document.getElementById('preview-category');
                const previewPrice = document.getElementById('preview-price');
                const previewPrep = document.getElementById('preview-prep');
                const previewStatus = document.getElementById('preview-status');
                const previewDescription = document.getElementById('preview-description');
                const charCount = document.getElementById('char-count');

                // Update character count
                descriptionTextarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                    updatePreview();
                });

                // Update preview when form fields change
                [nameInput, categorySelect, priceInput, prepTimeSelect, availableCheck, descriptionTextarea].forEach(
                    element => {
                        element.addEventListener('input', updatePreview);
                        element.addEventListener('change', updatePreview);
                    });

                function updatePreview() {
                    previewName.textContent = nameInput.value || 'Service Name';
                    previewCategory.textContent = categorySelect.value || '-';
                    previewPrice.textContent = priceInput.value || '0.00';
                    previewPrep.textContent = prepTimeSelect.value || '0';
                    previewDescription.textContent = descriptionTextarea.value || '-';

                    // Update status badge
                    if (availableCheck.checked) {
                        previewStatus.textContent = 'Available';
                        previewStatus.className = 'badge bg-success';
                    } else {
                        previewStatus.textContent = 'Unavailable';
                        previewStatus.className = 'badge bg-secondary';
                    }
                }

                // Initialize character count
                charCount.textContent = descriptionTextarea.value.length;
            });
        </script>
    @endpush
@endsection
