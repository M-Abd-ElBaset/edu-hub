<?php

namespace App\Services;

use App\Mail\CourseCompleted as CourseCompletedMail;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class CourseCompletionService
{
    public function checkAndAwardCompletion(User $user, Course $course): void
    {
        if ($this->isCourseCompleted($user, $course)) {
            $this->awardCertificate($user, $course);
            $this->notifyCompletion($user, $course);
            
            // Mark enrollment as completed
            $enrollment = $user->enrolledCourses()
                ->where('course_id', $course->id)
                ->first();
                
            if ($enrollment) {
                $enrollment->pivot->update(['completed_at' => now()]);
            }
        }
    }

    private function awardCertificate(User $user, Course $course): void
    {
        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$existingCertificate) {
            Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_number' => Certificate::generateCertificateNumber()
            ]);
        }
    }


    protected function notifyCompletion(User $user, Course $course)
    {
        // Dispatch mail & in-app notification (job queue recommended)
        $certificatePath = storage_path("app/certificates/{$user->id}_{$course->id}.pdf");
        Mail::to($user->email)->queue(new CourseCompletedMail($user, $course, $certificatePath));
    }

    public function isCourseCompleted(User $user, Course $course): float
    {
        $totalLessons = $course->lessons()->count();

        $completedLessons = $user->progress()
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->count();

        return $completedLessons >= $totalLessons;
    }

    private function generateVerificationUrl(): string
    {
        return url('/verify-certificate/' . uniqid());
    }
}