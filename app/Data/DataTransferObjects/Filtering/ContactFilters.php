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

    public static function fromRequest(Request $request): self
    {
        /** @var array<string, mixed> $inputFilters */
        $inputFilters = $request->input('filters', []);

        $filters = [
            'name' => self::getFilterValue($inputFilters, 'name'),
            'nameMatchMode' => self::getMatchMode($inputFilters, 'name'),
            'email' => self::getFilterValue($inputFilters, 'email'),
            'emailMatchMode' => self::getMatchMode($inputFilters, 'email'),
            'organizationId' => self::getFilterValue($inputFilters, 'organization'),
            //'organizationIdMatchMode' => self::getMatchMode($inputFilters, 'organization'),
            //'tagsMatchMode' => self::getMatchMode($inputFilters, 'tags'),
            'createdAtMatchMode' => self::getMatchMode($inputFilters, 'created_at'),
        ];
        $tags = self::getFilterValue($inputFilters, 'tags');
        if ($tags && is_array($tags) && count($tags) > 0) {
            $filters['tags'] = $tags;
        }
        $createdAt = self::getFilterValue($inputFilters, 'created_at');
        if ($createdAt) {
            $filters['createdAt'] = Carbon::parse($createdAt);
        }

        $params = [
            ...self::getPaginationFilters($request)->toArray(),
            ...$filters,
        ];

        return self::from($params);
    }
}
