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
        Schema::table('church_settings', function (Blueprint $table) {
            $table->string('pastor_signature_path')->nullable()->after('logo_path');
            $table->string('finance_signature_path')->nullable()->after('pastor_signature_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('church_settings', function (Blueprint $table) {
            $table->dropColumn(['pastor_signature_path', 'finance_signature_path']);
        });
    }
};
