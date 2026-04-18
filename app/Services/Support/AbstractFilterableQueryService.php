<?php

namespace App\Services\Support;

use App\Data\Filtering\FilterDefinition;
use App\Data\Filtering\FilterDefinitionData;
use App\Data\Filtering\FilterModeOptionData;
use App\Data\Filtering\FilterRequestData;
use App\Data\Filtering\PaginatedQueryRequestData;
use App\Enums\FilterMatchMode;
use App\Support\Filtering\FiltersByMatchMode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @template TModel of Model
 */
abstract class AbstractFilterableQueryService
{
    /**
     * @return array<int, FilterDefinition>
     */
    abstract protected function filterDefinitions(): array;

    /**
     * @return Builder<TModel>
     */
    abstract protected function baseQuery(): Builder;

    abstract protected function defaultSort(): string;

    /**
     * @return array<int, int>
     */
    protected function perPageOptions(): array
    {
        return [20, 50, 100];
    }

    /**
     * @return array<int, string>
     */
    protected function additionalSortableFields(): array
    {
        return [];
    }

    /**
     * @return array<int, FilterDefinitionData>
     */
    public function filterDefinitionData(): array
    {
        $items = [];

        foreach ($this->filterDefinitions() as $definition) {
            $modes = array_map(
                fn (FilterMatchMode $mode): FilterModeOptionData => new FilterModeOptionData(
                    label: $mode->label(),
                    value: $mode->value,
                ),
                $definition->allowedModes,
            );

            $items[] = new FilterDefinitionData(
                field: $definition->field,
                type: $definition->type,
                modes: $modes,
            );
        }

        return $items;
    }

    /**
     * @return array<string, array{type: string, modes: array<int, array{label: string, value: string}>}>
     */
    public function frontendFilterDefinitions(): array
    {
        $output = [];

        foreach ($this->filterDefinitionData() as $definition) {
            $modes = array_map(
                fn (FilterModeOptionData $mode): array => [
                    'label' => $mode->label,
                    'value' => $mode->value,
                ],
                $definition->modes,
            );

            $output[$definition->field] = [
                'type' => $definition->type,
                'modes' => $modes,
            ];
        }

        return $output;
    }

    /**
     * @return LengthAwarePaginator<int, TModel>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $queryData = PaginatedQueryRequestData::fromRequest($request);
        $perPage = $this->resolvePerPage($queryData->rows);
        $sort = $this->resolveSort($queryData->sortField, $queryData->sortOrder);

        return QueryBuilder::for($this->baseQuery(), $this->buildQueryBuilderRequest($request, $queryData, $sort))
            ->allowedFilters(...$this->allowedFilters())
            ->allowedSorts(...$this->sortableFields())
            ->defaultSort($this->defaultSort())
            ->paginate(perPage: $perPage, page: $queryData->page)
            ->appends($request->query());
    }

    protected function resolvePerPage(int $requestedRows): int
    {
        return in_array($requestedRows, $this->perPageOptions(), true)
            ? $requestedRows
            : $this->perPageOptions()[0];
    }

    protected function resolveSort(?string $sortField, int $sortOrder): ?string
    {
        if ($sortField === null || !in_array($sortField, $this->sortableFields(), true)) {
            return null;
        }

        return $sortOrder === -1
            ? "-$sortField"
            : $sortField;
    }

    protected function buildQueryBuilderRequest(
        Request $request,
        PaginatedQueryRequestData $queryData,
        ?string $sort,
    ): Request {
        $filters = [];

        foreach ($this->filterDefinitions() as $definition) {
            $filterData = $queryData->filters[$definition->field] ?? null;

            if (!$filterData instanceof FilterRequestData) {
                continue;
            }

            if (!is_string($filterData->matchMode) || $this->isEmptyFilterValue($filterData->value)) {
                continue;
            }

            $filters[$definition->field] = [
                'matchMode' => $filterData->matchMode,
                'value' => $filterData->value,
            ];
        }

        $query = ['filter' => $filters];

        if ($sort !== null) {
            $query['sort'] = $sort;
        }

        return Request::create('/' . ltrim($request->path(), '/'), 'GET', $query);
    }

    protected function isEmptyFilterValue(mixed $value): bool
    {
        if (is_array($value)) {
            return array_values(array_filter($value, static fn (mixed $item): bool => $item !== null && $item !== '')) === [];
        }

        return $value === null || $value === '';
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return collect($this->filterDefinitions())
            ->filter(fn (FilterDefinition $definition): bool => $definition->sortable)
            ->map(fn (FilterDefinition $definition): string => $definition->field)
            ->merge($this->additionalSortableFields())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, AllowedFilter>
     */
    protected function allowedFilters(): array
    {
        return array_map(
            fn (FilterDefinition $definition): AllowedFilter => AllowedFilter::custom(
                $definition->field,
                new FiltersByMatchMode(
                    column: $definition->column,
                    type: $definition->type,
                    allowedModes: $definition->allowedModes,
                ),
            ),
            $this->filterDefinitions(),
        );
    }
}
