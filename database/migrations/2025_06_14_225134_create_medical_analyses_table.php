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
        Schema::create('medical_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('preview_id')->references('id')->on('previews')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('medical_analysis_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_analyses');
    }
};
