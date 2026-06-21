<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ApplicationDocument extends Model
{
    /** Required admission document types: key => human label. */
    public const TYPES = [
        'sf10'       => 'SF10 / Form 137',
        'sf9'        => 'SF9 / Report Card (Grade 10)',
        'good_moral' => 'Certificate of Good Moral Character',
        'psa'        => 'PSA Birth Certificate',
        'photo'      => '2x2 ID Photo',
    ];

    protected $fillable = [
        'application_id',
        'type',
        'path',
        'original_name',
    ];

    /** Human-readable label for this document's type. */
    public function label(): string
    {
        return self::TYPES[$this->type] ?? ucwords(str_replace('_', ' ', $this->type));
    }

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
