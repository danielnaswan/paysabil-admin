<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'title',
        'description',
        'status',
        'submission_date',
        'document_url',
        'document_name',
        'document_size',
        'admin_remarks',
        'student_id',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'id' => 'string',
        'submission_date' => 'datetime',
        'reviewed_at' => 'datetime'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getDocumentSizeForHumans()
    {
        $bytes = $this->document_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }
        return round($bytes, 2) . ' ' . $units[$index];
    }
}
