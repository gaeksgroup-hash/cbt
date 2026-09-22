<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('attempts')->cascadeOnDelete()->index();
            $table->foreignId('attempt_question_id')->unique()->constrained('attempt_questions')->cascadeOnDelete();
            $table->json('answer_json')->nullable();
            $table->boolean('is_marked_review')->default(false);
            $table->timestamp('saved_at')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('awarded_points', 8, 2)->nullable();
            $table->decimal('reviewer_score', 8, 2)->nullable();
            $table->text('reviewer_note')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('attempt_answers'); }
};
