<?php

namespace Database\Seeders;

use App\Models\{Job, Attribute, JobAttributeValue};
use Illuminate\Database\Seeder;

class JobAttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        Job::each(function ($job) {
            JobAttributeValue::factory()
                ->count(rand(1, 5))
                ->create(['job_id' => $job->id]);
        });
    }
}
