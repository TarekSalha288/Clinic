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
        Schema::create('previews', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('doctor_id')->references('id')->on('doctors')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('department_id')->references('id')->on('departments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('diagnoseis');
            $table->boolean('diagnoseis_type');// 1 complete diagnos// 0 partial diagnos
            $table->string('medicine');
            $table->string('notes');
            $table->date('date');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('previews');
    }
};