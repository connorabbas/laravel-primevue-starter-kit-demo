<?php

namespace App\Http\Controllers\Admin;

use App\Data\DataTransferObjects\Filtering\ContactFilters;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Organization;
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
        $contacts = Contact::paginate(
            perPage: $request->integer('rows', 20),
            page: $request->integer('page', 1)
        );

        return Inertia::render('admin/contacts/Index', [
            'contacts' => $contacts,
        ]);
    }

    public function indexA(Request $request): Response
    {
        return Inertia::render('admin/contacts/IndexAdvanced', [
            'contacts' => $this->contactService->getContacts(ContactFilters::fromDataTableRequest($request)),
            'organizations' => fn () => Organization::all()
        ]);
    }
}
