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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced']);
            $table->decimal('price', 8, 2)->default(0);
            $table->boolean('is_published')->default(false);
            $table->string('thumbnail')->nullable();
            $table->json('requirements')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('instructor_id');
            $table->index(['category_id', 'difficulty']);
            $table->index(['is_published', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
