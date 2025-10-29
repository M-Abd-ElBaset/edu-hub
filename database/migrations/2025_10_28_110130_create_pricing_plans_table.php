<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['one_time','subscription','bundle'])->default('one_time');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('duration_days')->nullable(); // for subscription
            $table->timestamps();
        });

        // pivot table: plan_course (for bundle mapping)
        Schema::create('plan_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_plan_id')->constrained('pricing_plans')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->decimal('override_price', 10, 2)->nullable();

            $table->unique(['pricing_plan_id','course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_course');
        Schema::dropIfExists('pricing_plans');
    }
};
