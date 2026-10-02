@extends('layouts.app')

@section('title', 'Detail User')

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
                    <span>Detail User</span>
                </div>
                <h1 class="page-title">Detail User</h1>
                <p class="page-subtitle">Informasi akun dan riwayat aktivitas pengguna.</p>
            </div>
            <div class="page-header-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light users-back-button">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="users-detail-grid">
            <div class="users-detail-card">
                <div class="users-detail-header">
                    <div class="users-detail-profile">
                        <div class="users-avatar users-avatar-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h2>{{ $user->name }}</h2>
                            <span>{{ '@' . $user->username }}</span>
                        </div>
                    </div>
                </div>

                <div class="users-detail-body">
                    <div class="users-detail-list">
                        <div class="users-detail-row">
                            <span class="users-detail-label">Nama</span>
                            <strong class="users-detail-value">{{ $user->name }}</strong>
                        </div>
                        <div class="users-detail-row">
                            <span class="users-detail-label">Username</span>
                            <strong class="users-detail-value">{{ $user->username }}</strong>
                        </div>
                        <div class="users-detail-row">
                            <span class="users-detail-label">Nomor HP</span>
                            <strong class="users-detail-value">{{ $user->phone_number ?? '—' }}</strong>
                        </div>
                        <div class="users-detail-row">
                            <span class="users-detail-label">Role</span>
                            <strong class="users-detail-value">
                                @if ($user->role === 'admin')
                                    <span class="users-role-badge users-role-admin">Admin</span>
                                @else
                                    <span class="users-role-badge users-role-barista">Barista</span>
                                @endif
                            </strong>
                        </div>
                        <div class="users-detail-row">
                            <span class="users-detail-label">Status</span>
                            <strong class="users-detail-value">
                                @if ($user->trashed())
                                    <span class="users-status-badge users-status-inactive">
                                        <i class="bi bi-x-circle"></i>
                                        Nonaktif
                                    </span>
                                @else
                                    <span class="users-status-badge users-status-active">
                                        <i class="bi bi-check-circle"></i>
                                        Aktif
                                    </span>
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="users-activity">
                <div class="users-activity-header">
                    <div>
                        <h2 class="users-activity-title">Activity</h2>
                        <p>Riwayat aktivitas akun pengguna.</p>
                    </div>
                </div>

                <div class="users-activity-body">
                    <div class="users-activity-item">
                        <span class="users-activity-label">Dibuat</span>
                        <strong class="users-activity-value">
                            {{ $user->activityLog?->created_at?->format('d-m-Y H:i') ?? '—' }}
                        </strong>
                    </div>
                    <div class="users-activity-item">
                        <span class="users-activity-label">Dibuat oleh</span>
                        <strong class="users-activity-value">
                            {{ $user->activityLog?->creator?->name ?? 'System' }}
                        </strong>
                    </div>
                    <div class="users-activity-item">
                        <span class="users-activity-label">Terakhir diperbarui</span>
                        <strong class="users-activity-value">
                            {{ $user->activityLog?->updated_at?->format('d-m-Y H:i') ?? '—' }}
                        </strong>
                    </div>
                    <div class="users-activity-item">
                        <span class="users-activity-label">Diperbarui oleh</span>
                        <strong class="users-activity-value">
                            {{ $user->activityLog?->updater?->name ?? '—' }}
                        </strong>
                    </div>
                    <div class="users-activity-item">
                        <span class="users-activity-label">Dinonaktifkan</span>
                        <strong class="users-activity-value">
                            {{ $user->activityLog?->deleted_at?->format('d-m-Y H:i') ?? '—' }}
                        </strong>
                    </div>
                    <div class="users-activity-item">
                        <span class="users-activity-label">Dinonaktifkan oleh</span>
                        <strong class="users-activity-value">
                            {{ $user->activityLog?->deleter?->name ?? '—' }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
