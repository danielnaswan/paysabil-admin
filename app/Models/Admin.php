<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use SoftDeletes;

    protected $fillables = [
        "full_name",
        "department",
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }
}
