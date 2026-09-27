@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')

    <div class="container-fluid py-4">

        <div class="mb-4">

            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Users
            </a>

            <h1 class="h4 fw-bold mt-3 mb-1">
                Tambah User
            </h1>

            <p class="text-muted mb-0">
                Buat akun admin atau barista baru.
            </p>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <form method="POST" action="{{ route('admin.users.store') }}">

                    @csrf

                    <div class="row g-4">

                        <div class="col-md-6">

                            <label class="form-label">
                                Nama
                            </label>

                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Username
                            </label>

                            <input type="text" name="username"
                                class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}"
                                required>

                            @error('username')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Nomor HP
                            </label>

                            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}"
                                placeholder="08xxxxxxxxxx">

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Role
                            </label>

                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>

                                <option value="">
                                    Pilih Role
                                </option>

                                <option value="admin" @selected(old('role') === 'admin')>
                                    Admin
                                </option>

                                <option value="barista" @selected(old('role') === 'barista')>
                                    Barista
                                </option>

                            </select>

                            @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Password
                            </label>

                            <div class="password-input-wrapper">

                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror password-input"
                                    autocomplete="new-password" required>

                                <button type="button" class="password-toggle" data-target="password"
                                    aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label for="password_confirmation" class="form-label">
                                Konfirmasi Password
                            </label>

                            <div class="password-input-wrapper">

                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control password-input" autocomplete="new-password" required>

                                <button type="button" class="password-toggle" data-target="password_confirmation"
                                    aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>

                        </div>

                    </div>


                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-coffee">
                            <i class="bi bi-check-lg me-1"></i>
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

            document
                .querySelectorAll('.password-toggle')
                .forEach(function(button) {

                    button.addEventListener('click', function() {

                        const targetId =
                            this.dataset.target;

                        const input =
                            document.getElementById(targetId);

                        const icon =
                            this.querySelector('i');

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
