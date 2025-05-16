<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
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
}
