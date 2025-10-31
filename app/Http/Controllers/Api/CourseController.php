<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\Progress;
use App\Repositories\CourseRepository;
use App\Services\EnrollmentService;
use App\Services\ProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    public function __construct(
        private CourseRepository $courseRepo,
        private EnrollmentService $enrollmentService,
        private ProgressService $progressService)
    {
    }

    public function index(Request $request)
    {
        $courses = $this->courseRepo->getFilteredCourses($request);

        return CourseResource::collection($courses->paginate(15));
    }

     public function enroll(Course $course, EnrollRequest $request): JsonResponse
     {
        $user = $request->user();

        // Use repository/service to handle enroll + payment validation
        try {
            $enrollment = $this->enrollmentService->enrollUser($user, $course, $request->validated());
            return response()->json([
                'message'=>'enrolled',
                'data'=>['enrollment' => $enrollment]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    public function progress(Course $course)
    {
        $progress = $this->progressService->getUserProgressPercentage($course);
        return response()->json([
            'data'=>['progress' => $progress]
        ], Response::HTTP_OK);
    }
}
