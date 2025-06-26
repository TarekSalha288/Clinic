<?php

namespace Database\Seeders;

use App\Models\Day;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\MounthlyLeave;
use App\Models\Patient;
use App\Models\Son;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       Day::factory()
    ->count(6)
    ->saturdayToThursday()
    ->create();
    Department::factory(8)->create();
     User::factory(10)->create();
     //
        Patient::factory(10)->create();
        Son::factory(5)->create();

//Doctor::factory(8)->create();

    }
}