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
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropUnique('budgets_financial_year_category_month_unique');
            $table->dropColumn('category');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('budget_line_id')->after('financial_year')->constrained('budget_lines')->restrictOnDelete();
            $table->unique(['financial_year', 'budget_line_id', 'month'], 'budgets_financial_year_budget_line_month_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropUnique('budgets_financial_year_budget_line_month_unique');
            $table->dropConstrainedForeignId('budget_line_id');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->string('category')->after('financial_year');
            $table->unique(['financial_year', 'category', 'month'], 'budgets_financial_year_category_month_unique');
        });
    }
};
