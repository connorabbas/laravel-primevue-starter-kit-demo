<?php

namespace App\Services;

use App\Data\DataTransferObjects\Filtering\UserFilters;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function getUsers(UserFilters $filters): LengthAwarePaginator|Collection
    {
        $query = User::query()
            ->when($filters?->name && $filters?->nameMatchMode, function ($query) use ($filters) {
                $query->applyFilter('name', $filters->nameMatchMode, $filters->name);
            })
            ->when($filters?->email && $filters?->emailMatchMode, function ($query) use ($filters) {
                $query->applyFilter('email', $filters->emailMatchMode, $filters->email);
            });

        if ($filters->sortField && $filters->sortDirection) {
            $query->applySort($filters->sortField, $filters->sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $results = ($filters->perPage && $filters->page)
            ? $query->paginate(perPage: $filters->perPage, page: $filters->page)
            : $query->get();

        return $results;
    }
}
