<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function getUsers(
        ?string $search = null,
        ?string $role = null,
        bool $withTrashed = false
    ): LengthAwarePaginator {

        $query = User::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        if ($search) {
            $query->where(function ($query) use ($search) {

                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere(
                        'username',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone_number',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        return $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'phone_number' => $data['phone_number'] ?? null,
                'password' => $data['password'],
                'role' => $data['role'],
            ]);

            $this->activityLogService->create(
                $user,
                Auth::id()
            );

            return $user;
        });
    }

    public function update(
        User $user,
        array $data
    ): User {

        return DB::transaction(function () use ($user, $data) {

            $user->name = $data['name'];
            $user->username = $data['username'];
            $user->phone_number =
                $data['phone_number'] ?? null;
            $user->role = $data['role'];

            if (!empty($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();

            $this->activityLogService->update(
                $user,
                Auth::id()
            );

            return $user;
        });
    }

    public function softDelete(User $user): void
    {
        DB::transaction(function () use ($user) {

            $user->delete();

            $this->activityLogService->delete(
                $user,
                Auth::id()
            );
        });
    }

    public function restore(User $user): void
    {
        DB::transaction(function () use ($user) {

            $user->restore();

            $this->activityLogService->restore(
                $user,
                Auth::id()
            );
        });
    }

    public function forceDelete(User $user): void
    {
        DB::transaction(function () use ($user) {

            /*
             * Hard delete memang TIDAK masuk activity log.
             */

            ActivityLogService::class;

            $user->forceDelete();
        });
    }
}
