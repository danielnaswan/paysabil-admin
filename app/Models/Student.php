<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'id',
        'full_name',
        'matrix_no',
        'user_id'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application() : HasOne {
        return $this->hasOne(Application::class);
    }

    public function transaction() : HasOne {
        return $this->hasOne(Transaction::class);
    }

    public function ratings()  {
        return $this->hasMany(Rating::class);
    }
 }
