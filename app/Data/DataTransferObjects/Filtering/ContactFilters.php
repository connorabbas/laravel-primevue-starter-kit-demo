<?php

namespace App\Data\DataTransferObjects\Filtering;

use App\Enums\FilterMatchMode;
use Illuminate\Http\Request;

class ContactFilters extends BaseFilters
{
    public ?string $name = null;
    public ?FilterMatchMode $nameMatchMode = null;
    public ?string $email = null;
    public ?FilterMatchMode $emailMatchMode = null;
    public ?int $organizationId = null;
    public ?FilterMatchMode $organizationIdMatchMode = FilterMatchMode::EQUALS;
    public ?array $tags = null;
    public ?FilterMatchMode $tagsMatchMode = FilterMatchMode::IN;

    public static function fromDataTableRequest(Request $request): self
    {
        /** @var array<string, mixed> $filters */
        $filters = $request->input('filters', []);

        $inputFilters = [
            'name' => self::getFilterValue($filters, 'name'),
            'nameMatchMode' => self::getMatchMode($filters, 'name'),
            'email' => self::getFilterValue($filters, 'email'),
            'emailMatchMode' => self::getMatchMode($filters, 'email'),
            'organizationId' => self::getFilterValue($filters, 'organization'),
            //'organizationIdMatchMode' => self::getMatchMode($filters, 'organization'),
            //'tagsMatchMode' => self::getMatchMode($filters, 'tags'),
        ];
        $tags = self::getFilterValue($filters, 'tags');
        if ($tags && is_array($tags) && count($tags) > 0) {
            $inputFilters['tags'] = $tags;
        }

        $params = [
            ...self::getPaginationFilters($request)->toArray(),
            ...$inputFilters,
        ];

        return self::from($params);
    }
}
