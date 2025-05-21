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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('birth_date');
            $table->boolean('gender');//1 male 0 female
            $table->integer('age');
            $table->string('blood_type');
            $table->string('chronic_diseases');
            $table->string('medication_allergies');
            $table->string('permanent _medications');
            $table->string('previous_surgeries');
            $table->string('previous_illnesses');
            $table->string('medical_analysis');
            $table->integer('honest_score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
