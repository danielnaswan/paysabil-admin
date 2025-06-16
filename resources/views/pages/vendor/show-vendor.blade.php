@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Vendor Details</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <div class="row">
                <div class="col-md-4">
                    @if($vendor->user->profile_picture_url)
                        <img src="{{ $vendor->user->profile_picture_url }}" alt="profile picture" class="img-fluid rounded">
                    @else
                        <img src="/assets/img/default-avatar.png" alt="default profile" class="img-fluid rounded">
                    @endif
                </div>
                <div class="col-md-8">
                    <h5>{{ $vendor->business_name }}</h5>
                    <p><strong>Service Category:</strong> {{ $vendor->service_category }}</p>
                    <p><strong>Experience:</strong> {{ $vendor->experience_years }} years</p>
                    <p><strong>Rating:</strong> {{ $vendor->average_rating }} ({{ $vendor->total_reviews }} reviews)</p>
                    <p><strong>Email:</strong> {{ $vendor->user->email }}</p>
                    <p><strong>Phone:</strong> {{ $vendor->user->phone_number }}</p>
                    <p><strong>Created:</strong> {{ $vendor->created_at->format('d/m/Y') }}</p>

                    <div class="mt-4">
                        <a href="{{ route('vendor.edit', $vendor->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('vendor.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services/Menu Section -->
    <div class="card">
        <div class="card-header pb-0 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0">Services & Menu Items</h6>
                <a href="{{ route('services.create', ['vendor' => $vendor->id]) }}" class="btn btn-sm bg-gradient-primary">
                    <i class="fas fa-plus me-2"></i>Add New Menu Item
                </a>
            </div>
        </div>
        <div class="card-body pt-4 p-3">
            @if($vendor->services->count() > 0)
                <div class="row">
                    @foreach($vendor->services as $service)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">{{ $service->name }}</h6>
                                        <span class="badge bg-{{ $service->is_available ? 'success' : 'danger' }}">
                                            {{ $service->is_available ? 'Available' : 'Not Available' }}
                                        </span>
                                    </div>
                                    <p class="text-sm mb-1">{{ $service->description }}</p>
                                    <p class="text-sm mb-1"><strong>Category:</strong> {{ $service->category }}</p>
                                    <p class="text-sm mb-1"><strong>Price:</strong> RM{{ number_format($service->price, 2) }}</p>
                                    <p class="text-sm mb-3"><strong>Prep Time:</strong> {{ $service->preparation_time }} mins</p>
                                    
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('services.edit', $service->id) }}" class="btn btn-link text-dark px-3 mb-0">
                                            <i class="fas fa-pencil-alt text-dark me-2"></i>Edit
                                        </a>
                                        <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" 
                                                    onclick="return confirm('Are you sure you want to delete this menu item?')">
                                                <i class="far fa-trash-alt me-2"></i>Delete
                                            </button>
                                        </form>
                                        <a href="{{ route('qrcode.create', ['service_id' => $service->id]) }}" class="btn btn-link text-dark px-3 mb-0">
                                            <i class="fas fa-qrcode text-dark me-2"></i>QR
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="mb-0">No services or menu items added yet.</p>
                    {{-- <a href="{{ route('services.create', ['vendor' => $vendor->id]) }}" class="btn btn-sm btn-primary mt-3"> --}}
                    <a href="{{ route('services.create', ['vendor' => $vendor->id]) }}" class="btn btn-sm btn-primary mt-3">
                        Add First Menu Item
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection