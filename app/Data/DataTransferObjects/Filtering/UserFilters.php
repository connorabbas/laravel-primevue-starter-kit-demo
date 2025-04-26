<?php

namespace App\Data\DataTransferObjects\Filtering;

use App\Data\DataTransferObjects\Filtering\Traits\HasSortedPaginatedDataFilters;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class UserFilters extends Data
{
    use HasSortedPaginatedDataFilters;

    public ?string $name = null;
    public ?string $email = null;

    public static function fromDataTableRequest(Request $request): self
    {
        $filters = $request->input('filters');
        $sortedPaginatedArgs = (new self())->getSortedPaginationInputFilters($request);
        $filterArgs = [
            ...$sortedPaginatedArgs,
            'name' => isset($filters['name']['value']) ? $filters['name']['value'] : null,
            'email' => isset($filters['email']['value']) ? $filters['email']['value'] : null,
        ];

        return self::from($filterArgs);
    }
}
