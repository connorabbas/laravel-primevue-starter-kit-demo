<?php

namespace App\Http\Controllers\Examples\Paginator;

use App\Data\ContactData;
use App\Data\OrganizationData;
use App\Data\TagData;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Tag;
use App\Services\Examples\ContactDirectoryQueryService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function __construct(private readonly ContactDirectoryQueryService $queryService)
    {
    }

    public function index(Request $request): Response
    {
        /** @var LengthAwarePaginator<int, Contact> $contacts */
        $contacts = $this->queryService->paginate($request);

        /** @var LengthAwarePaginator<int, ContactData> $contacts */
        $contacts = $contacts->through(fn (Contact $contact): ContactData => ContactData::fromModel($contact));

        return Inertia::render('examples/paginator/contacts/Index', [
            'contacts' => $contacts,
            'organizations' => fn (): array => Organization::query()
                ->orderBy('name')
                ->get()
                ->map(fn (Organization $organization): OrganizationData => OrganizationData::fromModel($organization))
                ->all(),
            'tags' => fn (): array => Tag::query()
                ->orderBy('name')
                ->get()
                ->map(fn (Tag $tag): TagData => TagData::fromModel($tag))
                ->all(),
        ]);
    }
}
