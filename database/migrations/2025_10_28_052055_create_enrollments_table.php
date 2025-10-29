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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->decimal('amount_paid', 8, 2)->default(0);
            $table->enum('payment_method', ['card', 'paypal', 'bank_transfer'])->nullable();
            $table->timestamp('enrolled_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index(['user_id', 'payment_status']);
            $table->index(['course_id', 'payment_status']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
