<?php

namespace App\Services\Admin;

use App\Data\Filtering\FilterDefinition;
use App\Enums\FilterMatchMode;
use App\Models\User;
use App\Services\Support\AbstractFilterableQueryService;
use Illuminate\Database\Eloquent\Builder;

/** @extends AbstractFilterableQueryService<User> */
class UserDirectoryQueryService extends AbstractFilterableQueryService
{
    /**
     * @return Builder<User>
     */
    protected function baseQuery(): Builder
    {
        return User::query()->with('roles');
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
