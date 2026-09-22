<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_banks')->restrictOnDelete();
            $table->string('code')->unique();
            $table->unsignedInteger('version')->default(1);
            $table->string('type')->default('single_choice')->index();
            $table->string('title')->nullable();
            $table->longText('stem');
            $table->decimal('points', 8, 2)->default(10.00);
            $table->longText('explanation')->nullable();
            $table->text('legal_reference')->nullable();
            $table->json('answer_key_json')->nullable();
            $table->json('scoring_rules_json')->nullable();
            $table->json('metadata_json')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('questions'); }
};
