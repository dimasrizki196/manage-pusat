@extends('layouts.guest')

@section('title', 'Login - Kopi Pusat')

@section('content')

    <div class="auth-page">

        <div class="auth-container">

            {{-- =====================================================
            LEFT PANEL
        ====================================================== --}}
            <section class="auth-showcase">

                <div class="showcase-content">

                    <div class="showcase-brand">

                        <div class="showcase-logo">
                            <i class="bi bi-cup-hot-fill"></i>
                        </div>

                        <span>
                            KOPI PUSAT
                        </span>

                    </div>


                    <div class="showcase-main">

                        <span class="showcase-label">
                            INVENTORY MANAGEMENT
                        </span>

                        <h1>
                            Kelola stok kopi
                            <span>dengan lebih mudah.</span>
                        </h1>

                        <p>
                            Sistem manajemen stok untuk membantu
                            pencatatan barang, transaksi, dan
                            pengajuan restock di Kopi Pusat.
                        </p>

                    </div>


                    <div class="showcase-footer">

                        <span>
                            <i class="bi bi-shield-check"></i>
                            Sistem internal Kopi Pusat
                        </span>

                    </div>

                </div>

            </section>


            {{-- =====================================================
            LOGIN PANEL
        ====================================================== --}}
            <section class="auth-form-section">

                <div class="auth-form-wrapper">

                    {{-- Mobile brand --}}
                    <div class="mobile-brand">

                        <div class="mobile-brand-icon">
                            <i class="bi bi-cup-hot-fill"></i>
                        </div>

                        <div>
                            <strong>KOPI PUSAT</strong>

                            <span>
                                Sistem Manajemen Stok
                            </span>
                        </div>

                    </div>


                    {{-- Header --}}
                    <div class="auth-header">

                        <span class="auth-eyebrow">
                            SELAMAT DATANG
                        </span>

                        <h2>
                            Masuk ke akun Anda
                        </h2>

                        <p>
                            Gunakan akun yang telah terdaftar
                            untuk mengakses sistem.
                        </p>

                    </div>


                    {{-- Success message --}}
                    @if (session('success'))
                        <div class="auth-alert auth-alert-success">

                            <i class="bi bi-check-circle-fill"></i>

                            <span>
                                {{ session('success') }}
                            </span>

                        </div>
                    @endif


                    {{-- General error --}}
                    @if ($errors->any())
                        <div class="auth-alert auth-alert-danger">

                            <i class="bi bi-exclamation-circle-fill"></i>

                            <span>
                                {{ $errors->first() }}
                            </span>

                        </div>
                    @endif


                    {{-- =================================================
                    FORM
                ================================================== --}}
                    <form method="POST" action="{{ route('login.process') }}" class="auth-form" id="loginForm">

                        @csrf


                        {{-- Username --}}
                        <div class="auth-field">

                            <label for="username">
                                Username
                            </label>

                            <div class="auth-input-wrapper">

                                <i class="bi bi-person auth-input-icon"></i>

                                <input type="text" id="username" name="username" value="{{ old('username') }}"
                                    placeholder="Masukkan username" autocomplete="username" autofocus
                                    class="@error('username') has-error @enderror">

                            </div>

                            @error('username')
                                <small class="auth-error">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- Password --}}
                        <div class="auth-field">

                            <div class="auth-label-row">

                                <label for="password">
                                    Password
                                </label>

                            </div>


                            <div class="auth-input-wrapper">

                                <i class="bi bi-lock auth-input-icon"></i>

                                <input type="password" id="password" name="password" placeholder="Masukkan password"
                                    autocomplete="current-password" class="@error('password') has-error @enderror">

                                <button type="button" class="password-toggle" id="togglePassword"
                                    aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>

                            @error('password')
                                <small class="auth-error">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- Remember + forgot --}}
                        <div class="auth-options">

                            <label class="remember-option">

                                <input type="checkbox" name="remember" value="1"
                                    {{ old('remember') ? 'checked' : '' }}>

                                <span class="custom-checkbox">
                                    <i class="bi bi-check"></i>
                                </span>

                                <span>
                                    Ingat saya
                                </span>

                            </label>


                            <a href="{{ route('password.request') }}" class="forgot-link">
                                Lupa password?
                            </a>

                        </div>


                        {{-- Submit --}}
                        <button type="submit" class="auth-submit" id="loginButton">

                            <span class="button-content">

                                <span>
                                    Masuk
                                </span>

                                <i class="bi bi-arrow-right"></i>

                            </span>

                            <span class="button-loading d-none">

                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>

                                <span>
                                    Memproses...
                                </span>

                            </span>

                        </button>

                    </form>


                    {{-- Footer --}}
                    <div class="auth-footer">

                        <span>
                            © {{ date('Y') }} Kopi Pusat
                        </span>

                        <span class="footer-dot">
                            •
                        </span>

                        <span>
                            Sistem Manajemen Stok
                        </span>

                    </div>

                </div>

            </section>

        </div>

    </div>


    {{-- =============================================================
    JAVASCRIPT
============================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | Toggle Password
            |--------------------------------------------------------------------------
            */

            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');

            if (passwordInput && togglePassword) {

                togglePassword.addEventListener('click', function() {

                    const isPassword =
                        passwordInput.getAttribute('type') === 'password';

                    passwordInput.setAttribute(
                        'type',
                        isPassword ? 'text' : 'password'
                    );

                    const icon = this.querySelector('i');

                    icon.classList.toggle(
                        'bi-eye',
                        !isPassword
                    );

                    icon.classList.toggle(
                        'bi-eye-slash',
                        isPassword
                    );

                    this.setAttribute(
                        'aria-label',
                        isPassword ?
                        'Sembunyikan password' :
                        'Tampilkan password'
                    );

                });

            }


            /*
            |--------------------------------------------------------------------------
            | Loading State
            |--------------------------------------------------------------------------
            */

            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');

            if (loginForm && loginButton) {

                loginForm.addEventListener('submit', function() {

                    const buttonContent =
                        loginButton.querySelector('.button-content');

                    const buttonLoading =
                        loginButton.querySelector('.button-loading');

                    if (buttonContent) {
                        buttonContent.classList.add('d-none');
                    }

                    if (buttonLoading) {
                        buttonLoading.classList.remove('d-none');
                    }

                    loginButton.disabled = true;

                });

            }

        });
    </script>

@endsection
