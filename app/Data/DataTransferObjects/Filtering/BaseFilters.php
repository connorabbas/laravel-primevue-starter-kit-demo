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
            'perPage' => $request->integer('rows', 20),
            'page' => $request->integer('page', 1),
            'sortField' => $request->string('sortField')->value(),
            'sortDirection' => $request->input('sortOrder', 1) == 1 ? 'asc' : 'desc',
        ]);
    }

    /**
     * @param array<string, mixed> $filters
     */
    protected static function getFilterValue(array $filters, string $field): mixed
    {
        return isset($filters[$field]) && is_array($filters[$field])
            ? ($filters[$field]['value'] ?? null)
            : null;
    }

    /**
     * @param array<string, mixed> $filters
     */
    protected static function getMatchMode(array $filters, string $field): ?string
    {
        $value = isset($filters[$field]) && is_array($filters[$field])
            ? ($filters[$field]['matchMode'] ?? null)
            : null;

        return $value !== null ? (string) $value : null;
    }
}
