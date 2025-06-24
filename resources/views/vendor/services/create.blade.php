@extends('layouts.user_type.vendor')

@section('page-title', 'Create Service')
@section('page-heading', 'Add New Service')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <form method="POST" action="{{ route('vendor.services.store') }}">
                        @csrf

                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <p class="mb-0">Create New Service</p>
                                <button type="submit" class="btn btn-primary btn-sm ms-auto">Create Service</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-uppercase text-sm">Service Information</p>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Service Name *</label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text"
                                            name="name" id="name" value="{{ old('name') }}"
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
                                                {{ old('category') == 'Main Course' ? 'selected' : '' }}>Main Course
                                            </option>
                                            <option value="Side Dish"
                                                {{ old('category') == 'Side Dish' ? 'selected' : '' }}>Side Dish</option>
                                            <option value="Appetizer"
                                                {{ old('category') == 'Appetizer' ? 'selected' : '' }}>Appetizer</option>
                                            <option value="Dessert" {{ old('category') == 'Dessert' ? 'selected' : '' }}>
                                                Dessert</option>
                                            <option value="Beverage" {{ old('category') == 'Beverage' ? 'selected' : '' }}>
                                                Beverage</option>
                                            <option value="Snack" {{ old('category') == 'Snack' ? 'selected' : '' }}>Snack
                                            </option>
                                            <option value="Combo" {{ old('category') == 'Combo' ? 'selected' : '' }}>Combo
                                            </option>
                                            <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other
                                            </option>
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
                                            value="{{ old('price') }}" placeholder="0.00" required>
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
                                            <option value="5" {{ old('preparation_time') == '5' ? 'selected' : '' }}>5
                                                minutes</option>
                                            <option value="10" {{ old('preparation_time') == '10' ? 'selected' : '' }}>
                                                10 minutes</option>
                                            <option value="15" {{ old('preparation_time') == '15' ? 'selected' : '' }}>
                                                15 minutes</option>
                                            <option value="20" {{ old('preparation_time') == '20' ? 'selected' : '' }}>
                                                20 minutes</option>
                                            <option value="30" {{ old('preparation_time') == '30' ? 'selected' : '' }}>
                                                30 minutes</option>
                                            <option value="45" {{ old('preparation_time') == '45' ? 'selected' : '' }}>
                                                45 minutes</option>
                                            <option value="60" {{ old('preparation_time') == '60' ? 'selected' : '' }}>
                                                1 hour</option>
                                            <option value="90" {{ old('preparation_time') == '90' ? 'selected' : '' }}>
                                                1.5 hours</option>
                                            <option value="120"
                                                {{ old('preparation_time') == '120' ? 'selected' : '' }}>2 hours</option>
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
                                                {{ old('is_available', true) ? 'checked' : '' }}>
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
                                            rows="4" placeholder="Describe your service in detail... (ingredients, portion size, special features, etc.)"
                                            required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">
                                            <span id="char-count">0</span>/500 characters
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Service Preview --}}
                            <div id="service-preview" class="d-none">
                                <hr class="horizontal dark">
                                <p class="text-uppercase text-sm">Service Preview</p>
                                <div class="alert alert-primary">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-1" id="preview-name">Service Name</h6>
                                            <p class="mb-1"><strong>Category:</strong> <span
                                                    id="preview-category">-</span></p>
                                            <p class="mb-1"><strong>Price:</strong> RM <span
                                                    id="preview-price">0.00</span></p>
                                            <p class="mb-1"><strong>Prep Time:</strong> <span id="preview-prep">0</span>
                                                minutes</p>
                                            <p class="mb-0"><strong>Status:</strong> <span id="preview-status"
                                                    class="badge bg-success">Available</span></p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small class="text-muted">This is how your service will appear to
                                                customers</small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <p class="mb-0"><strong>Description:</strong></p>
                                        <p class="text-sm mb-0" id="preview-description">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Information Panel --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Service Creation Tips</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-primary shadow border-radius-md me-3">
                                <i class="ni ni-bulb-61 text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Clear Descriptions</h6>
                                <p class="text-sm mb-0">Include ingredients, portion sizes, and any special features to
                                    help customers make informed choices.</p>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-success shadow border-radius-md me-3">
                                <i class="ni ni-money-coins text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Competitive Pricing</h6>
                                <p class="text-sm mb-0">Research similar services in your area and price competitively
                                    while ensuring profitability.</p>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-info shadow border-radius-md me-3">
                                <i class="ni ni-time-alarm text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Realistic Prep Times</h6>
                                <p class="text-sm mb-0">Set accurate preparation times to manage customer expectations and
                                    avoid disappointment.</p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="icon icon-shape bg-gradient-warning shadow border-radius-md me-3">
                                <i class="ni ni-image text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">QR Code Generation</h6>
                                <p class="text-sm mb-0">After creating your service, you can generate QR codes for easy
                                    customer ordering.</p>
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
                        <a href="{{ route('vendor.services.index') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="ni ni-collection me-2"></i>View All Services
                        </a>
                        <a href="{{ route('vendor.qrcodes.index') }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="ni ni-image me-2"></i>Manage QR Codes
                        </a>
                        <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary w-100">
                            <i class="ni ni-tv-2 me-2"></i>Back to Dashboard
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
                const servicePreview = document.getElementById('service-preview');
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
                    const hasContent = nameInput.value || categorySelect.value || priceInput.value ||
                        descriptionTextarea.value;

                    if (hasContent) {
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

                        servicePreview.classList.remove('d-none');
                    } else {
                        servicePreview.classList.add('d-none');
                    }
                }

                // Initialize character count
                charCount.textContent = descriptionTextarea.value.length;
            });
        </script>
    @endpush
@endsection
