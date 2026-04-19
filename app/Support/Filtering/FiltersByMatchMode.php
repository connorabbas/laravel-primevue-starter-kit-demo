<?php

namespace App\Support\Filtering;

use App\Enums\FilterMatchMode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\Filters\Filter;

/** @implements Filter<Model> */
class FiltersByMatchMode implements Filter
{
    /**
     * @param array<int, FilterMatchMode> $allowedModes
     */
    public function __construct(
        private readonly string $column,
        private readonly string $type,
        private readonly array $allowedModes,
    ) {
    }

    /**
     * @param Builder<Model> $query
     */
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        if (!is_array($value)) {
            return;
        }

        $mode = FilterMatchMode::tryFrom((string) ($value['matchMode'] ?? $value['op'] ?? ''));

        if (!$mode instanceof FilterMatchMode || !in_array($mode, $this->allowedModes, true)) {
            return;
        }

        $normalizedValue = $this->normalizeValue($value['value'] ?? null, $mode);

        if ($normalizedValue === null) {
            return;
        }

        if (str_contains($this->column, '.')) {
            [$relation, $relationColumn] = $this->parseRelationColumn();

            $query->whereHas($relation, function (Builder $relationQuery) use ($relationColumn, $mode, $normalizedValue): void {
                $this->applyMatchModeQuery($relationQuery, $mode, $normalizedValue, $relationColumn);
            });

            return;
        }

        $this->applyMatchModeQuery($query, $mode, $normalizedValue, $this->column);
    }

    /**
     * @param Builder<Model> $query
     */
    private function applyMatchModeQuery(
        Builder $query,
        FilterMatchMode $mode,
        mixed $normalizedValue,
        string $column,
    ): void {
        $qualifiedColumn = str_contains($column, '.')
            ? $column
            : $query->qualifyColumn($column);

        $stringValue = is_array($normalizedValue) ? null : (string) $normalizedValue;

        match ($mode) {
            FilterMatchMode::STARTS_WITH => $query->where($qualifiedColumn, 'like', "{$stringValue}%"),
            FilterMatchMode::CONTAINS => $query->where($qualifiedColumn, 'like', "%{$stringValue}%"),
            FilterMatchMode::NOT_CONTAINS => $query->where($qualifiedColumn, 'not like', "%{$stringValue}%"),
            FilterMatchMode::ENDS_WITH => $query->where($qualifiedColumn, 'like', "%{$stringValue}"),
            FilterMatchMode::EQUALS => $query->where($qualifiedColumn, '=', $normalizedValue),
            FilterMatchMode::NOT_EQUALS => $query->where($qualifiedColumn, '!=', $normalizedValue),
            FilterMatchMode::IN => is_array($normalizedValue)
                ? $query->whereIn($qualifiedColumn, $normalizedValue)
                : null,
            FilterMatchMode::LESS_THAN => $query->where($qualifiedColumn, '<', $normalizedValue),
            FilterMatchMode::LESS_THAN_OR_EQUAL_TO => $query->where($qualifiedColumn, '<=', $normalizedValue),
            FilterMatchMode::GREATER_THAN => $query->where($qualifiedColumn, '>', $normalizedValue),
            FilterMatchMode::GREATER_THAN_OR_EQUAL_TO => $query->where($qualifiedColumn, '>=', $normalizedValue),
            FilterMatchMode::BETWEEN => is_array($normalizedValue)
                ? $this->applyBetween($query, $normalizedValue, $qualifiedColumn)
                : null,
            FilterMatchMode::DATE_IS => $query->whereDate($qualifiedColumn, '=', $stringValue),
            FilterMatchMode::DATE_IS_NOT => $query->whereDate($qualifiedColumn, '!=', $stringValue),
            FilterMatchMode::DATE_BEFORE => $query->whereDate($qualifiedColumn, '<', $stringValue),
            FilterMatchMode::DATE_AFTER => $query->whereDate($qualifiedColumn, '>', $stringValue),
        };
    }

    /**
     * @return array{string, string}
     */
    private function parseRelationColumn(): array
    {
        $segments = explode('.', $this->column);
        $relation = (string) array_shift($segments);
        $column = implode('.', $segments);

        return [$relation, $column];
    }

    /**
     * @param Builder<Model> $query
     * @param array<int, string|int|float|bool> $value
     */
    private function applyBetween(Builder $query, array $value, string $column): void
    {
        if (count($value) !== 2) {
            return;
        }

        [$start, $end] = $value;

        if ($this->type === 'date') {
            $query
                ->whereDate($column, '>=', (string) $start)
                ->whereDate($column, '<=', (string) $end);

            return;
        }

        $query->whereBetween($column, [$start, $end]);
    }

    /**
     * @return array<int, string|int|float|bool>|string|int|float|bool|null
     */
    private function normalizeValue(mixed $rawValue, FilterMatchMode $mode): string|int|float|bool|array|null
    {
        if (in_array($mode, [FilterMatchMode::IN, FilterMatchMode::BETWEEN], true)) {
            if (!is_array($rawValue) || count($rawValue) === 0) {
                return null;
            }

            $normalized = array_values(array_filter(
                array_map(fn (mixed $value): string|int|float|bool|null => $this->normalizeScalar($value), $rawValue),
                static fn (mixed $value): bool => $value !== null,
            ));

            if ($normalized === []) {
                return null;
            }

            return $normalized;
        }

        return $this->normalizeScalar($rawValue);
    }

    private function normalizeScalar(mixed $value): string|int|float|bool|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($this->type === 'date') {
            try {
                return Carbon::parse((string) $value)->toDateString();
            } catch (\Throwable) {
                return null;
            }
        }

        if ($this->type === 'number') {
            if (!is_numeric($value)) {
                return null;
            }

            if (str_contains((string) $value, '.')) {
                return (float) $value;
            }

            return (int) $value;
        }

        if ($this->type === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return (string) $value;
    }
}
