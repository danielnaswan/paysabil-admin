<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes, Timestamp;
    protected $fillable = [
        'status',
        'amount',
        'meal_details',
        'transaction_date',
        'student_id',      
        'vendor_id',       
        'qr_code_id', 
    ];

    protected $casts = [
               
    ];

    public function student() :BelongsTo {
        return $this->belongsTo(Student::class);
    }

    public function vendor() :BelongsTo {
        return $this->belongsTo(Vendor::class);
    }

    public function qrCode() :BelongsTo {
        return $this->belongsTo(QrCode::class);
    }
}
