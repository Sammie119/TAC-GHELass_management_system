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
        Schema::create('cell_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('area')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('leader_id')
                ->nullable()
                ->constrained('members')
                ->nullOnDelete();
            $table->foreignId('assistant_leader_id')
                ->nullable()
                ->constrained('members')
                ->nullOnDelete();
            $table->enum('meeting_day', [
                'sunday','monday','tuesday','wednesday',
                'thursday','friday','saturday'
            ])->nullable();
            $table->time('meeting_time')->nullable();
            $table->string('meeting_venue')->nullable();
            $table->enum('status', ['active','inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot: members belong to cell groups
        Schema::create('cell_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cell_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->date('joined_date')->nullable();
            $table->boolean('is_leader')->default(false);
            $table->timestamps();
            $table->unique(['cell_group_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cell_groups');
        Schema::dropIfExists('cell_group_members');
    }
};
