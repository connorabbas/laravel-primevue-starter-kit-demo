<?php

namespace App\Services;

use App\Data\DataTransferObjects\Filtering\UserFilters;
use App\Enums\FilterMatchMode;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * @return LengthAwarePaginator<int, User> | Collection<int, User>
     */
    public function getUsers(UserFilters $filters): LengthAwarePaginator|Collection
    {
        $name = $filters->name;
        $nameMatchMode = $filters->nameMatchMode;
        $email = $filters->email;
        $emailMatchMode = $filters->emailMatchMode;
        $createdAt = $filters->createdAt;
        $createdAtMatchMode = $filters->createdAtMatchMode;

        $query = User::query();
        if ($name !== null && $nameMatchMode instanceof FilterMatchMode) {
            $query->applyFilter('name', $nameMatchMode, $name);
        }
        if ($email !== null && $emailMatchMode instanceof FilterMatchMode) {
            $query->applyFilter('email', $emailMatchMode, $email);
        }
        if ($createdAt !== null && $createdAtMatchMode instanceof FilterMatchMode) {
            $query->applyFilter('created_at', $createdAtMatchMode, $createdAt);
        }

        if ($filters->sortField && $filters->sortDirection) {
            $query->applySort($filters->sortField, $filters->sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $results = (is_int($filters->perPage) && is_int($filters->page))
            ? $query->paginate(perPage: $filters->perPage, page: $filters->page)
            : $query->get();

        return $results;
    }
}
