<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): View
    {
        $users = $this->userService->getUsers(
            search: $request->input('search'),
            role: $request->input('role'),
            withTrashed: true
        );

        return view(
            'admin.users.index',
            compact('users')
        );
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(
        StoreUserRequest $request
    ): RedirectResponse {
        $this->userService->create(
            $request->validated()
        );

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User berhasil ditambahkan.'
            );
    }

    public function show(int $id): View
    {
        $user = User::withTrashed()
            ->with('activityLog')
            ->findOrFail($id);

        return view(
            'admin.users.show',
            compact('user')
        );
    }

    public function edit(int $id): View
    {
        $user = User::withTrashed()
            ->findOrFail($id);

        return view(
            'admin.users.edit',
            compact('user')
        );
    }

    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $this->ensureCanManage($user);

        $this->userService->update(
            $user,
            $request->validated()
        );

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User berhasil diperbarui.'
            );
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $this->ensureCanManage($user);

        $this->userService->softDelete($user);

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User berhasil dinonaktifkan.'
            );
    }

    public function restore(int $id): RedirectResponse
    {
        $user = User::withTrashed()
            ->findOrFail($id);

        $this->ensureCanManage($user);

        $this->userService->restore($user);

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User berhasil diaktifkan kembali.'
            );
    }

    public function forceDestroy(int $id): RedirectResponse
    {
        $user = User::withTrashed()
            ->findOrFail($id);

        $this->ensureCanManage($user);

        $this->userService->forceDelete($user);

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User berhasil dihapus permanen.'
            );
    }

    private function ensureCanManage(User $user): void
    {
        abort_if(
            $user->id === Auth::id(),
            403,
            'Anda tidak dapat mengubah akun sendiri.'
        );
    }
}
