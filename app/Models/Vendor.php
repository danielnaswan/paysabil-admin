<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'business_name',
        'service_category',
        'experience_years',
        'average_rating',
        'total_reviews',
        'profile_picture_url',
        'user_id'
    ];

    protected $casts = [
        'id' => 'string',
        'average_rating' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}