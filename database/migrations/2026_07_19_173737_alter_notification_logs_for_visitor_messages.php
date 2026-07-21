<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE notification_logs MODIFY member_id BIGINT UNSIGNED NULL');

        Schema::table('notification_logs', function (Blueprint $table) {
            $table->foreignId('visitor_id')->nullable()->after('member_id')->constrained('visitors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visitor_id');
        });

        DB::statement('ALTER TABLE notification_logs MODIFY member_id BIGINT UNSIGNED NOT NULL');
    }
};
