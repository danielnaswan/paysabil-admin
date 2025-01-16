<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'id',
        'full_name',
        'matrix_no',
        'profile_picture_url',
        'user_id'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
