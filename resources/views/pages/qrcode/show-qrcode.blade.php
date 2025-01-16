@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <div class="d-flex align-items-center">
                <h6 class="mb-0">QR Code Details</h6>
                <div class="ms-auto">
                    <span class="badge badge-sm bg-{{ $qrCode->status === 'ACTIVE' ? 'success' : ($qrCode->status === 'EXPIRED' ? 'warning' : 'danger') }}">
                        {{ $qrCode->status }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body pt-4 p-3">
            <div class="row">
                <div class="col-md-6 text-center">
                    <div class="qr-code-container p-4 bg-white rounded shadow-sm">
                        {!! $qrImage !!}
                    </div>
                    <div class="mt-3">
                        <button onclick="printQR()" class="btn btn-info btn-sm">
                            <i class="fas fa-print me-2"></i>Print QR Code
                        </button>
                        <button onclick="downloadQR()" class="btn btn-primary btn-sm ms-2">
                            <i class="fas fa-download me-2"></i>Download QR
                        </button>
                    </div>
                    <p class="text-sm text-muted mt-2">
                        Scan this QR code using the mobile app to verify the service
                    </p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Service Details</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 px-0">
                            <strong class="text-dark">Vendor:</strong> {{ $qrCode->vendor->business_name }}
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <strong class="text-dark">Service:</strong> {{ $qrCode->service->name }}
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <strong class="text-dark">Price:</strong> RM{{ number_format($qrCode->service->price, 2) }}
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <strong class="text-dark">Category:</strong> {{ $qrCode->service->category }}
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <strong class="text-dark">Preparation Time:</strong> {{ $qrCode->service->preparation_time }} minutes
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <strong class="text-dark">Generated On:</strong> {{ $qrCode->generated_date->format('d/m/Y H:i') }}
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <strong class="text-dark">Expires On:</strong> {{ $qrCode->expiry_date->format('d/m/Y H:i') }}
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <strong class="text-dark">Description:</strong>
                            <p class="mb-0 text-sm">{{ $qrCode->service->description }}</p>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('qrcode.edit', $qrCode->id) }}" class="btn btn-info m-0 me-2">
                            <i class="fas fa-pencil-alt me-2"></i>Edit
                        </a>
                        <a href="{{ route('qrcode.index') }}" class="btn btn-light m-0">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function printQR() {
        const printWindow = window.open('', '', 'width=600,height=600');
        const qrCode = document.querySelector('.qr-code-container').innerHTML;
        const serviceDetails = `
            <div style="text-align: center; margin-top: 20px; font-family: Arial, sans-serif;">
                <p style="margin: 5px 0;"><strong>{{ $qrCode->vendor->business_name }}</strong></p>
                <p style="margin: 5px 0;">{{ $qrCode->service->name }}</p>
                <p style="margin: 5px 0;">RM{{ number_format($qrCode->service->price, 2) }}</p>
                <p style="margin: 5px 0; font-size: 12px;">Expires: {{ $qrCode->expiry_date->format('d/m/Y H:i') }}</p>
            </div>
        `;
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print QR Code - {{ $qrCode->service->name }}</title>
                    <style>
                        body {
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            margin: 0;
                            padding: 20px;
                        }
                        .qr-container {
                            background: white;
                            padding: 20px;
                            border-radius: 8px;
                            box-shadow: 0 0 10px rgba(0,0,0,0.1);
                        }
                    </style>
                </head>
                <body>
                    <div class="qr-container">
                        ${qrCode}
                        ${serviceDetails}
                    </div>
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }

    function downloadQR() {
        const svg = document.querySelector('.qr-code-container svg');
        const svgData = new XMLSerializer().serializeToString(svg);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        // Set canvas size to match SVG
        const svgSize = svg.viewBox.baseVal;
        canvas.width = svgSize.width;
        canvas.height = svgSize.height;
        
        img.onload = function() {
            ctx.drawImage(this, 0, 0);
            const pngFile = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.download = 'qr-code-{{ $qrCode->service->name }}.png';
            downloadLink.href = pngFile;
            downloadLink.click();
        };
        
        img.src = 'data:image/svg+xml;base64,' + btoa(svgData);
    }
</script>
@endpush
@endsection