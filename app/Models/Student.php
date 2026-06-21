<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_number',
        'first_name',
        'last_name',
        'phone',
        'birthdate',
        'address',
        'strand_id',
        'grade_level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function strand()
    {
        return $this->belongsTo(Strand::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function semesterRecords()
    {
        return $this->hasMany(SemesterRecord::class);
    }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /** Generate a unique student number / School ID like 2026-00006. */
    public static function generateNumber(): string
    {
        $year = date('Y');
        $seq  = static::count() + 1;

        do {
            $number = $year.'-'.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
            $seq++;
        } while (static::where('student_number', $number)->exists());

        return $number;
    }

    /** Latest enrollment for the given active school year (or overall). */
    public function currentEnrollment(?int $schoolYearId = null)
    {
        return $this->enrollments()
            ->when($schoolYearId, function ($q) use ($schoolYearId) {
                $q->whereHas('section', fn ($s) => $s->where('school_year_id', $schoolYearId));
            })
            ->latest('submitted_at')
            ->first();
    }
}
