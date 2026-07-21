<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE income_records MODIFY payment_method VARCHAR(50) NOT NULL DEFAULT 'cash'");
        DB::statement("ALTER TABLE expense_records MODIFY payment_method VARCHAR(50) NOT NULL DEFAULT 'cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE income_records MODIFY payment_method ENUM('cash','momo','bank_transfer','cheque','online') NOT NULL DEFAULT 'cash'");
        DB::statement("ALTER TABLE expense_records MODIFY payment_method ENUM('cash','momo','bank_transfer','cheque') NOT NULL DEFAULT 'cash'");
    }
};
