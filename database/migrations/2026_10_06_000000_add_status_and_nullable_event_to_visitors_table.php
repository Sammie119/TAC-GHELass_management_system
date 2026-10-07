<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->change();
            $table->string('status', 10)->default('visit')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // event_id is left nullable: visitors recorded without an event would block reverting it.
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
