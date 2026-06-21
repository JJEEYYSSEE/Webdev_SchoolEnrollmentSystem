<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'application_id',
        'type',
        'path',
        'original_name',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /** Public URL for previewing/downloading the stored file. */
    public function url(): string
    {
        return Storage::url($this->path);
    }
}
