<?php

namespace Tests\Feature\Examples;

use App\Models\Contact;
use App\Models\Organization;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ContactDirectoryQueryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_data_table_example_applies_string_relation_and_sort_filters(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['name' => 'Acme Labs']);
        $otherOrganization = Organization::factory()->create(['name' => 'Other Co']);
        $priorityTag = Tag::factory()->create(['name' => 'Priority']);
        $prospectTag = Tag::factory()->create(['name' => 'Prospect']);

        $matchingContact = Contact::factory()->for($organization)->create([
            'name' => 'Alpha Person',
            'email' => 'alpha@example.test',
            'created_at' => Carbon::parse('2025-01-20 10:00:00'),
            'updated_at' => Carbon::parse('2025-01-20 10:00:00'),
        ]);
        $matchingContact->tags()->attach($priorityTag);

        $nonMatchingContact = Contact::factory()->for($otherOrganization)->create([
            'name' => 'Beta Person',
            'email' => 'beta@example.test',
            'created_at' => Carbon::parse('2025-01-10 10:00:00'),
            'updated_at' => Carbon::parse('2025-01-10 10:00:00'),
        ]);
        $nonMatchingContact->tags()->attach($prospectTag);

        $response = $this->inertiaGet($user, '/examples/data-table/contacts?' . http_build_query([
            'rows' => 10,
            'page' => 1,
            'sortField' => 'created_at',
            'sortOrder' => -1,
            'filters' => [
                'name' => [
                    'value' => 'Alpha',
                    'matchMode' => 'contains',
                ],
                'organization' => [
                    'value' => $organization->id,
                    'matchMode' => 'equals',
                ],
                'tags' => [
                    'value' => [$priorityTag->id],
                    'matchMode' => 'in',
                ],
            ],
        ]));

        $response->assertInertia(
            fn (AssertableInertia $page): AssertableInertia => $page
                ->component('examples/data-table/contacts/Index')
                ->where('contacts.per_page', 20)
                ->has('contacts.data', 1)
                ->where('contacts.data.0.id', $matchingContact->id)
                ->where('contacts.data.0.name', 'Alpha Person')
                ->where('contacts.data.0.organization.id', $organization->id)
                ->where('contacts.data.0.tags.0.id', $priorityTag->id)
                ->has('contacts.data.0.createdAt')
                ->has('contacts.data.0.updatedAt')
        );
    }

    public function test_paginator_example_applies_in_filters_and_sorting(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $prospectTag = Tag::factory()->create(['name' => 'Prospect']);
        $vipTag = Tag::factory()->create(['name' => 'VIP']);

        $firstMatchingContact = Contact::factory()->for($organization)->create([
            'name' => 'Aaron Prospect',
            'email' => 'aaron@example.test',
        ]);
        $firstMatchingContact->tags()->attach($prospectTag);

        $secondMatchingContact = Contact::factory()->for($organization)->create([
            'name' => 'Zelda Prospect',
            'email' => 'zelda@example.test',
        ]);
        $secondMatchingContact->tags()->attach($prospectTag);

        $nonMatchingContact = Contact::factory()->for($organization)->create([
            'name' => 'Mona Vip',
            'email' => 'mona@example.test',
        ]);
        $nonMatchingContact->tags()->attach($vipTag);

        $response = $this->inertiaGet($user, '/examples/paginator/contacts?' . http_build_query([
            'rows' => 50,
            'page' => 1,
            'sortField' => 'name',
            'sortOrder' => 1,
            'filters' => [
                'tags' => [
                    'value' => [$prospectTag->id],
                    'matchMode' => 'in',
                ],
            ],
        ]));

        $response->assertInertia(
            fn (AssertableInertia $page): AssertableInertia => $page
                ->component('examples/paginator/contacts/Index')
                ->where('contacts.per_page', 50)
                ->has('contacts.data', 2)
                ->where('contacts.data.0.name', 'Aaron Prospect')
                ->where('contacts.data.1.name', 'Zelda Prospect')
        );
    }

    private function inertiaGet(User $user, string $url): TestResponse
    {
        return $this
            ->actingAs($user)
            ->get($url);
    }
}
