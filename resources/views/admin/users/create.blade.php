@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')

    <div class="users-page">
        <div class="page-header">
            <div class="page-header-content">
                <div class="users-breadcrumb">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people"></i>
                        Users
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Tambah User</span>
                </div>
                <h1 class="page-title">Tambah User</h1>
                <p class="page-subtitle">Buat akun admin atau barista baru.</p>
            </div>
            <div class="page-header-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light users-back-button">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
 
        <div class="users-form-card">
            <div class="users-form-header">
                <div class="users-form-header-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h2>Informasi User</h2>
                    <p>Masukkan informasi akun yang akan dibuat.</p>
                </div>
            </div>

            <div class="users-form-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="users-form-grid">
                        <div class="users-form-group">
                            <label for="name">Nama</label>
                            <input type="text" id="name" name="name" class="@error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="users-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="users-form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username"
                                class="@error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="users-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="users-form-group">
                            <label for="phone_number">Nomor HP</label>
                            <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                                placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="users-form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" class="@error('role') is-invalid @enderror" required>
                                <option value="">Pilih Role</option>
                                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                <option value="barista" @selected(old('role') === 'barista')>Barista</option>
                            </select>
                            @error('role')
                                <div class="users-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="users-form-group">
                            <label for="password">Password</label>
                            <div class="users-password-wrapper">
                                <input type="password" id="password" name="password"
                                    class="@error('password') is-invalid @enderror" autocomplete="new-password" required>
                                <button type="button" class="users-password-toggle" data-target="password"
                                    aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="users-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="users-form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <div class="users-password-wrapper">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    autocomplete="new-password" required>
                                <button type="button" class="users-password-toggle" data-target="password_confirmation"
                                    aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="users-form-actions">
                        <a href="{{ route('admin.users.index') }}" class="users-cancel-button">
                            Batal
                        </a>
                        <button type="submit" class="users-submit-button">
                            <i class="bi bi-check-lg"></i>
                            Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.users-password-toggle').forEach(function(button) {
                button.addEventListener('click', function() {
                    const input = document.getElementById(this.dataset.target);
                    const icon = this.querySelector('i');

                    if (!input) {
                        return;
                    }

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                        this.setAttribute('aria-label', 'Sembunyikan password');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                        this.setAttribute('aria-label', 'Tampilkan password');
                    }
                });
            });
        });
    </script>
@endpush
