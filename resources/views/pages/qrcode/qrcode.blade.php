@extends('layouts.user_type.auth')

@section('content')
<div>
    @if(session('success'))
        <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
            <span class="alert-text text-white">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="fa fa-close" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">All QR Codes</h5>
                        </div>
                        <a href="{{ route('qrcode.create') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+ Generate New QR Code</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Vendor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Service</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Expiry Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($qrCodes as $qrCode)
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $qrCode->vendor->business_name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $qrCode->service->name }}</p>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-{{ $qrCode->status === 'ACTIVE' ? 'success' : ($qrCode->status === 'EXPIRED' ? 'warning' : 'danger') }}">
                                            {{ $qrCode->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $qrCode->expiry_date->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="align-middle">
                                        <div class="ms-auto">
                                            <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('qrcode.show', $qrCode->id) }}">
                                                <i class="fas fa-qrcode text-dark me-2"></i>View
                                            </a>
                                            <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('qrcode.edit', $qrCode->id) }}">
                                                <i class="fas fa-pencil-alt text-dark me-2"></i>Edit
                                            </a>
                                            <form action="{{ route('qrcode.destroy', $qrCode->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this QR Code?')">
                                                    <i class="far fa-trash-alt me-2"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection