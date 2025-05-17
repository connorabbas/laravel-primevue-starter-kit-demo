<?php

namespace App\Services;

use App\Data\DataTransferObjects\Filtering\ContactFilters;
use App\Enums\FilterMatchMode;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactService
{
    /**
     * @return LengthAwarePaginator<int, Contact> | Collection<int, Contact>
     */
    public function getContacts(ContactFilters $filters): LengthAwarePaginator|Collection
    {
        $name = $filters->name;
        $nameMatchMode = $filters->nameMatchMode;
        $email = $filters->email;
        $emailMatchMode = $filters->emailMatchMode;
        $organizationId = $filters->organizationId;
        $organizationIdMatchMode = $filters->organizationIdMatchMode;
        $tags = $filters->tags;
        $tagsMatchMode = $filters->tagsMatchMode;

        $query = Contact::query()->with(['organization', 'tags']);
        if ($name !== null && $nameMatchMode instanceof FilterMatchMode) {
            $query->applyFilter('name', $nameMatchMode, $name);
        }
        if ($email !== null && $emailMatchMode instanceof FilterMatchMode) {
            $query->applyFilter('email', $emailMatchMode, $email);
        }
        if ($organizationId !== null && $organizationIdMatchMode instanceof FilterMatchMode) {
            $query->applyRelationFilter('organization', 'id', $organizationIdMatchMode, $organizationId);
        }
        if (is_array($tags) && $tagsMatchMode instanceof FilterMatchMode) {
            $query->applyRelationFilter('tags', 'id', $tagsMatchMode, $tags);
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
