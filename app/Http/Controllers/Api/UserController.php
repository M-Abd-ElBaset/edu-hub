<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private UserRepository $userRepo)
    {
    }

    public function courses(User $user)
    {
        $courses = $this->userRepo->getUserCourses($user);

        $data = [
            'courses' => CourseResource::collection($courses->paginate(15))
        ];
        return response()->json(['data' => $data], Response::HTTP_OK);
    }
}
