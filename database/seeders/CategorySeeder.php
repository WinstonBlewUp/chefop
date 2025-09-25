<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Project;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Crée les 3 catégories principales
        $categories = [
            'narrative',
            'commercials',
            'music videos',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

       
    }
}
