<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $user = $this->authService->login(
            username: $request->string('username')->toString(),
            password: $request->string('password')->toString(),
            remember: $request->boolean('remember')
        );

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('barista.dashboard');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'exists:users,username',
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.exists' => 'Username tidak ditemukan.',
        ]);

        $user = User::where('username', $request->username)->firstOrFail();

        $adminPhone = config('kopi.admin_whatsapp');

        $message = "Halo Admin Kopi Pusat,\n\n"
            . "Saya ingin meminta reset password akun.\n\n"
            . "Username: {$user->username}\n"
            . "Nama: {$user->name}\n\n"
            . "Mohon bantuan untuk melakukan reset password.\n\n"
            . "Terima kasih.";

        $whatsappUrl = 'https://wa.me/'
            . $adminPhone
            . '?text='
            . urlencode($message);

        return redirect()->away($whatsappUrl);
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil keluar dari sistem.');
    }
}
