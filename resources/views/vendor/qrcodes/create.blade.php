@extends('layouts.user_type.vendor')

@section('page-title', 'Generate QR Code')
@section('page-heading', 'Generate New QR Code')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <form method="POST" action="{{ route('vendor.qrcodes.store') }}">
                        @csrf

                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <p class="mb-0">Generate QR Code</p>
                                <button type="submit" class="btn btn-primary btn-sm ms-auto">Generate QR Code</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-uppercase text-sm">QR Code Configuration</p>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="service_id" class="form-control-label">Select Service *</label>
                                        <select class="form-control @error('service_id') is-invalid @enderror"
                                            name="service_id" id="service_id" required>
                                            <option value="">Choose a service...</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}"
                                                    {{ old('service_id', request('service_id')) == $service->id ? 'selected' : '' }}
                                                    data-price="{{ $service->price }}"
                                                    data-prep-time="{{ $service->preparation_time }}">
                                                    {{ $service->name }} - RM {{ number_format($service->price, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('service_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Select the service for which you want to generate QR
                                            codes</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expiry_hours" class="form-control-label">Expiry Time (Hours) *</label>
                                        <select class="form-control @error('expiry_hours') is-invalid @enderror"
                                            name="expiry_hours" id="expiry_hours" required>
                                            <option value="">Select expiry time...</option>
                                            <option value="1" {{ old('expiry_hours') == '1' ? 'selected' : '' }}>1 Hour
                                            </option>
                                            <option value="2" {{ old('expiry_hours') == '2' ? 'selected' : '' }}>2
                                                Hours</option>
                                            <option value="4" {{ old('expiry_hours') == '4' ? 'selected' : '' }}>4
                                                Hours</option>
                                            <option value="8" {{ old('expiry_hours') == '8' ? 'selected' : '' }}>8
                                                Hours</option>
                                            <option value="12" {{ old('expiry_hours') == '12' ? 'selected' : '' }}>12
                                                Hours</option>
                                            <option value="24" {{ old('expiry_hours') == '24' ? 'selected' : '' }}>1
                                                Day</option>
                                            <option value="48" {{ old('expiry_hours') == '48' ? 'selected' : '' }}>2
                                                Days</option>
                                            <option value="72" {{ old('expiry_hours') == '72' ? 'selected' : '' }}>3
                                                Days</option>
                                            <option value="168" {{ old('expiry_hours') == '168' ? 'selected' : '' }}>1
                                                Week</option>
                                        </select>
                                        @error('expiry_hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">QR codes will automatically expire after this time</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity" class="form-control-label">Quantity *</label>
                                        <select class="form-control @error('quantity') is-invalid @enderror" name="quantity"
                                            id="quantity" required>
                                            <option value="">Select quantity...</option>
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('quantity') == $i ? 'selected' : '' }}>
                                                    {{ $i }} QR Code{{ $i > 1 ? 's' : '' }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Maximum 10 QR codes can be generated at once</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Service Details Preview --}}
                            <div id="service-preview" class="d-none">
                                <hr class="horizontal dark">
                                <p class="text-uppercase text-sm">Service Preview</p>
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-1" id="preview-name">Service Name</h6>
                                            <p class="mb-1">Price: <strong id="preview-price">RM 0.00</strong></p>
                                            <p class="mb-0">Preparation Time: <strong id="preview-prep-time">0
                                                    minutes</strong></p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small class="text-muted">QR codes for this service will allow customers to
                                                place orders directly</small>
                                        </div>
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
                        <h6>QR Code Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-primary shadow border-radius-md me-3">
                                <i class="ni ni-image text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">How QR Codes Work</h6>
                                <p class="text-sm mb-0">Customers scan QR codes to place orders for your services directly
                                    through the mobile app.</p>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-success shadow border-radius-md me-3">
                                <i class="ni ni-time-alarm text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Expiry Management</h6>
                                <p class="text-sm mb-0">Set appropriate expiry times based on your service availability and
                                    demand patterns.</p>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="icon icon-shape bg-gradient-info shadow border-radius-md me-3">
                                <i class="ni ni-bulb-61 text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Best Practices</h6>
                                <p class="text-sm mb-0">Generate QR codes during peak hours, use shorter expiry times for
                                    limited offers.</p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="icon icon-shape bg-gradient-warning shadow border-radius-md me-3">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Track Performance</h6>
                                <p class="text-sm mb-0">Monitor QR code usage and customer interactions in your dashboard
                                    analytics.</p>
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
                        <a href="{{ route('vendor.qrcodes.index') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="ni ni-collection me-2"></i>View All QR Codes
                        </a>
                        <a href="{{ route('vendor.services.index') }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="ni ni-app me-2"></i>Manage Services
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
                const serviceSelect = document.getElementById('service_id');
                const servicePreview = document.getElementById('service-preview');
                const previewName = document.getElementById('preview-name');
                const previewPrice = document.getElementById('preview-price');
                const previewPrepTime = document.getElementById('preview-prep-time');

                serviceSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];

                    if (this.value) {
                        const serviceName = selectedOption.text.split(' - RM')[0];
                        const price = selectedOption.dataset.price;
                        const prepTime = selectedOption.dataset.prepTime;

                        previewName.textContent = serviceName;
                        previewPrice.textContent = 'RM ' + parseFloat(price).toFixed(2);
                        previewPrepTime.textContent = prepTime + ' minutes';

                        servicePreview.classList.remove('d-none');
                    } else {
                        servicePreview.classList.add('d-none');
                    }
                });

                // Trigger change event if service is pre-selected
                if (serviceSelect.value) {
                    serviceSelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush
@endsection
