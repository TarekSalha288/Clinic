<?php

namespace Database\Seeders;

use App\Models\Day;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\MonthlyLeave;
use App\Models\Patient;
use App\Models\Son;
use App\Models\Symbtom;
use App\Models\User;
use Database\Factories\DepartmentFactory;
use Database\Factories\SymbtomFactory;
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

        Department::factory(count(DepartmentFactory::$departments))->create();
        Symbtom::factory(count(SymbtomFactory::$symptoms))->create();
        User::factory(10)->create();
        $doctorUsers = User::factory(8)->doctor()->create();

        Patient::factory(10)->create();

        Son::factory(5)->create();

        $departments = Department::all();
        foreach ($doctorUsers as $user) {

            $randomDepartment = $departments->random();
            Doctor::factory()->for($user)->create([
                'department_id' => $randomDepartment->id,
                'bio' => fake()->realText(200),
            ]);
        }
    }
}
