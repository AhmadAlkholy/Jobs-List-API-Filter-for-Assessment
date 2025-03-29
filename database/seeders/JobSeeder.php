<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Language;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        Job::factory()
            ->count(10)
            ->hasAttached(Language::inRandomOrder()->limit(3)->get(), [], 'languages')
            ->hasAttached(Category::inRandomOrder()->limit(3)->get(), [], 'categories')
            ->hasAttached(Location::inRandomOrder()->limit(3)->get(), [], 'locations')
            ->create();
    }
}
