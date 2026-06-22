<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'section_id',
        'status',
        'remarks',
        'approved_by',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function approver()
    {
        return $this->belongsTo(Registrar::class, 'approved_by');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'enrollment_subjects')
            ->withPivot('grade', 'status')
            ->withTimestamps();
    }

    public function enrollmentSubjects()
    {
        return $this->hasMany(EnrollmentSubject::class);
    }

    public function documents()
    {
        return $this->hasMany(EnrollmentDocument::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /** Returned for compliance — the student may fix and re-submit. */
    public function isInvalid(): bool
    {
        return $this->status === 'invalid';
    }
}
