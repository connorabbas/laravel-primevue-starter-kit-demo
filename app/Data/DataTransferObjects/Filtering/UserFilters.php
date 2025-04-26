<?php

namespace App\Data\DataTransferObjects\Filtering;

use App\Data\DataTransferObjects\Filtering\Traits\HasPaginatedDataFilters;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class UserFilters extends Data
{
    use HasPaginatedDataFilters;
    public ?string $name = null;
    public ?string $email = null;

    public static function fromDataTableRequest(Request $request): self
    {
        $filters = $request->input('filters');
        $paginationArgs = (new self())->getPaginationInputFilters($request);
        $filterArgs = [
            ...$paginationArgs,
            'name' => isset($filters['name']['value']) ? $filters['name']['value'] : null,
            'email' => isset($filters['email']['value']) ? $filters['email']['value'] : null,
        ];

        return self::from($filterArgs);
    }
}
