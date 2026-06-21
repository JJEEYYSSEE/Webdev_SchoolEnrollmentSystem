<?php

namespace App\Services;

use App\Models\Section;
use Carbon\Carbon;

class ScheduleGenerator
{
    private const DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

    /**
     * Auto-generate a weekly timetable for a section's subjects.
     *
     * Uses DepEd-style class hours within the section's AM/PM window, 60-minute
     * slots, one homeroom per cohort. Subjects are spread across the week
     * (round-robin by day) so no two land on the same day/time within the
     * section. Returns the number of subjects scheduled.
     */
    public function generate(Section $section): int
    {
        $subjects = $section->subjects()->orderBy('subjects.subject_code')->get();

        if ($subjects->isEmpty()) {
            return 0;
        }

        $slots = $this->slotsFor($section->time_period);
        $room  = 'Room '.$section->id;

        // Round-robin by day first so subjects spread evenly across Mon–Fri.
        $assignments = [];
        foreach ($slots as $slot) {
            foreach (self::DAYS as $day) {
                $assignments[] = ['day' => $day, 'start' => $slot[0], 'end' => $slot[1]];
            }
        }

        $count = 0;

        foreach ($subjects->values() as $i => $subject) {
            if (! isset($assignments[$i])) {
                break; // more subjects than available weekly slots
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

    /**
     * 60-minute slots within the AM or PM window (skips the 12:00–1:00 lunch).
     *
     * @return array<int, array{0:string,1:string}>  list of [start, end] (H:i:s)
     */
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
