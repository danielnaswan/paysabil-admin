@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0 px-3">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0">Generate New QR Code</h6>
                    <a href="{{ route('qrcode.index') }}" class="btn btn-outline-primary btn-sm ms-auto">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
            <div class="card-body pt-4 p-3">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-text text-white">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif

                <form action="{{ route('qrcode.store') }}" method="POST" role="form" id="qrCodeForm">
                    @csrf

                    <!-- Vendor and Service Selection -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vendor-select" class="form-control-label">
                                    <i class="fas fa-store me-1"></i>Select Vendor *
                                </label>
                                <select class="form-control" id="vendor-select" name="vendor_id" required>
                                    <option value="">Choose a vendor...</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}"
                                            data-services-count="{{ $vendor->services->count() }}"
                                            {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->business_name }} ({{ $vendor->service_category }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                                <small class="form-text text-muted">
                                    Select the vendor for this QR code
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="service-select" class="form-control-label">
                                    <i class="fas fa-utensils me-1"></i>Select Service/Menu *
                                </label>
                                <select class="form-control" id="service-select" name="service_id" required disabled>
                                    <option value="">First select a vendor...</option>
                                </select>
                                @error('service_id')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                                <small class="form-text text-muted">
                                    Available services will appear after selecting a vendor
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Validity and Template Selection -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expiry-hours" class="form-control-label">
                                    <i class="fas fa-clock me-1"></i>QR Code Validity (Hours) *
                                </label>
                                <select class="form-control" id="expiry-hours" name="expiry_hours" required>
                                    <option value="1" {{ old('expiry_hours') == '1' ? 'selected' : '' }}>1 Hour
                                    </option>
                                    <option value="6" {{ old('expiry_hours') == '6' ? 'selected' : '' }}>6 Hours
                                    </option>
                                    <option value="12" {{ old('expiry_hours') == '12' ? 'selected' : '' }}>12 Hours
                                    </option>
                                    <option value="24" {{ old('expiry_hours', '24') == '24' ? 'selected' : '' }}>1 Day
                                        (24 Hours)</option>
                                    <option value="48" {{ old('expiry_hours') == '48' ? 'selected' : '' }}>2 Days (48
                                        Hours)</option>
                                    <option value="72" {{ old('expiry_hours') == '72' ? 'selected' : '' }}>3 Days (72
                                        Hours)</option>
                                    <option value="168" {{ old('expiry_hours') == '168' ? 'selected' : '' }}>1 Week (168
                                        Hours)</option>
                                </select>
                                @error('expiry_hours')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                                <small class="form-text text-muted">
                                    QR code will expire after this duration
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="template-select" class="form-control-label">
                                    <i class="fas fa-palette me-1"></i>QR Code Template
                                </label>
                                <select class="form-control" id="template-select" name="template">
                                    @if (isset($templates))
                                        @foreach ($templates as $key => $template)
                                            <option value="{{ $key }}"
                                                data-description="{{ $template['description'] }}"
                                                {{ old('template', 'default') == $key ? 'selected' : '' }}>
                                                {{ $template['name'] }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="default" selected>Default Template</option>
                                        <option value="modern">Modern Template</option>
                                        <option value="elegant">Elegant Template</option>
                                        <option value="minimal">Minimal Template</option>
                                    @endif
                                </select>
                                <small class="form-text text-muted" id="template-description">
                                    Clean and professional design
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Template Preview Section -->
                    @if (isset($templates))
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="mb-3">
                                    <i class="fas fa-eye me-1"></i>Template Preview
                                </h6>
                                <div class="row" id="template-previews">
                                    @foreach ($templates as $key => $template)
                                        <div class="col-md-3 mb-3 template-preview" data-template="{{ $key }}"
                                            style="{{ $key === 'default' ? '' : 'display: none;' }}">
                                            <div class="card template-card {{ $key === 'default' ? 'border-primary' : '' }}"
                                                style="cursor: pointer;" onclick="selectTemplate('{{ $key }}')">
                                                <div class="card-body text-center p-3">
                                                    <div class="template-preview-box mb-2"
                                                        style="height: 120px; background: {{ $key === 'default' ? '#f8f9fa' : ($key === 'modern' ? 'linear-gradient(45deg, #667eea, #764ba2)' : ($key === 'elegant' ? '#2c3e50' : '#ffffff')) }}; 
                                                        border: 1px solid #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                        <div
                                                            style="width: 60px; height: 60px; background: #000; border-radius: 4px; position: relative;">
                                                            <div
                                                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 8px;">
                                                                QR</div>
                                                        </div>
                                                    </div>
                                                    <h6 class="mb-1 text-sm">{{ $template['name'] }}</h6>
                                                    <small class="text-muted">{{ $template['description'] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Service Details Preview -->
                    <div class="row mt-4" id="service-preview" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="mb-2">
                                    <i class="fas fa-info-circle me-1"></i>QR Code Preview
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Vendor:</strong> <span id="preview-vendor">-</span><br>
                                        <strong>Service:</strong> <span id="preview-service">-</span><br>
                                        <strong>Category:</strong> <span id="preview-category">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Price:</strong> <span id="preview-price">-</span><br>
                                        <strong>Valid Until:</strong> <span id="preview-expiry">-</span><br>
                                        <strong>Template:</strong> <span id="preview-template">Default</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('qrcode.index') }}" class="btn btn-light m-0 me-2">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn bg-gradient-primary m-0" id="submit-btn" disabled>
                            <i class="fas fa-qrcode me-1"></i>Generate QR Code
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <h6>Generating QR Code...</h6>
                    <p class="text-muted mb-0">Please wait while we create your QR code</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const vendorSelect = document.getElementById('vendor-select');
                const serviceSelect = document.getElementById('service-select');
                const expirySelect = document.getElementById('expiry-hours');
                const templateSelect = document.getElementById('template-select');
                const submitBtn = document.getElementById('submit-btn');
                const servicePreview = document.getElementById('service-preview');
                const form = document.getElementById('qrCodeForm');

                let services = {};
                let selectedVendor = null;
                let selectedService = null;

                // Handle vendor selection
                vendorSelect.addEventListener('change', function() {
                    const vendorId = this.value;

                    if (vendorId) {
                        // Enable service select and show loading
                        serviceSelect.disabled = false;
                        serviceSelect.innerHTML = '<option value="">Loading services...</option>';

                        // Store selected vendor info
                        selectedVendor = {
                            id: vendorId,
                            name: vendorSelect.options[vendorSelect.selectedIndex].text.split(' (')[0]
                        };

                        // Fetch services for the selected vendor
                        fetch(`/qrcode/vendor/${vendorId}/services`, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success === true && Array.isArray(data.services)) {
                                    // Store services data
                                    services[vendorId] = data.services;

                                    // Update service dropdown
                                    updateServiceOptions(vendorId);
                                } else {
                                    throw new Error(data.message || 'Invalid response format');
                                }
                            })
                            .catch(error => {
                                console.error('Error loading services:', error);

                                // Try static fallback using vendors data
                                tryStaticFallback(vendorId);
                            });

                    } else {
                        // No vendor selected - reset service dropdown
                        serviceSelect.disabled = true;
                        serviceSelect.innerHTML = '<option value="">First select a vendor...</option>';
                        selectedVendor = null;
                        selectedService = null;
                        hidePreview();
                    }

                    validateForm();
                });

                // Static fallback function
                function tryStaticFallback(vendorId) {
                    try {
                        const vendorData = @json($vendors);
                        const selectedVendorData = vendorData.find(v => v.id == vendorId);

                        if (selectedVendorData && selectedVendorData.services && selectedVendorData.services.length >
                            0) {
                            serviceSelect.innerHTML = '<option value="">Choose service...</option>';

                            selectedVendorData.services.forEach(service => {
                                if (service.is_available !== false) {
                                    const option = document.createElement('option');
                                    option.value = service.id;

                                    const price = parseFloat(service.price || 0).toFixed(2);
                                    option.textContent = `${service.name} - RM ${price}`;
                                    if (service.category) {
                                        option.textContent += ` (${service.category})`;
                                    }

                                    option.dataset.price = service.price;
                                    option.dataset.category = service.category || '';
                                    option.dataset.name = service.name;

                                    serviceSelect.appendChild(option);
                                }
                            });

                            services[vendorId] = selectedVendorData.services;
                            serviceSelect.disabled = false;
                        } else {
                            serviceSelect.innerHTML = '<option value="">No services available</option>';
                            serviceSelect.disabled = true;
                        }
                    } catch (e) {
                        serviceSelect.innerHTML = '<option value="">Failed to load services</option>';
                        serviceSelect.disabled = true;
                    }
                }

                // Handle service selection
                serviceSelect.addEventListener('change', function() {
                    const serviceId = this.value;

                    if (serviceId && selectedVendor && services[selectedVendor.id]) {
                        const service = services[selectedVendor.id].find(s => s.id == serviceId);

                        if (service) {
                            selectedService = service;
                            updatePreview();
                        } else {
                            selectedService = null;
                            hidePreview();
                        }
                    } else {
                        selectedService = null;
                        hidePreview();
                    }

                    validateForm();
                });

                // Handle expiry hours change
                expirySelect.addEventListener('change', updatePreview);

                // Handle template selection
                templateSelect.addEventListener('change', function() {
                    const selectedTemplate = this.value;
                    const description = this.options[this.selectedIndex].dataset.description ||
                        'Template design';

                    document.getElementById('template-description').textContent = description;

                    // Update template cards if they exist
                    const templatePreviews = document.querySelectorAll('.template-preview');
                    if (templatePreviews.length > 0) {
                        templatePreviews.forEach(preview => {
                            preview.style.display = 'none';
                            preview.querySelector('.template-card').classList.remove('border-primary');
                        });

                        const selectedPreview = document.querySelector(`[data-template="${selectedTemplate}"]`);
                        if (selectedPreview) {
                            selectedPreview.style.display = 'block';
                            selectedPreview.querySelector('.template-card').classList.add('border-primary');
                        }
                    }

                    updatePreview();
                });

                // Update service options
                function updateServiceOptions(vendorId) {
                    const vendorServices = services[vendorId] || [];

                    serviceSelect.innerHTML = '<option value="">Choose service...</option>';

                    if (vendorServices.length === 0) {
                        serviceSelect.innerHTML = '<option value="">No services available for this vendor</option>';
                        serviceSelect.disabled = true;
                        return;
                    }

                    vendorServices.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.id;

                        const price = parseFloat(service.price || 0).toFixed(2);
                        option.textContent = `${service.name} - RM ${price}`;
                        if (service.category) {
                            option.textContent += ` (${service.category})`;
                        }

                        option.dataset.price = service.price;
                        option.dataset.category = service.category || '';
                        option.dataset.name = service.name;

                        serviceSelect.appendChild(option);
                    });

                    serviceSelect.disabled = false;

                    // Restore selected value if exists (for validation errors)
                    const oldServiceId = '{{ old('service_id') }}';
                    if (oldServiceId && serviceSelect.querySelector(`option[value="${oldServiceId}"]`)) {
                        serviceSelect.value = oldServiceId;
                        if (serviceSelect.value) {
                            const service = vendorServices.find(s => s.id == oldServiceId);
                            selectedService = service;
                            updatePreview();
                        }
                    }
                }

                // Update preview
                function updatePreview() {
                    if (selectedVendor && selectedService) {
                        const expiryHours = parseInt(expirySelect.value);
                        const expiryDate = new Date();
                        expiryDate.setHours(expiryDate.getHours() + expiryHours);

                        document.getElementById('preview-vendor').textContent = selectedVendor.name;
                        document.getElementById('preview-service').textContent = selectedService.name;
                        document.getElementById('preview-category').textContent = selectedService.category || 'N/A';
                        document.getElementById('preview-price').textContent =
                            `RM ${parseFloat(selectedService.price).toFixed(2)}`;
                        document.getElementById('preview-expiry').textContent = expiryDate.toLocaleString();
                        document.getElementById('preview-template').textContent = templateSelect.options[templateSelect
                            .selectedIndex].text;

                        servicePreview.style.display = 'block';
                    }
                }

                // Hide preview
                function hidePreview() {
                    servicePreview.style.display = 'none';
                }

                // Validate form
                function validateForm() {
                    const isValid = vendorSelect.value && serviceSelect.value && expirySelect.value;
                    submitBtn.disabled = !isValid;
                }

                // Form submission
                form.addEventListener('submit', function(e) {
                    // Validate one more time
                    if (!selectedVendor || !selectedService) {
                        e.preventDefault();
                        alert('Please select both vendor and service');
                        return false;
                    }

                    // Show loading modal
                    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
                    loadingModal.show();

                    // Disable submit button to prevent double submission
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Generating...';
                });

                // Initialize validation
                validateForm();

                // Trigger vendor change if old value exists (for validation errors)
                if (vendorSelect.value) {
                    vendorSelect.dispatchEvent(new Event('change'));
                }

                // Initialize template description
                if (templateSelect.options[templateSelect.selectedIndex].dataset.description) {
                    document.getElementById('template-description').textContent =
                        templateSelect.options[templateSelect.selectedIndex].dataset.description;
                }
            });

            // Template selection function for onclick events
            function selectTemplate(templateKey) {
                const templateSelect = document.getElementById('template-select');
                templateSelect.value = templateKey;
                templateSelect.dispatchEvent(new Event('change'));
            }
        </script>
    @endpush

    @push('styles')
        <style>
            .template-card {
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .template-card:hover {
                border-color: #3498db !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .template-preview-box {
                position: relative;
                overflow: hidden;
            }

            .form-control:focus {
                border-color: #3498db;
                box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            }

            .alert-info {
                background-color: #e8f4fd;
                border-color: #bee5eb;
                color: #0c5460;
            }

            #service-preview {
                animation: fadeIn 0.3s ease-in;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .spinner-border {
                width: 3rem;
                height: 3rem;
            }
        </style>
    @endpush
@endsection
