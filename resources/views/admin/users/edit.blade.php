@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

    <div class="container-fluid py-4">

        <div class="mb-4">

            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Users
            </a>

            <h1 class="h4 fw-bold mt-3 mb-1">
                Edit User
            </h1>

            <p class="text-muted mb-0">
                Perbarui informasi user.
            </p>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <form method="POST" action="{{ route('admin.users.update', $user) }}">

                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        <div class="col-md-6">

                            <label class="form-label">
                                Nama
                            </label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                                required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Username
                            </label>

                            <input type="text" name="username" class="form-control"
                                value="{{ old('username', $user->username) }}" required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Nomor HP
                            </label>

                            <input type="text" name="phone_number" class="form-control"
                                value="{{ old('phone_number', $user->phone_number) }}">

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Role
                            </label>

                            <select name="role" class="form-select" required>

                                <option value="admin" @selected(old('role', $user->role) === 'admin')>
                                    Admin
                                </option>

                                <option value="barista" @selected(old('role', $user->role) === 'barista')>
                                    Barista
                                </option>

                            </select>

                        </div>


                        <div class="col-md-6">

                            <label for="password" class="form-label">
                                Password Baru
                            </label>

                            <div class="password-input-wrapper">

                                <input type="password" name="password" id="password" class="form-control password-input"
                                    autocomplete="new-password">

                                <button type="button" class="password-toggle" data-target="password"
                                    aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>

                            <div class="form-text">
                                Kosongkan jika tidak ingin mengubah password.
                            </div>

                        </div>


                        <div class="mb-3">

                            <label for="password_confirmation" class="form-label">
                                Konfirmasi Password Baru
                            </label>

                            <div class="password-input-wrapper">

                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control password-input" autocomplete="new-password">

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
                            <i class="bi bi-save me-1"></i>
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document
            .querySelectorAll('.password-toggle')
            .forEach(function (button) {

                button.addEventListener('click', function () {

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