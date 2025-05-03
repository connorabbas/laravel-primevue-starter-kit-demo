<?php

namespace App\Data\DataTransferObjects\Filtering;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

abstract class BaseFilters extends Data
{
    public ?int $perPage = 20;
    public ?int $page = 1;
    public ?string $sortField = null;
    public ?string $sortDirection = 'asc';

    protected static function getPaginationFilters(Request $request): self
    {
        return self::from([
            'perPage' => (int) $request->input('rows', 20),
            'page' => (int) $request->input('page', 1),
            'sortField' => $request->input('sortField'),
            'sortDirection' => $request->input('sortOrder', 1) == 1 ? 'asc' : 'desc',
        ]);
    }

    protected static function getFilterValue(array $filters, string $field): mixed
    {
        return $filters[$field]['value'] ?? null;
    }

    protected static function getMatchMode(array $filters, string $field): ?string
    {
        return $filters[$field]['matchMode'] ?? null;
    }
}