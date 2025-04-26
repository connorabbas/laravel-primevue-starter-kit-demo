<?php

namespace App\Data\DataTransferObjects\Filtering\Traits;

use Illuminate\Http\Request;

trait HasPaginatedDataFilters
{
    public ?int $perPage = null;
    public ?int $currentPage = null;
    public ?string $sortField = null;
    public ?string $sortDirection = 'asc';

    public function getPaginationInputFilters(Request $request): array
    {
        return [
            'perPage' => $request->has('rows')
                ? (int) $request->input('rows', 20)
                : null,
            'currentPage' => $request->has('page')
                ? (int) $request->input('page', 0)
                : null,
            'sortField' => $request->input('sortField'),
            'sortDirection' => $request->input('sortOrder', 1) == 1 ? 'asc' : 'desc',
        ];
    }
}
