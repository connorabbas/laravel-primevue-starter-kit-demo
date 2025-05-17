<?php

namespace App\Data\DataTransferObjects\Filtering;

use App\Enums\FilterMatchMode;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class ContactFilters extends BaseFilters
{
    public ?string $name = null;
    public ?FilterMatchMode $nameMatchMode = null;
    public ?string $email = null;
    public ?FilterMatchMode $emailMatchMode = null;
    public ?int $organizationId = null;
    public ?FilterMatchMode $organizationIdMatchMode = FilterMatchMode::EQUALS;
    /** @var int[] $tags */
    public ?array $tags = null;
    public ?FilterMatchMode $tagsMatchMode = FilterMatchMode::IN;
    public ?DateTime $createdAt = null;
    public ?FilterMatchMode $createdAtMatchMode = null;

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
            'createdAtMatchMode' => self::getMatchMode($filters, 'created_at'),
        ];
        $tags = self::getFilterValue($filters, 'tags');
        if ($tags && is_array($tags) && count($tags) > 0) {
            $inputFilters['tags'] = $tags;
        }
        $createdAt = self::getFilterValue($filters, 'created_at');
        if ($createdAt) {
            $inputFilters['createdAt'] = Carbon::parse($createdAt);
        }

        $params = [
            ...self::getPaginationFilters($request)->toArray(),
            ...$inputFilters,
        ];

        return self::from($params);
    }
}
