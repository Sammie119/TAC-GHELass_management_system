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
        Schema::table('cash_book_opening_balances', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->nullable()->after('financial_year');
        });

        Schema::table('cash_book_opening_balances', function (Blueprint $table) {
            $table->dropUnique('cash_book_opening_balances_year_method_account_unique');
            $table->unique(
                ['financial_year', 'month', 'payment_method', 'bank_account_id'],
                'cash_book_opening_balances_year_month_method_account_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_book_opening_balances', function (Blueprint $table) {
            $table->dropUnique('cash_book_opening_balances_year_month_method_account_unique');
            $table->unique(
                ['financial_year', 'payment_method', 'bank_account_id'],
                'cash_book_opening_balances_year_method_account_unique'
            );
            $table->dropColumn('month');
        });
    }
};
