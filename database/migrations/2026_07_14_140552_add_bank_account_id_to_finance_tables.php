<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'income_records' => 'payment_method',
        'expense_records' => 'payment_method',
        'pledge_payments' => 'payment_method',
        'petty_cash_transactions' => 'category',
        'financial_requests' => 'payment_method',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName => $afterColumn) {
            Schema::table($tableName, function (Blueprint $table) use ($afterColumn) {
                $table->foreignId('bank_account_id')->nullable()->after($afterColumn)
                    ->constrained('bank_accounts')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName => $afterColumn) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('bank_account_id');
            });
        }
    }
};
