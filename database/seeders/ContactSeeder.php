<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Organization;
use App\Models\Tag;
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

        $tags = Tag::factory()
            ->count(10)
            ->create();

        $contacts = Contact::factory()
            ->count(100)
            ->recycle($organizations)
            ->create();

        $contacts->each(function (Contact $contact) use ($tags) {
            $contact->tags()->attach(
                $tags->random(rand(1, 3))
                    ->pluck('id')
                    ->toArray()
            );
        });

        $organizations->each(function (Organization $org) use ($tags) {
            $org->tags()->attach(
                $tags->random(rand(2, 5))
                    ->pluck('id')
                    ->toArray()
            );
        });
    }
}
