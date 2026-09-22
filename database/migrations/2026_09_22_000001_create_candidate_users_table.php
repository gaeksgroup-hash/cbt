<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('candidate_users', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('pre_generated')->index();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('candidate_users'); }
};
