<?php

namespace App\Services;

use App\Contracts\Services\IPaymentService;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentService implements IPaymentService
{
    public function processPayment(User $user, Course $course, array $paymentData): bool
    {
        Log::info('Payment processed successfully', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'payment_method' => $paymentData['payment_method']
        ]);

        return true;
    }
}