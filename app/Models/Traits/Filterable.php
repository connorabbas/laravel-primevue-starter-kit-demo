<?php

namespace App\Models\Traits;

use App\Enums\FilterMatchMode;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

trait Filterable
{
    /**
     * @return string[]
     */
    abstract protected function getFilterableColumns(): array;

    /**
     * Validate if the column is filterable/sortable.
     * @throws InvalidArgumentException
     */
    private function validateColumn(string $column): void
    {
        if (!in_array($column, $this->getFilterableColumns(), true)) {
            throw new InvalidArgumentException("Invalid column name: {$column}.");
        }
    }

    /**
     * Apply a filter to the query based on the column, match mode, and value.
     */
    public function scopeApplyFilter(
        Builder $query,
        string $column,
        FilterMatchMode $matchMode,
        mixed $value
    ): void {
        $this->validateColumn($column);
        $this->applyFilterLogic($query, $column, $matchMode, $value);
    }

    /**
     * Apply a filter to a related model's column.
     * @throws InvalidArgumentException
     */
    public function scopeApplyRelationFilter(
        Builder $query,
        string $relation,
        string $column,
        FilterMatchMode $matchMode,
        mixed $value
    ): void {
        $query->whereHas($relation, function (Builder $query) use ($column, $matchMode, $value) {
            $related = $query->getModel();
            $relatedClass = get_class($related);
            $usedTraits = class_uses($relatedClass);
            $thisTrait = Filterable::class;
            if (!in_array($thisTrait, $usedTraits, true)) {
                throw new InvalidArgumentException("Related model $relatedClass must use the $thisTrait trait.");
            }
            $related->validateColumn($column);
            $this->applyFilterLogic($query, $column, $matchMode, $value);
        });
    }

    /**
     * Apply dynamic sorting to the query.
     */
    public function scopeApplySort(
        Builder $query,
        string $column,
        string $direction = 'asc'
    ): void {
        $this->validateColumn($column);
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($column, $direction);
    }

    /**
     * Apply the filter logic to a query based on the match mode.
     */
    private function applyFilterLogic(
        Builder $query,
        string $column,
        FilterMatchMode $matchMode,
        mixed $value
    ): Builder {
        return match ($matchMode) {
            FilterMatchMode::STARTS_WITH => $query->where($column, 'like', "{$value}%"),
            FilterMatchMode::CONTAINS => $query->where($column, 'like', "%{$value}%"),
            FilterMatchMode::NOT_CONTAINS => $query->where($column, 'not like', "%{$value}%"),
            FilterMatchMode::ENDS_WITH => $query->where($column, 'like', "%{$value}"),
            FilterMatchMode::EQUALS => $query->where($column, '=', $value),
            FilterMatchMode::NOT_EQUALS => $query->where($column, '!=', $value),
            FilterMatchMode::IN => $query->whereIn($column, (array) $value),
            FilterMatchMode::LESS_THAN => $query->where($column, '<', $value),
            FilterMatchMode::LESS_THAN_OR_EQUAL_TO => $query->where($column, '<=', $value),
            FilterMatchMode::GREATER_THAN => $query->where($column, '>', $value),
            FilterMatchMode::GREATER_THAN_OR_EQUAL_TO => $query->where($column, '>=', $value),
            FilterMatchMode::BETWEEN => $query->whereBetween($column, (array) $value),
            FilterMatchMode::DATE_IS => $query->whereDate($column, '=', $value),
            FilterMatchMode::DATE_IS_NOT => $query->whereDate($column, '!=', $value),
            FilterMatchMode::DATE_BEFORE => $query->whereDate($column, '<', $value),
            FilterMatchMode::DATE_AFTER => $query->whereDate($column, '>', $value),
        };
    }
}
