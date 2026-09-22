<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_uuid')->unique();
            $table->foreignId('candidate_user_id')->constrained('candidate_users')->restrictOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->restrictOnDelete();
            $table->unsignedInteger('attempt_no')->default(1);
            $table->string('status')->default('in_progress')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->decimal('score_auto', 6, 2)->nullable();
            $table->decimal('score_manual', 6, 2)->nullable();
            $table->decimal('score_final', 6, 2)->nullable();
            $table->boolean('passed')->nullable();
            $table->unsignedInteger('duration_used_seconds')->nullable();
            $table->string('grading_status')->default('pending')->index();
            $table->timestamps();
            $table->unique(['candidate_user_id', 'exam_id', 'attempt_no']);
            $table->index(['candidate_user_id', 'exam_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('attempts'); }
};
