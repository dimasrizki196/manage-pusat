@extends('layouts.guest')

@section('title', 'Lupa Password - Kopi Pusat')

@section('content')

<div class="auth-page">

    <div class="auth-container forgot-container">

        {{-- LEFT --}}
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
                        ACCOUNT RECOVERY
                    </span>

                    <h1>
                        Butuh bantuan
                        <span>akses akun?</span>
                    </h1>

                    <p>
                        Masukkan username akun Anda.
                        Kami akan mengarahkan Anda ke
                        WhatsApp Admin Kopi Pusat untuk
                        mengajukan reset password.
                    </p>

                </div>


                <div class="showcase-footer">

                    <span>
                        <i class="bi bi-whatsapp"></i>
                        Bantuan melalui WhatsApp
                    </span>

                </div>

            </div>

        </section>


        {{-- RIGHT --}}
        <section class="auth-form-section">

            <div class="auth-form-wrapper">

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


                <div class="auth-header">

                    <a
                        href="{{ route('login') }}"
                        class="back-login"
                    >
                        <i class="bi bi-arrow-left"></i>

                        Kembali ke login
                    </a>

                    <span class="auth-eyebrow">
                        LUPA PASSWORD
                    </span>

                    <h2>
                        Hubungi Admin
                    </h2>

                    <p>
                        Masukkan username Anda untuk
                        mengajukan permintaan reset password
                        melalui WhatsApp.
                    </p>

                </div>


                @if ($errors->any())

                    <div class="auth-alert auth-alert-danger">

                        <i class="bi bi-exclamation-circle-fill"></i>

                        <span>
                            {{ $errors->first() }}
                        </span>

                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('password.whatsapp') }}"
                    class="auth-form"
                >

                    @csrf


                    <div class="auth-field">

                        <label for="username">
                            Username
                        </label>

                        <div class="auth-input-wrapper">

                            <i class="bi bi-person auth-input-icon"></i>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="{{ old('username') }}"
                                placeholder="Masukkan username"
                                autocomplete="username"
                                autofocus
                            >

                        </div>

                        @error('username')

                            <small class="auth-error">

                                <i class="bi bi-exclamation-circle"></i>

                                {{ $message }}

                            </small>

                        @enderror

                    </div>


                    <div class="whatsapp-info">

                        <div class="whatsapp-info-icon">

                            <i class="bi bi-whatsapp"></i>

                        </div>

                        <div>

                            <strong>
                                Permintaan melalui WhatsApp
                            </strong>

                            <p>
                                Setelah menekan tombol,
                                WhatsApp akan terbuka dengan
                                pesan permintaan reset yang
                                sudah disiapkan.
                            </p>

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="auth-submit"
                    >

                        <span>
                            Hubungi Admin
                        </span>

                        <i class="bi bi-whatsapp"></i>

                    </button>

                </form>


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

@endsection