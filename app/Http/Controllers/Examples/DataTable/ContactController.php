<?php

namespace App\Http\Controllers\Examples\DataTable;

use App\Data\DataTransferObjects\Filtering\ContactFilters;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Tag;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function __construct(protected ContactService $contactService)
    {
        //
    }

    public function index(Request $request): Response
    {
        return Inertia::render('examples/data-table/contacts/Index', [
            'contacts' => $this->contactService->getContacts(ContactFilters::fromDataTableRequest($request)),
            'organizations' => fn () => Organization::all(),
            'tags' => fn () => Tag::all(),
        ]);
    }
}
