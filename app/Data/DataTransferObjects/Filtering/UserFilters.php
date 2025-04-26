<?php

namespace App\Data\DataTransferObjects\Filtering;

use App\Data\DataTransferObjects\Filtering\Traits\HasSortedPaginatedDataFilters;
use App\Enums\FilterMatchMode;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class UserFilters extends Data
{
    use HasSortedPaginatedDataFilters;

    public ?string $name = null;
    public ?FilterMatchMode $nameMatchMode = null;
    public ?string $email = null;
    public ?FilterMatchMode $emailMatchMode = null;

    public static function fromDataTableRequest(Request $request): self
    {
        $filters = $request->input('filters');
        $sortedPaginatedArgs = (new self())->getSortedPaginationInputFilters($request);
        $filterArgs = [
            ...$sortedPaginatedArgs,
            'name' => isset($filters['name']['value']) ? $filters['name']['value'] : null,
            'nameMatchMode' => isset($filters['name']['matchMode']) ? $filters['name']['matchMode'] : null,
            'email' => isset($filters['email']['value']) ? $filters['email']['value'] : null,
            'emailMatchMode' => isset($filters['email']['matchMode']) ? $filters['email']['matchMode'] : null,
        ];

        return self::from($filterArgs);
    }
}
