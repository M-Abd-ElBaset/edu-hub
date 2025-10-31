<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\CourseEnrolledNotification;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function enrollUser(User $user, Course $course, array $paymentData): Enrollment
    {
        return DB::transaction(function () use ($user, $course, $paymentData) {
            // Check if already enrolled
            if ($this->isUserEnrolled($user, $course)) {
                throw new \Exception('User is already enrolled in this course');
            }

            // Process payment
            $paymentResult = $this->paymentService->processPayment(
                $user,
                $course,
                $paymentData
            );

            if (!$paymentResult) {
                throw new \Exception('Payment failed');
            }

            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'payment_status' => 'completed',
                'amount_paid' => $course->price,
                'payment_method' => $paymentData['payment_method'],
                'enrolled_at' => now()
            ]);

            // $user->notify(new CourseEnrolledNotification($course));

            return $enrollment;
        });
    }

    private function isUserEnrolled(User $user, Course $course): bool
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('payment_status', 'completed')
            ->exists();
    }
}