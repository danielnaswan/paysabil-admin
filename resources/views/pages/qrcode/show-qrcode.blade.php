@extends('layouts.user_type.auth')
{{-- {{dd($qrCode)}} --}}

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">QR Code Details</h6>
                        <div>
                            <span class="badge badge-lg {{ $qrCode->status_badge_class }}">
                                {{ $qrCode->status }}
                            </span>
                            @if($qrCode->isExpiringSoon(24))
                                <span class="badge badge-sm bg-warning ms-2">Expiring Soon!</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <div class="qr-code-container p-4 bg-white rounded shadow-sm border">
                                @if($imageExists)
                                    <img src="{{ route('qrcode.image', $qrCode) }}" 
                                         alt="QR Code" 
                                         class="img-fluid" 
                                         style="max-width: 300px;">
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center" style="height: 300px;">
                                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                        <h6 class="text-warning">QR Code Image Not Found</h6>
                                        <p class="text-sm text-muted">The QR code image may have been deleted or corrupted</p>
                                        <button onclick="regenerateQR()" class="btn btn-sm btn-primary">
                                            <i class="fas fa-refresh me-1"></i>Regenerate Image
                                        </button>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-3 d-flex justify-content-center gap-2">
                                <button onclick="printQR()" class="btn btn-info btn-sm">
                                    <i class="fas fa-print me-2"></i>Print QR Code
                                </button>
                                <a href="{{ route('qrcode.download', $qrCode) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>Download PNG
                                </a>
                                <button onclick="copyQRLink()" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-link me-2"></i>Copy Link
                                </button>
                            </div>
                            
                            <div class="mt-3 p-3 bg-light rounded">
                                <h6 class="text-xs text-uppercase font-weight-bold mb-2">QR Code Information</h6>
                                <p class="text-sm mb-1"><strong>Code:</strong> {{ $qrCode->code }}</p>
                                <p class="text-sm mb-1"><strong>Generated:</strong> {{ $qrCode->getFormattedGeneratedDate() }}</p>
                                <p class="text-sm mb-0"><strong>Valid Until:</strong> {{ $qrCode->getFormattedExpiryDate() }}</p>
                            </div>
                            
                            <p class="text-sm text-muted mt-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Scan this QR code using the mobile app to verify the service
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Service Details</h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-gradient-secondary rounded-circle me-3">
                                            <i class="fas fa-store text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-sm">{{ $qrCode->vendor->business_name }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $qrCode->vendor->service_category }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-gradient-info rounded-circle me-3">
                                            <i class="fas fa-utensils text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-sm">{{ $qrCode->service->name }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $qrCode->service->category }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-gradient-success rounded-circle me-3">
                                            <i class="fas fa-tag text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-sm">RM {{ number_format($qrCode->service->price, 2) }}</h6>
                                            <p class="text-xs text-secondary mb-0">Service Price</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-gradient-warning rounded-circle me-3">
                                            <i class="fas fa-clock text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-sm">{{ $qrCode->service->preparation_time }} minutes</h6>
                                            <p class="text-xs text-secondary mb-0">Preparation Time</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($qrCode->service->description)
                            <div class="mt-4">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-2">Service Description</h6>
                                <p class="text-sm text-secondary">{{ $qrCode->service->description }}</p>
                            </div>
                            @endif
                            
                            <div class="d-flex justify-content-end mt-4 gap-2">
                                <a href="{{ route('qrcode.edit', $qrCode->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-pencil-alt me-2"></i>Edit QR Code
                                </a>
                                <a href="{{ route('qrcode.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-2"></i>Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Usage Statistics -->
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Usage Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="font-weight-bolder text-info">{{ $usageStats['total_scans'] }}</h4>
                                <p class="text-sm mb-0">Total Scans</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="font-weight-bolder text-success">{{ $usageStats['successful_scans'] }}</h4>
                                <p class="text-sm mb-0">Successful</p>
                            </div>
                        </div>
                    </div>
                    <hr class="horizontal dark">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="font-weight-bolder text-warning">{{ $usageStats['unique_users'] }}</h4>
                                <p class="text-sm mb-0">Unique Users</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="font-weight-bolder text-primary">RM {{ number_format($usageStats['total_revenue'], 2) }}</h4>
                                <p class="text-sm mb-0">Revenue</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($usageStats['first_scan'])
                    <hr class="horizontal dark">
                    <div class="text-center">
                        <p class="text-xs text-secondary mb-1">First Scan: {{ \Carbon\Carbon::parse($usageStats['first_scan'])->format('d M Y, H:i') }}</p>
                        @if($usageStats['last_scan'])
                        <p class="text-xs text-secondary mb-0">Last Scan: {{ \Carbon\Carbon::parse($usageStats['last_scan'])->format('d M Y, H:i') }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- QR Code Status -->
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">QR Code Status</h6>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-one-side">
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                <i class="fas fa-plus text-success"></i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Generated</h6>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                    {{ $qrCode->getFormattedGeneratedDate() }}
                                </p>
                            </div>
                        </div>
                        
                        @if($qrCode->status === 'USED' || $usageStats['successful_scans'] > 0)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                <i class="fas fa-qrcode text-info"></i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">First Scan</h6>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                    {{ $usageStats['first_scan'] ? \Carbon\Carbon::parse($usageStats['first_scan'])->format('d M Y, H:i') : 'Not scanned yet' }}
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        @if($qrCode->is_expired)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                <i class="fas fa-clock text-warning"></i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Expired</h6>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                    {{ $qrCode->getFormattedExpiryDate() }}
                                </p>
                            </div>
                        </div>
                        @else
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                <i class="fas fa-hourglass-half text-primary"></i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Will Expire</h6>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                    {{ $qrCode->getFormattedExpiryDate() }}
                                </p>
                                <p class="text-xs text-muted mb-0">{{ $qrCode->time_remaining }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printQR() {
    const printWindow = window.open('', '', 'width=800,height=600');
    const qrCodeSrc = '{{ route("qrcode.image", $qrCode) }}';
    
    const printContent = `
        <html>
            <head>
                <title>QR Code - {{ $qrCode->service->name }}</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        padding: 20px;
                        background: white;
                    }
                    .print-container {
                        text-align: center;
                        border: 2px solid #000;
                        padding: 30px;
                        border-radius: 10px;
                        background: white;
                        box-shadow: 0 0 20px rgba(0,0,0,0.1);
                    }
                    .qr-image {
                        max-width: 300px;
                        margin: 20px 0;
                    }
                    .header {
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 10px;
                        color: #333;
                    }
                    .vendor-name {
                        font-size: 20px;
                        color: #666;
                        margin-bottom: 10px;
                    }
                    .service-name {
                        font-size: 18px;
                        color: #333;
                        margin-bottom: 5px;
                    }
                    .price {
                        font-size: 22px;
                        font-weight: bold;
                        color: #e74c3c;
                        margin: 15px 0;
                    }
                    .details {
                        font-size: 14px;
                        color: #666;
                        margin-top: 20px;
                    }
                    .footer {
                        font-size: 12px;
                        color: #999;
                        margin-top: 20px;
                        border-top: 1px solid #eee;
                        padding-top: 10px;
                    }
                    @media print {
                        body { margin: 0; }
                        .print-container { 
                            border: 1px solid #000; 
                            box-shadow: none; 
                        }
                    }
                </style>
            </head>
            <body>
                <div class="print-container">
                    <div class="header">Pay Sabil Al-Hikmah</div>
                    <div class="vendor-name">{{ $qrCode->vendor->business_name }}</div>
                    <div class="service-name">{{ $qrCode->service->name }}</div>
                    <div class="price">RM {{ number_format($qrCode->service->price, 2) }}</div>
                    
                    <img src="${qrCodeSrc}" alt="QR Code" class="qr-image" />
                    
                    <div class="details">
                        <div>Category: {{ $qrCode->service->category }}</div>
                        <div>Preparation Time: {{ $qrCode->service->preparation_time }} minutes</div>
                        <div>Valid Until: {{ $qrCode->getFormattedExpiryDate() }}</div>
                    </div>
                    
                    <div class="footer">
                        <div>QR Code: {{ $qrCode->code }}</div>
                        <div>Generated: {{ $qrCode->getFormattedGeneratedDate() }}</div>
                        <div>Scan with Pay Sabil mobile app</div>
                    </div>
                </div>
            </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.focus();
    
    // Wait for image to load before printing
    printWindow.onload = function() {
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
}

function copyQRLink() {
    const qrLink = '{{ route("qrcode.image", $qrCode) }}';
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(qrLink).then(function() {
            showToast('QR code link copied to clipboard!', 'success');
        }, function(err) {
            console.error('Could not copy text: ', err);
            fallbackCopyTextToClipboard(qrLink);
        });
    } else {
        fallbackCopyTextToClipboard(qrLink);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('QR code link copied to clipboard!', 'success');
        } else {
            showToast('Failed to copy link', 'error');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        showToast('Failed to copy link', 'error');
    }
    
    document.body.removeChild(textArea);
}

function regenerateQR() {
    if (confirm('This will regenerate the QR code image. Continue?')) {
        // Make a request to regenerate the image
        fetch('{{ route("qrcode.image", $qrCode) }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                showToast('Failed to regenerate QR code', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to regenerate QR code', 'error');
        });
    }
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <span class="alert-text text-white">${message}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert">
            <i class="fa fa-close"></i>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Auto-refresh if QR code is expiring soon
@if($qrCode->isExpiringSoon(1))
setTimeout(() => {
    location.reload();
}, 60000); // Refresh every minute for codes expiring within 1 hour
@endif
</script>
@endpush
@endsection