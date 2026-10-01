@extends('layouts.app')

@section('title', 'Detail Activity Log')

@section('content')
<div class="activity-detail-page">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="page-header-content">
            <div class="activity-detail-breadcrumb">
                <a href="{{ route('admin.activity-logs.index') }}">
                    <i class="bi bi-clock-history"></i>
                    Activity Log
                </a>

                <i class="bi bi-chevron-right"></i>

                <span>Detail</span>
            </div>

            <h1 class="page-title">
                Detail Activity Log
            </h1>

            <p class="page-subtitle">
                Informasi aktivitas dan riwayat perubahan data.
            </p>
        </div>

        <div class="page-header-actions">
            <a href="{{ route('admin.activity-logs.index') }}"
                class="btn btn-light activity-back-button">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="activity-detail-grid">

        {{-- DATA INFORMATION --}}
        <div class="activity-detail-card">

            <div class="activity-detail-card-header">
                <div class="activity-detail-card-icon">
                    <i class="bi bi-database"></i>
                </div>

                <div>
                    <h5>Informasi Data</h5>
                    <p>Informasi record yang terkait.</p>
                </div>
            </div>

            <div class="activity-detail-list">

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Tabel
                    </span>

                    <span class="activity-detail-value">
                        <span class="activity-table-badge">
                            {{ $activityLog->table_name }}
                        </span>
                    </span>
                </div>

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Record ID
                    </span>

                    <span class="activity-detail-value">
                        #{{ $activityLog->record_id }}
                    </span>
                </div>

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Activity ID
                    </span>

                    <span class="activity-detail-value">
                        #{{ $activityLog->id }}
                    </span>
                </div>

            </div>
        </div>

        {{-- CREATED --}}
        <div class="activity-detail-card">

            <div class="activity-detail-card-header">
                <div class="activity-detail-card-icon activity-icon-created">
                    <i class="bi bi-plus-lg"></i>
                </div>

                <div>
                    <h5>Data Dibuat</h5>
                    <p>Informasi saat record pertama dibuat.</p>
                </div>
            </div>

            <div class="activity-detail-list">

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Oleh
                    </span>

                    <span class="activity-detail-value">
                        @if ($activityLog->creator)
                            <div class="activity-detail-user">
                                <div class="activity-detail-avatar">
                                    {{ strtoupper(substr($activityLog->creator->name, 0, 1)) }}
                                </div>

                                <span>
                                    {{ $activityLog->creator->name }}
                                </span>
                            </div>
                        @else
                            <span class="activity-empty">
                                —
                            </span>
                        @endif
                    </span>
                </div>

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Waktu
                    </span>

                    <span class="activity-detail-value">
                        @if ($activityLog->created_at)
                            {{ $activityLog->created_at->format('d-m-Y H:i:s') }}
                        @else
                            —
                        @endif
                    </span>
                </div>

            </div>
        </div>

        {{-- UPDATED --}}
        <div class="activity-detail-card">

            <div class="activity-detail-card-header">
                <div class="activity-detail-card-icon activity-icon-updated">
                    <i class="bi bi-pencil"></i>
                </div>

                <div>
                    <h5>Terakhir Diubah</h5>
                    <p>Informasi perubahan terakhir pada record.</p>
                </div>
            </div>

            <div class="activity-detail-list">

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Oleh
                    </span>

                    <span class="activity-detail-value">
                        @if ($activityLog->updater)
                            <div class="activity-detail-user">
                                <div class="activity-detail-avatar">
                                    {{ strtoupper(substr($activityLog->updater->name, 0, 1)) }}
                                </div>

                                <span>
                                    {{ $activityLog->updater->name }}
                                </span>
                            </div>
                        @else
                            <span class="activity-empty">
                                Belum pernah diubah
                            </span>
                        @endif
                    </span>
                </div>

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Waktu
                    </span>

                    <span class="activity-detail-value">
                        @if ($activityLog->updated_at)
                            {{ $activityLog->updated_at->format('d-m-Y H:i:s') }}
                        @else
                            —
                        @endif
                    </span>
                </div>

            </div>
        </div>

        {{-- DELETED --}}
        <div class="activity-detail-card">

            <div class="activity-detail-card-header">
                <div class="activity-detail-card-icon activity-icon-deleted">
                    <i class="bi bi-trash"></i>
                </div>

                <div>
                    <h5>Dinonaktifkan</h5>
                    <p>Informasi saat record dinonaktifkan.</p>
                </div>
            </div>

            <div class="activity-detail-list">

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Status
                    </span>

                    <span class="activity-detail-value">
                        @if ($activityLog->deleted_at)
                            <span class="activity-status activity-status-deleted">
                                <i class="bi bi-x-circle"></i>
                                Nonaktif
                            </span>
                        @else
                            <span class="activity-status activity-status-active">
                                <i class="bi bi-check-circle"></i>
                                Aktif
                            </span>
                        @endif
                    </span>
                </div>

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Oleh
                    </span>

                    <span class="activity-detail-value">
                        @if ($activityLog->deleter)
                            <div class="activity-detail-user">
                                <div class="activity-detail-avatar">
                                    {{ strtoupper(substr($activityLog->deleter->name, 0, 1)) }}
                                </div>

                                <span>
                                    {{ $activityLog->deleter->name }}
                                </span>
                            </div>
                        @else
                            <span class="activity-empty">
                                —
                            </span>
                        @endif
                    </span>
                </div>

                <div class="activity-detail-row">
                    <span class="activity-detail-label">
                        Waktu
                    </span>

                    <span class="activity-detail-value">
                        @if ($activityLog->deleted_at)
                            {{ $activityLog->deleted_at->format('d-m-Y H:i:s') }}
                        @else
                            <span class="activity-empty">
                                Belum dinonaktifkan
                            </span>
                        @endif
                    </span>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection