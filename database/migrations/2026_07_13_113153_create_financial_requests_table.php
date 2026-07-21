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
        Schema::create('financial_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category');
            $table->string('description');
            $table->string('payee')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('GHS');
            $table->decimal('amount_ghs', 12, 2)->default(0);
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->string('payment_method', 50)->default('cash');
            $table->date('request_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamp('pastor_approved_at')->nullable();
            $table->foreignId('pastor_approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('super_admin_approved_at')->nullable();
            $table->foreignId('super_admin_approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();

            $table->string('pv_number')->nullable()->unique();
            $table->timestamp('pv_generated_at')->nullable();
            $table->foreignId('pv_generated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('expense_record_id')->nullable()->constrained('expense_records')->nullOnDelete();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_requests');
    }
};
