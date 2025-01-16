@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Generate New QR Code</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('qrcode.store') }}" method="POST" role="form text-left">
                @csrf
                @if($errors->any())
                    <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
                        <span class="alert-text text-white">{{$errors->first()}}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vendor-select" class="form-control-label">Select Vendor</label>
                            <select class="form-control" id="vendor-select" name="vendor_id" required>
                                <option value="">Choose a vendor...</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->business_name }}</option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="service-select" class="form-control-label">Select Service/Menu</label>
                            <select class="form-control" id="service-select" name="service_id" required>
                                <option value="">Choose a service...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-vendor="{{ $service->vendor_id }}">
                                        {{ $service->name }} - RM{{ number_format($service->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="expiry-hours" class="form-control-label">QR Code Validity (Hours)</label>
                            <input class="form-control" type="number" id="expiry-hours" name="expiry_hours" min="1" value="24" required>
                            @error('expiry_hours')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('qrcode.index') }}" class="btn btn-light m-0 me-2">Cancel</a>
                    <button type="submit" class="btn bg-gradient-primary m-0">Generate QR Code</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('vendor-select').addEventListener('change', function() {
        const vendorId = this.value;
        const serviceSelect = document.getElementById('service-select');
        const serviceOptions = serviceSelect.querySelectorAll('option');
        
        // Reset service selection
        serviceSelect.value = '';
        
        // Show/hide services based on vendor
        serviceOptions.forEach(option => {
            if (option.value === '') return; // Skip placeholder option
            if (option.dataset.vendor === vendorId) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection