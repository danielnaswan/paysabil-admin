@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Edit QR Code</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('qrcode.update', $qrCode->id) }}" method="POST" role="form text-left">
                @csrf
                @method('PUT')
                
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
                            <label class="form-control-label">Vendor</label>
                            <input class="form-control" type="text" value="{{ $qrCode->vendor->business_name }}" disabled>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Service/Menu</label>
                            <input class="form-control" type="text" value="{{ $qrCode->service->name }} - RM{{ number_format($qrCode->service->price, 2) }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-control-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="ACTIVE" {{ $qrCode->status === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                <option value="EXPIRED" {{ $qrCode->status === 'EXPIRED' ? 'selected' : '' }}>Expired</option>
                                <option value="USED" {{ $qrCode->status === 'USED' ? 'selected' : '' }}>Used</option>
                                <option value="INVALID" {{ $qrCode->status === 'INVALID' ? 'selected' : '' }}>Invalid</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="expiry-hours" class="form-control-label">Extend Validity (Hours from Generation)</label>
                            <input class="form-control" type="number" id="expiry-hours" name="expiry_hours" min="1" value="24" required>
                            <small class="text-muted">Current expiry: {{ $qrCode->expiry_date->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('qrcode.index') }}" class="btn btn-light m-0 me-2">Cancel</a>
                    <button type="submit" class="btn bg-gradient-primary m-0">Update QR Code</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection