<?php

namespace App\Http\Controllers\Admin;

use App\Data\DataTransferObjects\Filtering\UserFilters;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(public UserService $userService)
    {
    }

    public function index(Request $request): Response
    {
        //dd(UserFilters::fromDataTableRequest($request));
        return Inertia::render('admin/users/Index', [
            'users' => $this->userService->getUsers(
                UserFilters::fromDataTableRequest($request)
            ),
        ]);
    }
}
