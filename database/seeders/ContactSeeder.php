<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::factory()
            ->count(10)
            ->create();

        Contact::factory()
            ->count(100)
            ->recycle($organizations)
            ->create();
    }
}
