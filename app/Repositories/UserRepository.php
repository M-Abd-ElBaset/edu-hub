<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class UserRepository
{
    public function getUserCourses(User $user)
    {
        $cacheKey = $this->buildCacheKey($user);

        return Cache::remember($cacheKey, 3600, function () use ($user) {
            return $this->buildQuery($user)->paginate(15);
        });
    }

    private function buildQuery(User $user): Builder
    {
        $query = Course::published();
        
        $query->whereHas('enrollments', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
       
        $query->orderBy('created_at', 'desc');
        

        return $query;
    }

    private function buildCacheKey(User $user): string
    {
        return 'courses:' . md5(serialize($user->toArray()));
    }
}