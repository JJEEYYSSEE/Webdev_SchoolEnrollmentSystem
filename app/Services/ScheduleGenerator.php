<?php

namespace App\Services;

use App\Models\Section;
use Carbon\Carbon;

class ScheduleGenerator
{
    private const DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

    // Spread a section's subjects across the week in 60-min slots. Returns how many got scheduled.
    public function generate(Section $section): int
    {
        $subjects = $section->subjects()->orderBy('subjects.subject_code')->get();

        if ($subjects->isEmpty()) {
            return 0;
        }

        $slots = $this->slotsFor($section->time_period);
        $room  = 'Room '.$section->id;

        // day-first order so subjects spread across Mon–Fri, not stacked on one day
        $assignments = [];
        foreach ($slots as $slot) {
            foreach (self::DAYS as $day) {
                $assignments[] = ['day' => $day, 'start' => $slot[0], 'end' => $slot[1]];
            }
        }

        $count = 0;

        foreach ($subjects->values() as $i => $subject) {
            if (! isset($assignments[$i])) {
                break; // ran out of slots
            }

            $a = $assignments[$i];
            $section->subjects()->updateExistingPivot($subject->id, [
                'day_of_week' => $a['day'],
                'start_time'  => $a['start'],
                'end_time'    => $a['end'],
                'room'        => $room,
            ]);

            $count++;
        }

        return $count;
    }

    // 60-min slots in the AM or PM window (lunch 12–1 skipped). Returns [start, end] pairs.
    private function slotsFor(string $timePeriod): array
    {
        $starts = $timePeriod === 'PM'
            ? ['13:00', '14:00', '15:00', '16:00']
            : ['07:30', '08:30', '09:30', '10:30'];

        return array_map(fn ($s) => [
            Carbon::parse($s)->format('H:i:s'),
            Carbon::parse($s)->addMinutes(60)->format('H:i:s'),
        ], $starts);
    }
}
