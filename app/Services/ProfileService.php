<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /**
     * Update informasi profile user.
     */
    public function updateProfile(
        User $user,
        array $data
    ): User {

        return DB::transaction(function () use (
            $user,
            $data
        ) {

            $user->update([
                'name' => $data['name'],
                'username' => $data['username'],
                'phone_number' => $data['phone_number'] ?? null,
            ]);

            /*
             * Activity log akan ditangani
             * oleh mekanisme activity log
             * yang sudah kita gunakan di project.
             */

            return $user->fresh();
        });
    }


    /**
     * Update password user.
     */
    public function updatePassword(
        User $user,
        string $password
    ): void {

        DB::transaction(function () use (
            $user,
            $password
        ) {

            $user->update([
                'password' => Hash::make($password),
            ]);

            /*
             * Jangan pernah menyimpan password
             * ke activity_log.
             *
             * Activity log hanya mencatat
             * bahwa password telah diperbarui.
             */
        });
    }
}
