<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EnrollmentDocument extends Model
{
    /** Grade 12 enrollment requirements: key => human label. */
    public const TYPES = [
        'sf9'   => 'Grade 11 Report Card (SF9)',
        'photo' => '2x2 ID Photo',
    ];

    protected $fillable = [
        'enrollment_id',
        'type',
        'path',
        'original_name',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function label(): string
    {
        return self::TYPES[$this->type] ?? ucwords(str_replace('_', ' ', $this->type));
    }

    public function url(): string
    {
        return Storage::url($this->path);
    }
}
