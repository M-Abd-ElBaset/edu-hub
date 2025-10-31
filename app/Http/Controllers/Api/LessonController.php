<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteLessonRequest;
use App\Models\Lesson;
use App\Services\ProgressService;
use Illuminate\Http\JsonResponse;

class LessonController extends Controller
{
    public function __construct(private ProgressService $progressService) {}

    public function complete(Lesson $lesson): JsonResponse
    {
        $user = auth()->user();
        
        try {
            $progress = $this->progressService->markLessonComplete($user, $lesson);
            
            return response()->json([
                'message' => 'Lesson marked as complete',
                'progress' => $progress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
}