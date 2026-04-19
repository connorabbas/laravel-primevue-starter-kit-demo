<?php

namespace App\Data\Filtering;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class PaginatedQueryRequestData extends Data
{
    /**
     * @param array<string, FilterRequestData> $filters
     */
    public function __construct(
        public int $page,
        public int $rows,
        public ?string $sortField,
        public int $sortOrder,
        public array $filters,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        /** @var mixed $rawFilters */
        $rawFilters = $request->input('filters', []);

        $filters = [];

        if (is_array($rawFilters)) {
            foreach ($rawFilters as $field => $filter) {
                if (!is_string($field) || !is_array($filter)) {
                    continue;
                }

                $matchMode = $filter['matchMode'] ?? null;
                $value = $filter['value'] ?? null;

                $filters[$field] = new FilterRequestData(
                    matchMode: is_string($matchMode) ? $matchMode : null,
                    value: $value,
                );
            }
        }

        $sortField = $request->string('sortField')->value();

        return new self(
            page: max(1, $request->integer('page', 1)),
            rows: max(1, $request->integer('rows', 20)),
            sortField: $sortField !== '' ? $sortField : null,
            sortOrder: $request->integer('sortOrder', 1),
            filters: $filters,
        );
    }
}
