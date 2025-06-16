<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'preparation_time',
        'is_available',
        'vendor_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'preparation_time' => 'integer',
        'is_available' => 'boolean'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function qrCodes()
    {
        return $this->hasOne(QrCode::class);
    }
}