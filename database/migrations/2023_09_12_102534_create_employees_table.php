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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('card_id')->unique();
            $table->string('name');
            $table->boolean('maintenance')->default(false); //Maintenance user, bypass checking
            $table->unsignedBigInteger('origin_id')->nullable();
            $table->unsignedBigInteger('cost_center_id')->index('cost_center_id')->nullable();
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
        Schema::dropIfExists('employees');
    }
};
