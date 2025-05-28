<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apointment_symbtoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apointment_id')->references('id')->on('apointments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('symbtom_id')->references('id')->on('symbtoms')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apointment_symbtoms');
    }
};
