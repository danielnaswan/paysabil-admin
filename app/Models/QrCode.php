<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class QrCode extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'service_details',
        'generated_date',
        'expiry_date',
        'status',
        'vendor_id',
        'service_id'
    ];

    protected $casts = [
        'service_details' => 'array',
        'generated_date' => 'datetime',
        'expiry_date' => 'datetime'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}