<?php

namespace App\Http\Controllers\Examples\DataTable;

use App\Http\Controllers\Controller;
use App\Models\Contact;
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
        $contacts = Contact::query()
            ->with('organization', 'tags')
            ->paginate(
                perPage: $request->integer('rows', 20),
                page: $request->integer('page', 1)
            );

        return Inertia::render('examples/data-table/contacts/Index', [
            'contacts' => $contacts,
        ]);
    }
}
