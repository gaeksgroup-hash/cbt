<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attempt_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->nullable()->constrained('questions')->nullOnDelete();
            $table->unsignedInteger('question_version')->default(1);
            $table->json('question_snapshot_json');
            $table->unsignedInteger('display_order');
            $table->json('option_order_json')->nullable();
            $table->string('section_code', 20)->nullable()->index();
            $table->decimal('max_points', 8, 2)->default(10.00);
            $table->timestamps();
            $table->unique(['attempt_id', 'display_order']);
            $table->index('attempt_id');
        });
    }
    public function down(): void { Schema::dropIfExists('attempt_questions'); }
};
