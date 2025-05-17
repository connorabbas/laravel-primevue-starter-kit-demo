<?php

namespace App\Http\Controllers\Admin;

use App\Data\DataTransferObjects\Filtering\ContactFilters;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Tag;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FilteredContactController extends Controller
{
    public function __construct(protected ContactService $contactService)
    {
        //
    }

    public function index(Request $request): Response
    {
        return Inertia::render('admin/contacts/IndexAdvanced', [
            'contacts' => $this->contactService->getContacts(ContactFilters::fromDataTableRequest($request)),
            'organizations' => fn () => Organization::all(),
            'tags' => fn () => Tag::all(),
        ]);
    }
}
