<?php

namespace App\Data\DataTransferObjects\Filtering;

use App\Enums\FilterMatchMode;
use Illuminate\Http\Request;

class UserFilters extends BaseFilters
{
    public ?string $name = null;
    public ?FilterMatchMode $nameMatchMode = null;
    public ?string $email = null;
    public ?FilterMatchMode $emailMatchMode = null;

    public static function fromRequest(Request $request): self
    {
        /** @var array<string, mixed> $filters */
        $filters = $request->input('filters', []);

        return self::from([
            ...self::getPaginationFilters($request)->toArray(),
            'name' => self::getFilterValue($filters, 'name'),
            'nameMatchMode' => self::getMatchMode($filters, 'name'),
            'email' => self::getFilterValue($filters, 'email'),
            'emailMatchMode' => self::getMatchMode($filters, 'email'),
        ]);
    }
}
