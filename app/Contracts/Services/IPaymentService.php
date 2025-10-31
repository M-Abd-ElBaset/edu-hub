<?php

namespace App\Contracts\Services;

use App\Models\Course;
use App\Models\User;

interface IPaymentService
{
    public function processPayment(User $user, Course $course, array $paymentData): bool;
}