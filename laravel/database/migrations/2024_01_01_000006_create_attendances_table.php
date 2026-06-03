<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamp('checked_in_at');
            $table->enum('status', ['present', 'late', 'absent'])->default('present');
            $table->decimal('similarity_score', 5, 4)->default(0.0000);
            $table->string('captured_photo')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            // Prevent duplicate attendance per session
            $table->unique(['session_id', 'student_id']);
            $table->index(['session_id', 'status']);
            $table->index('student_id');
            $table->index('checked_in_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
