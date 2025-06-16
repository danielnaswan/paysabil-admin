@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Add New Menu Item</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('services.store') }}" method="POST" role="form text-left">
                @csrf
                @if($errors->any())
                    <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
                        <span class="alert-text text-white">{{$errors->first()}}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif

                @if(isset($vendor))
                    <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                @else
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="vendor-select" class="form-control-label">Select Vendor</label>
                                <select class="form-control" id="vendor-select" name="vendor_id" required>
                                    <option value="">Choose a vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->business_name }}</option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-control-label">Item Name</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category" class="form-control-label">Category</label>
                            <input class="form-control" type="text" id="category" name="category" value="{{ old('category') }}" required>
                            @error('category')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description" class="form-control-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price" class="form-control-label">Price (RM)</label>
                            <input class="form-control" type="number" step="0.01" id="price" name="price" value="{{ old('price') }}" required>
                            @error('price')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="preparation_time" class="form-control-label">Preparation Time (minutes)</label>
                            <input class="form-control" type="number" id="preparation_time" name="preparation_time" value="{{ old('preparation_time') }}" required>
                            @error('preparation_time')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_available" name="is_available" checked>
                            <label class="form-check-label" for="is_available">Available for Order</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('vendor.show', isset($vendor) ? $vendor->id : old('vendor_id')) }}" class="btn btn-light m-0 me-2">Cancel</a>
                    <button type="submit" class="btn bg-gradient-primary m-0">Add Menu Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection