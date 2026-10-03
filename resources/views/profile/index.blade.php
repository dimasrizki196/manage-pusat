@if (auth()->user()->role === 'barista')
    @extends('layouts.barista')
@else
    @extends('layouts.app')
@endif

@section('title', 'Profile')

@section('content')
    <div class="profile-page">
        {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">
                    Profile
                </h1>
                <p class="page-subtitle">
                    Kelola informasi pribadi dan keamanan akun Anda.
                </p>
            </div>
        </div>

        {{-- =====================================================
         SUCCESS MESSAGE
    ====================================================== --}}
        @if (session('success'))
            <div class="profile-alert profile-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>
                    {{ session('success') }}
                </span>
            </div>
        @endif

        {{-- =====================================================
         VALIDATION ERROR
    ====================================================== --}}
        @if ($errors->any())
            <div class="profile-alert profile-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>
                    <strong>
                        Terdapat kesalahan.
                    </strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="profile-grid">
            {{-- =================================================
             PROFILE INFORMATION
        ================================================== --}}
            <section class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-icon">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <h2>
                            Informasi Profil
                        </h2>
                        <p>
                            Perbarui informasi akun Anda.
                        </p>
                    </div>
                </div>

                <div class="profile-card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- NAME --}}
                        <div class="profile-field">
                            <label for="name" class="profile-label">
                                Nama Lengkap
                            </label>

                            <input type="text" id="name" name="name"
                                class="profile-input @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" autocomplete="name" required>

                            @error('name')
                                <div class="profile-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- USERNAME --}}
                        <div class="profile-field">
                            <label for="username" class="profile-label">
                                Username
                            </label>

                            <input type="text" id="username" name="username"
                                class="profile-input @error('username') is-invalid @enderror"
                                value="{{ old('username', $user->username) }}" autocomplete="username" required>

                            @error('username')
                                <div class="profile-error">
                                    {{ $message }}
                                </div>
                            @enderror

                            <small class="profile-help">
                                Username digunakan untuk login.
                            </small>
                        </div>

                        {{-- PHONE --}}
                        <div class="profile-field">
                            <label for="phone_number" class="profile-label">
                                Nomor WhatsApp
                            </label>

                            <input type="text" id="phone_number" name="phone_number"
                                class="profile-input @error('phone_number') is-invalid @enderror"
                                value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel"
                                placeholder="Contoh: 081234567890">

                            @error('phone_number')
                                <div class="profile-error">
                                    {{ $message }}
                                </div>
                            @enderror

                            <small class="profile-help">
                                Digunakan untuk kebutuhan komunikasi dan pemulihan password.
                            </small>
                        </div>

                        {{-- ROLE --}}
                        <div class="profile-field">
                            <label for="role" class="profile-label">
                                Role
                            </label>

                            <div class="profile-readonly">
                                <div class="profile-readonly-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>

                                <span>
                                    {{ ucfirst($user->role) }}
                                </span>

                                <span class="profile-readonly-badge">
                                    Dikelola Admin
                                </span>
                            </div>
                        </div>

                        {{-- ACTION --}}
                        <div class="profile-card-footer">
                            <button type="submit" class="profile-primary-button">
                                <i class="bi bi-check2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            {{-- =================================================
             SECURITY
        ================================================== --}}
            <section class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-icon profile-card-icon-security">
                        <i class="bi bi-shield-lock"></i>
                    </div>

                    <div>
                        <h2>
                            Keamanan Akun
                        </h2>
                        <p>
                            Kelola password untuk menjaga keamanan akun.
                        </p>
                    </div>
                </div>

                <div class="profile-card-body">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- CURRENT PASSWORD --}}
                        <div class="profile-field">
                            <label for="current_password" class="profile-label">
                                Password Saat Ini
                            </label>

                            <div class="profile-password-wrapper">
                                <input type="password" id="current_password" name="current_password"
                                    class="profile-input @error('current_password') is-invalid @enderror"
                                    autocomplete="current-password" required>

                                <button type="button" class="profile-password-toggle"
                                    data-password-toggle="current_password" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            @error('current_password')
                                <div class="profile-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- NEW PASSWORD --}}
                        <div class="profile-field">
                            <label for="password" class="profile-label">
                                Password Baru
                            </label>

                            <div class="profile-password-wrapper">
                                <input type="password" id="password" name="password"
                                    class="profile-input @error('password') is-invalid @enderror"
                                    autocomplete="new-password" required>

                                <button type="button" class="profile-password-toggle" data-password-toggle="password"
                                    aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            @error('password')
                                <div class="profile-error">
                                    {{ $message }}
                                </div>
                            @enderror

                            <small class="profile-help">
                                Gunakan minimal 8 karakter.
                            </small>
                        </div>

                        {{-- CONFIRM PASSWORD --}}
                        <div class="profile-field">
                            <label for="password_confirmation" class="profile-label">
                                Konfirmasi Password Baru
                            </label>

                            <div class="profile-password-wrapper">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="profile-input" autocomplete="new-password" required>

                                <button type="button" class="profile-password-toggle"
                                    data-password-toggle="password_confirmation" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- SECURITY NOTE --}}
                        <div class="profile-security-note">
                            <i class="bi bi-info-circle"></i>
                            <span>
                                Setelah password berhasil diubah,
                                gunakan password baru saat login berikutnya.
                            </span>
                        </div>

                        {{-- ACTION --}}
                        <div class="profile-card-footer">
                            <button type="submit" class="profile-primary-button">
                                <i class="bi bi-key"></i>
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document
                .querySelectorAll('[data-password-toggle]')
                .forEach(function(button) {
                    button.addEventListener('click', function() {
                        const inputId = this.dataset.passwordToggle;
                        const input = document.getElementById(inputId);
                        const icon = this.querySelector('i');

                        if (!input) {
                            return;
                        }

                        if (input.type === 'password') {
                            input.type = 'text';

                            icon.classList.remove('bi-eye');
                            icon.classList.add('bi-eye-slash');

                            this.setAttribute(
                                'aria-label',
                                'Sembunyikan password'
                            );
                        } else {
                            input.type = 'password';

                            icon.classList.remove('bi-eye-slash');
                            icon.classList.add('bi-eye');

                            this.setAttribute(
                                'aria-label',
                                'Tampilkan password'
                            );
                        }
                    });
                });
        });
    </script>
@endpush
