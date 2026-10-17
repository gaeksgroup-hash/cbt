<?php

use App\Support\SakEssayRubrics;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->string('username', 80)->nullable()->unique()->after('id');
        });

        Schema::create('candidate_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_user_id')->constrained('candidate_users')->cascadeOnDelete();
            $table->string('entitlement_key', 64);
            $table->string('source', 40)->default('admin');
            $table->foreignId('granted_by_admin_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->unique(['candidate_user_id', 'entitlement_key']);
            $table->index(['entitlement_key', 'revoked_at']);
        });

        Schema::create('attempt_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_user_id')->constrained('candidate_users')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('admin_user_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->unsignedInteger('reset_after_attempt_no')->default(0);
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['candidate_user_id', 'exam_id', 'id']);
        });

        DB::table('exams')->update(['attempt_limit' => 3]);
        foreach (SakEssayRubrics::all() as $code => $rules) {
            DB::table('questions')->where('code', $code)->update([
                'scoring_rules_json' => json_encode($rules, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    public function down(): void
    {
        foreach (array_keys(SakEssayRubrics::all()) as $code) {
            DB::table('questions')->where('code', $code)->update([
                'scoring_rules_json' => json_encode(['mode' => 'manual_review']),
            ]);
        }
        DB::table('exams')->update(['attempt_limit' => null]);
        Schema::dropIfExists('attempt_resets');
        Schema::dropIfExists('candidate_entitlements');
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
