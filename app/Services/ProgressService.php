<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Progress;

class ProgressService
{
    public function getUserProgressPercentage(Course $course)
    {
        $totalCourseDuration = $course->lessons()->sum('duration_seconds');

        if ($totalCourseDuration == 0) {
            return 0; // avoid division by zero
        }

        $userTimeSpent = Progress::where('user_id', auth()->id())
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->sum('time_spent_seconds');

        $percentage = ($userTimeSpent / $totalCourseDuration) * 100;

        return round($percentage, 2);
    }
}