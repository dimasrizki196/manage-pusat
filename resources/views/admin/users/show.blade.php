@extends('layouts.app')

@section('title', 'Detail User')

@section('content')

    <div class="container-fluid py-4">

        <div class="mb-4">

            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Users
            </a>

            <h1 class="h4 fw-bold mt-3 mb-1">
                Detail User
            </h1>

        </div>


        <div class="row g-4">

            {{-- PROFILE --}}
            <div class="col-lg-7">

                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <div class="d-flex align-items-center gap-3 mb-4">

                            <div class="user-avatar user-avatar-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>

                            <div>

                                <h2 class="h5 mb-1 fw-bold">
                                    {{ $user->name }}
                                </h2>

                                <span class="text-muted">
                                    {{ '@' . $user->username }}
                                </span>

                            </div>

                        </div>


                        <div class="row g-4">

                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Nama
                                </small>

                                <strong>
                                    {{ $user->name }}
                                </strong>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Username
                                </small>

                                <strong>
                                    {{ $user->username }}
                                </strong>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Nomor HP
                                </small>

                                <strong>
                                    {{ $user->phone_number ?? '-' }}
                                </strong>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Role
                                </small>

                                <strong>
                                    {{ ucfirst($user->role) }}
                                </strong>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Status
                                </small>

                                @if ($user->trashed())
                                    <span class="text-danger fw-semibold">
                                        Nonaktif
                                    </span>
                                @else
                                    <span class="text-success fw-semibold">
                                        Aktif
                                    </span>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ACTIVITY --}}
            <div class="col-lg-5">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white border-0 p-4">

                        <h3 class="h6 fw-bold mb-0">
                            Activity
                        </h3>

                    </div>


                    <div class="card-body px-4 pt-0">

                        <div class="activity-item">

                            <span>
                                Dibuat
                            </span>

                            <strong>
                                {{ $user->activityLog?->created_at?->format('d M Y H:i') ?? '-' }}
                            </strong>

                        </div>


                        <div class="activity-item">

                            <span>
                                Dibuat oleh
                            </span>

                            <strong>
                                {{ $user->activityLog?->creator?->name ?? 'System' }}
                            </strong>

                        </div>


                        <div class="activity-item">

                            <span>
                                Terakhir diperbarui
                            </span>

                            <strong>
                                {{ $user->activityLog?->updated_at?->format('d M Y H:i') ?? '-' }}
                            </strong>

                        </div>


                        <div class="activity-item">

                            <span>
                                Diperbarui oleh
                            </span>

                            <strong>
                                {{ $user->activityLog?->updater?->name ?? '-' }}
                            </strong>

                        </div>


                        <div class="activity-item">

                            <span>
                                Dinonaktifkan
                            </span>

                            <strong>
                                {{ $user->activityLog?->deleted_at?->format('d M Y H:i') ?? '-' }}
                            </strong>

                        </div>


                        <div class="activity-item">

                            <span>
                                Dinonaktifkan oleh
                            </span>

                            <strong>
                                {{ $user->activityLog?->deleter?->name ?? '-' }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
