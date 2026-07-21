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
        Schema::create('petty_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['replenishment', 'disbursement']);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('GHS');
            $table->decimal('amount_ghs', 12, 2)->default(0);
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->string('category')->nullable();
            $table->string('description');
            $table->string('payee')->nullable();
            $table->foreignId('custodian_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('receipt_number')->nullable();
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('expense_record_id')->nullable()->constrained('expense_records')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_transactions');
    }
};
