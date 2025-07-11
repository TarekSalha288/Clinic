<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            // Use constrained() for better readability and automatic reference
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->unique(); // Add unique if one-to-one relationship

            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnDelete();

            $table->text('bio');
            $table->integer('subscription')->nullable();
            $table->integer('price_of_examination')->nullable();
            $table->timestamps();
        });

        // For SQLite, enable foreign key constraints
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
