<?php

namespace App\Http\Controllers\Admin;

use App\Data\UserData;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\UserDirectoryQueryService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserDirectoryQueryService $queryService)
    {
    }

    public function index(Request $request): Response
    {
        /** @var LengthAwarePaginator<int, User> $users */
        $users = $this->queryService->paginate($request);

        /** @var LengthAwarePaginator<int, UserData> $users */
        $users = $users->through(fn (User $user): UserData => UserData::fromModel($user));

        return Inertia::render('admin/users/Index', [
            'users' => $users,
        ]);
    }
}
