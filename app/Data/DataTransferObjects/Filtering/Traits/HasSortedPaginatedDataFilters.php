<?php

namespace App\Data\DataTransferObjects\Filtering\Traits;

use Illuminate\Http\Request;

trait HasSortedPaginatedDataFilters
{
    public ?int $perPage = null;
    public ?int $currentPage = null;
    public ?string $sortField = null;
    public ?string $sortDirection = 'asc';

    public function getSortedPaginationInputFilters(Request $request): array
    {
        return [
            'perPage' => (int) $request->input('rows', 20),
            'currentPage' => (int) $request->input('page', 1),
            'sortField' => $request->input('sortField'),
            'sortDirection' => $request->input('sortOrder', 1) == 1 ? 'asc' : 'desc',
        ];
    }
}
