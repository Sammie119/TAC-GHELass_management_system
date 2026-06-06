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
        Schema::create('new_souls', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('area')->nullable();           // location/area
            $table->date('date_won');
            $table->foreignId('won_by')                  // which member won them
            ->nullable()
                ->constrained('members')
                ->nullOnDelete();
            $table->foreignId('assigned_to')             // staff following up
            ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('status', [
                'new',
                'contacted',
                'attending',
                'baptised',
                'converted',
                'backslidden',
            ])->default('new');
            $table->string('church_background')->nullable();
            $table->date('salvation_prayer_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('soul_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soul_id')
                ->constrained('new_souls')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('method', ['phone', 'visit', 'sms', 'email', 'church'])->default('phone');
            $table->enum('outcome', ['no_answer', 'spoke', 'visited_church', 'not_interested', 'other'])->default('spoke');
            $table->text('notes')->nullable();
            $table->date('followup_date');
            $table->date('next_followup_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_souls');
        Schema::dropIfExists('soul_followups');
    }
};
