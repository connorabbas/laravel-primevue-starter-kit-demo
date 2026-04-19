<?php

namespace App\Services\Examples;

use App\Data\Filtering\FilterDefinition;
use App\Enums\FilterMatchMode;
use App\Models\Contact;
use App\Services\Support\AbstractFilterableQueryService;
use Illuminate\Database\Eloquent\Builder;

/** @extends AbstractFilterableQueryService<Contact> */
class ContactDirectoryQueryService extends AbstractFilterableQueryService
{
    /**
     * @return Builder<Contact>
     */
    protected function baseQuery(): Builder
    {
        return Contact::query()->with(['organization', 'tags']);
    }

    protected function defaultSort(): string
    {
        return '-created_at';
    }

    /**
     * @return array<int, FilterDefinition>
     */
    protected function filterDefinitions(): array
    {
        return [
            FilterDefinition::make('name', 'string', [
                FilterMatchMode::STARTS_WITH,
                FilterMatchMode::CONTAINS,
                FilterMatchMode::NOT_CONTAINS,
                FilterMatchMode::ENDS_WITH,
                FilterMatchMode::EQUALS,
                FilterMatchMode::NOT_EQUALS,
            ]),
            FilterDefinition::make('email', 'string', [
                FilterMatchMode::STARTS_WITH,
                FilterMatchMode::CONTAINS,
                FilterMatchMode::NOT_CONTAINS,
                FilterMatchMode::ENDS_WITH,
                FilterMatchMode::EQUALS,
                FilterMatchMode::NOT_EQUALS,
            ]),
            FilterDefinition::make(
                field: 'organization',
                type: 'number',
                allowedModes: [FilterMatchMode::EQUALS],
                column: 'organization_id',
                sortable: false,
            ),
            FilterDefinition::make(
                field: 'tags',
                type: 'number',
                allowedModes: [FilterMatchMode::IN],
                column: 'tags.taggables.tag_id',
                sortable: false,
            ),
            FilterDefinition::make('created_at', 'date', [
                FilterMatchMode::DATE_IS,
                FilterMatchMode::DATE_IS_NOT,
                FilterMatchMode::DATE_BEFORE,
                FilterMatchMode::DATE_AFTER,
                FilterMatchMode::BETWEEN,
            ]),
        ];
    }
}
