<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sons', function (Blueprint $table) {
            $table->id();


            $table->foreignId('parent_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
            ;

            // Patient relationship - using the same method
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade')
            ;

            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sons');
    }
};