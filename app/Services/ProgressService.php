<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    public function __construct(private CourseCompletionService $completionService) 
    {
    }

    public function markLessonComplete(User $user, Lesson $lesson): Progress
    {
        return DB::transaction(function () use ($user, $lesson) {
            // Check if user is enrolled in this lesson
            if (!$this->isEnrolled($user, $lesson)) {
                throw new \Exception('Cannot complete this lesson. Check enrollment.');
            }
            
            if (!$this->isPreviousLessonCompleted($user, $lesson)) {
                throw new \Exception('Cannot complete this lesson. Check previous lesson completion.');
            }

            // Update or create progress record
            $progress = Progress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id
                ],
                [
                    'course_id' => $lesson->course_id,
                    'is_completed' => true,
                    'time_spent_seconds' => $lesson->duration_seconds ?? 0,
                    'completed_at' => now()
                ]
            );

            // Check course completion
            $this->completionService->checkAndAwardCompletion($user, $lesson->course);

            return $progress;
        });
    }

    public function isEnrolled(User $user, Lesson $lesson): bool
    {
        // Check if user is enrolled in the course
        return $user->enrolledCourses()
            ->where('course_id', $lesson->course_id)
            ->exists();
    }
    
    public function isPreviousLessonCompleted(User $user, Lesson $lesson): bool
    {
        $previousLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('order', '<', $lesson->order)
            ->orderBy('order', 'desc')
            ->first();

        return $user->progress()
            ->where('lesson_id', $previousLesson->id ?? 0)
            ->where('is_completed', true)
            ->exists();
    }

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