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
        DB::statement('ALTER TABLE cash_book_opening_balances MODIFY payment_method VARCHAR(255) NULL');

        Schema::table('cash_book_opening_balances', function (Blueprint $table) {
            $table->dropUnique('cash_book_opening_balances_financial_year_payment_method_unique');
            $table->foreignId('bank_account_id')->nullable()->after('payment_method')
                ->constrained('bank_accounts')->nullOnDelete();
            $table->unique(
                ['financial_year', 'payment_method', 'bank_account_id'],
                'cash_book_opening_balances_year_method_account_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_book_opening_balances', function (Blueprint $table) {
            $table->dropUnique('cash_book_opening_balances_year_method_account_unique');
            $table->dropConstrainedForeignId('bank_account_id');
            $table->unique(['financial_year', 'payment_method'], 'cash_book_opening_balances_financial_year_payment_method_unique');
        });

        DB::statement('ALTER TABLE cash_book_opening_balances MODIFY payment_method VARCHAR(255) NOT NULL');
    }
};
