<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PricingPlan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EduHubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructor = User::factory()->create(['role'=>'instructor', 'email'=>'instructor@example.com', 'password'=> '12345678']);
        
        $student = User::factory()->create(['role'=>'student', 'email'=>'student@example.com', 'password'=> '12345678']);
        $category = Category::create(['name'=>'Programming','slug'=>'programming']);

        $course = Course::create([
            'instructor_id'=>$instructor->id,
            'category_id'=>$category->id,
            'title'=>'Intro to Laravel',
            'slug'=>'intro-to-laravel',
            'description'=>'Learn Laravel basics.',
            'difficulty'=>'beginner',
            'is_published'=>true,
            'price'=>49.99
        ]);

        // create lessons
        for ($i=1;$i<=5;$i++) {
            Lesson::create([
                'course_id' => $course->id,
                'title' => 'Lesson '. $i,
                'type' => 'video',
                'order' => $i,
                'duration_seconds' => 600
            ]);
        }

        PricingPlan::create(['name'=>'Monthly Access','type'=>'subscription','price'=>19.99,'duration_days'=>30]);
        PricingPlan::create(['name'=>'Lifetime Access','type'=>'one_time','price'=>199.99]);
    }
}
