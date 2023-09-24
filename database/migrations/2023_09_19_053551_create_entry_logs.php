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
        Schema::create('entry_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('station_id');
            $table->string('card_id')->nullable();
            $table->timestamp('enter_time')->nullable();
            $table->timestamp('exit_time')->nullable();
            $table->integer('stay_duration_seconds')->nullable(); //From station setting
            $table->integer('actual_stay_duration_seconds')->nullable(); //How long is the stay enter + exit time
            $table->integer('disable_next_entry_seconds')->nullable();
            $table->integer('overstay_seconds')->nullable();
            $table->boolean('maintenance')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'station_id'], 'emp_sta_id');
        });

        Schema::create('raw_entry_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('station_id');
            $table->string('card_id')->nullable();
            $table->timestamp('enter_time')->nullable();
            $table->timestamp('exit_time')->nullable();
            $table->integer('stay_duration_seconds')->nullable(); //From station setting
            $table->integer('actual_stay_duration_seconds')->nullable(); //How long is the stay enter + exit time
            $table->integer('disable_next_entry_seconds')->nullable();
            $table->integer('overstay_seconds')->nullable();
            $table->boolean('maintenance')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'station_id'], 'emp_sta_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_logs');
        Schema::dropIfExists('raw_entry_logs');
    }
};
