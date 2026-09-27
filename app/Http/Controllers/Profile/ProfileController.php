<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService
    ) {}


    /**
     * Display profile page.
     */
    public function index(): View
    {
        return view(
            'profile.index',
            [
                'user' => Auth::user(),
            ]
        );
    }


    /**
     * Update profile information.
     */
    public function update(
        UpdateProfileRequest $request
    ): RedirectResponse {

        $this->profileService->updateProfile(
            Auth::user(),
            $request->validated()
        );

        return back()->with(
            'success',
            'Informasi profil berhasil diperbarui.'
        );
    }


    /**
     * Update password.
     */
    public function updatePassword(
        UpdatePasswordRequest $request
    ): RedirectResponse {

        $this->profileService->updatePassword(
            Auth::user(),
            $request->validated('password')
        );

        return back()->with(
            'success',
            'Password berhasil diperbarui.'
        );
    }
}
