<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'name' => 'Cardiology',
                'description' => 'Specializes in the diagnosis and treatment of heart conditions.',
                'image' => 'cardiology.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pediatrics',
                'description' => 'Focuses on the medical care of infants, children, and adolescents.',
                'image' => 'pediatrics.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Neurology',
                'description' => 'Deals with disorders of the nervous system.',
                'image' => 'neurology.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
