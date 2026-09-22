<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('slug');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('exam_type')->default('chapter')->index();
            $table->unsignedInteger('duration_seconds');
            $table->decimal('pass_score', 6, 2)->default(65.00);
            $table->unsignedInteger('attempt_limit')->nullable();
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            $table->string('result_review_mode')->default('full_explanation');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->unique(['program_id', 'slug']);
        });
    }
    public function down(): void { Schema::dropIfExists('exams'); }
};
