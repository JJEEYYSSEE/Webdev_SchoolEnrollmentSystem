<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'strand_id',
        'school_year_id',
        'grade_level',
        'semester',
        'section_name',
        'time_period',
        'max_capacity',
    ];

    public function strand()
    {
        return $this->belongsTo(Strand::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'section_subjects')
            ->withPivot('day_of_week', 'start_time', 'end_time', 'room')
            ->withTimestamps();
    }

    /** True once a weekly schedule has been generated for this section. */
    public function hasSchedule(): bool
    {
        return $this->subjects()->whereNotNull('section_subjects.start_time')->exists();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Count of approved enrollments occupying a slot. Prefers an eager-loaded
     * `approved_count` (via withCount) to avoid an N+1 query per section.
     */
    public function approvedCount(): int
    {
        return $this->approved_count ?? $this->enrollments()->where('status', 'approved')->count();
    }

    /** Slots still open. Never negative. */
    public function remainingSlots(): int
    {
        return max(0, $this->max_capacity - $this->approvedCount());
    }

    public function isFull(): bool
    {
        return $this->remainingSlots() <= 0;
    }

    /** Open but running low — drives the "Almost full" badge on the student picker. */
    public function isNearlyFull(): bool
    {
        return ! $this->isFull() && $this->remainingSlots() <= 5;
    }

    public function displayName(): string
    {
        return "Grade {$this->grade_level} - {$this->section_name} ({$this->time_period})";
    }
}
