<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseRepository
{
    public function getFilteredCourses(Request $request): Builder
    {
        $cacheKey = $this->buildCacheKey($request);
        
        return Cache::remember($cacheKey, 3600, function () use ($request) {
            return $this->buildQuery($request);
        });
    }

    private function buildQuery(Request $request): Builder
    {
        $query = Course::published()->with(['category', 'instructor']);

        // Filter by category
        if ($request->has('filter.category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->get('filter')['category']);
            });
        }

        // Filter by difficulty
        if ($request->has('filter.difficulty')) {
            $query->where('difficulty', $request->get('filter')['difficulty']);
        }

        // Search
        if ($request->has('filter.search')) {
            $search = $request->get('filter')['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        if ($request->has('sort')) {
            $sortField = ltrim($request->get('sort'), '-');
            $sortDirection = str_starts_with($request->get('sort'), '-') ? 'desc' : 'asc';
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    private function buildCacheKey(Request $request): string
    {
        return 'courses:' . md5(serialize($request->all()));
    }

    public function getUserProgress($user, $course)
    {
        // Eager load progress to avoid N+1
        return $course->load([
            'lessons.progress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }
        ]);
    }
}