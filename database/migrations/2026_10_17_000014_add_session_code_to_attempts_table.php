<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->dropUnique(['candidate_user_id', 'exam_id', 'attempt_no']);
            $table->string('session_code', 20)->nullable()->after('exam_id')->index();
            $table->unique(
                ['candidate_user_id', 'exam_id', 'session_code', 'attempt_no'],
                'attempt_candidate_exam_session_number_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->dropUnique('attempt_candidate_exam_session_number_unique');
            $table->dropColumn('session_code');
            $table->unique(['candidate_user_id', 'exam_id', 'attempt_no']);
        });
    }
};
