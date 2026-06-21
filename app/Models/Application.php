<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'current_step',
        // personal
        'lrn',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'birthdate',
        'sex',
        'place_of_birth',
        'civil_status',
        'mother_tongue',
        'religion',
        'is_ip',
        'ip_community',
        'has_disability',
        'disability_type',
        'is_4ps',
        'household_id',
        'mobile',
        'email',
        // address
        'current_address',
        'current_barangay',
        'current_city',
        'current_province',
        'current_zip',
        'permanent_same',
        'permanent_address',
        'permanent_barangay',
        'permanent_city',
        'permanent_province',
        'permanent_zip',
        // parents / guardian
        'father_name',
        'father_contact',
        'mother_name',
        'mother_contact',
        'guardian_name',
        'guardian_relationship',
        'guardian_contact',
        // education
        'jhs_name',
        'jhs_school_id',
        'jhs_year_graduated',
        'general_average',
        'elementary_name',
        'elementary_year_graduated',
        'is_returning',
        'is_transferee',
        'previous_school',
        // academic
        'strand_id',
        'grade_level',
        // status / review (set by controllers, never from raw form input)
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'birthdate'       => 'date',
            'is_ip'           => 'boolean',
            'has_disability'  => 'boolean',
            'is_4ps'          => 'boolean',
            'permanent_same'  => 'boolean',
            'is_returning'    => 'boolean',
            'is_transferee'   => 'boolean',
            'general_average' => 'decimal:2',
            'submitted_at'    => 'datetime',
            'reviewed_at'     => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function strand()
    {
        return $this->belongsTo(Strand::class);
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Registrar::class, 'reviewed_by');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInvalid(): bool
    {
        return $this->status === 'invalid';
    }

    public function isQualified(): bool
    {
        return $this->status === 'qualified';
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
