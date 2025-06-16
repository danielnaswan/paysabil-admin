<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'student_id',
        'stars',
        'review_comment',
        'review_date',
    ];

    public function vendor() {
        return $this->belongsTo(Vendor::class);
    }

    public function student() {
        return $this->belongsTo(Student::class); 
    }
}
