<?php

namespace App\Http\Controllers\Examples\Paginator;

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
        return Inertia::render('examples/paginator/contacts/Index', [
            'contacts' => $this->contactService->getContacts(ContactFilters::fromRequest($request)),
            'organizations' => fn () => Organization::all(),
            'tags' => fn () => Tag::all(),
        ]);
    }
}
