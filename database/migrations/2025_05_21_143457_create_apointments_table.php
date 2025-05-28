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
        Schema::create('apointments', function (Blueprint $table) {
            $table->id();
             $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('doctor_id')->references('id')->on('doctors')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('department_id')->references('id')->on('departments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('Pointment_data');
            $table->string('apoitment_status');
            $table->string('status');//waiting or accept
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apointments');
    }
};