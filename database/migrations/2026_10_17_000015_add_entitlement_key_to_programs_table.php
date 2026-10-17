<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('entitlement_key', 64)->nullable()->after('description')->index();
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['entitlement_key']);
            $table->dropColumn('entitlement_key');
        });
    }
};
