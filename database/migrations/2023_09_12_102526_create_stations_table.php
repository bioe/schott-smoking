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
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->integer('max_pax'); //How many person allow to enter
            $table->integer('stay_duration_seconds'); //How long they allow to smoke
            $table->integer('warning_below_seconds'); //Warning when the time reach below
            $table->integer('disable_next_entry_seconds'); //Prevent re-entry
            $table->integer('door_open_seconds'); //IO to turn off the door again
            $table->integer('annoucement_interval'); //Annoucement Slider
            $table->integer('banner_interval'); //Banner Slider
            $table->string('ip')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
