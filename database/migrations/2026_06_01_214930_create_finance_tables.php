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
        // Income records (tithes, offerings, etc.)
        Schema::create('income_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('category'); // tithe, offering, thanksgiving, welfare, pledge, other
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('GHS');
            $table->decimal('amount_ghs', 12, 2)->default(0); // converted amount
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'momo', 'bank_transfer', 'cheque', 'online'])->default('cash');
            $table->string('reference')->nullable(); // transaction ref
            $table->string('event_id')->nullable(); // linked event
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Expense records
        Schema::create('expense_records', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // salaries, utilities, maintenance, events, outreach, other
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('GHS');
            $table->decimal('amount_ghs', 12, 2)->default(0);
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->date('expense_date');
            $table->enum('payment_method', ['cash', 'momo', 'bank_transfer', 'cheque'])->default('cash');
            $table->string('payee')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('attachment')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Online payment requests (member self-service)
        Schema::create('online_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->string('category');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('GHS');
            $table->string('phone')->nullable(); // for MoMo
            $table->string('reference')->nullable();
            $table->string('provider')->default('momo'); // momo, card
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_records');
        Schema::dropIfExists('expense_records');
        Schema::dropIfExists('online_payments');
    }
};
