@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
    <div class="admin-page activity-log-page">

        {{-- HEADER --}}
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Activity Log</h1>
                <p class="page-subtitle">
                    Riwayat aktivitas perubahan data dalam sistem.
                </p>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="activity-log-filter-card">
            <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="activity-log-filter-form">

                <div class="activity-log-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas...">
                </div>

                <div class="activity-log-select">
                    <select name="table">
                        <option value="">Semua Data</option>

                        @foreach ($tables as $table)
                            <option value="{{ $table }}" {{ request('table') === $table ? 'selected' : '' }}>
                                {{ match ($table) {
                                    'users' => 'Pengguna',
                                    'units' => 'Satuan',
                                    'categories' => 'Kategori',
                                    'items' => 'Barang',
                                    'transactions' => 'Transaksi',
                                    default => ucwords(str_replace('_', ' ', $table)),
                                } }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="activity-log-filter-button">
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>

                @if (request()->filled('search') || request()->filled('table'))
                    <a href="{{ route('admin.activity-logs.index') }}" class="activity-log-reset-button">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Reset
                    </a>
                @endif

            </form>
        </div>

        {{-- TABLE --}}
        <div class="activity-log-card">

            <div class="activity-log-card-header">
                <div>
                    <h2>Riwayat Aktivitas</h2>
                    <span>
                        {{ $activityLogs->total() }} aktivitas
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table activity-log-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Data</th>
                            <th>Record ID</th>
                            <th>Dibuat Oleh</th>
                            <th>Dibuat Pada</th>
                            <th>Diubah Oleh</th>
                            <th>Diubah Pada</th>
                            <th>Dihapus Oleh</th>
                            <th>Dihapus Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($activityLogs as $log)
                            <tr>
                                <td>
                                    {{ $activityLogs->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <span class="activity-table-name">
                                        {{ $log->table_name }}
                                    </span>
                                </td>

                                <td>
                                    <span class="activity-record-id">
                                        #{{ $log->record_id }}
                                    </span>
                                </td>

                                <td>
                                    @if ($log->creator)
                                        <div class="activity-user">
                                            <span>
                                                {{ $log->creator->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="activity-empty">
                                            —
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($log->created_at)
                                        <div class="activity-date">
                                            {{ $log->created_at->format('d-m-Y') }}
                                        </div>

                                        <div class="activity-time">
                                            {{ $log->created_at->format('H:i') }}
                                        </div>
                                    @else
                                        <span class="activity-empty">
                                            —
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($log->updater)
                                        <div class="activity-user">
                                            <span>
                                                {{ $log->updater->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="activity-empty">
                                            —
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($log->updated_at)
                                        <div class="activity-date">
                                            {{ $log->updated_at->format('d-m-Y') }}
                                        </div>

                                        <div class="activity-time">
                                            {{ $log->updated_at->format('H:i') }}
                                        </div>
                                    @else
                                        <span class="activity-empty">
                                            —
                                        </span>
                                    @endif
                                </td>

                                {{-- DELETED BY --}}
                                <td>
                                    @if ($log->deleter)
                                        <div class="activity-user activity-user-deleted">
                                            <span>
                                                {{ $log->deleter->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="activity-empty">
                                            —
                                        </span>
                                    @endif
                                </td>

                                {{-- DELETED AT --}}
                                <td>
                                    @if ($log->deleted_at)
                                        <div class="activity-date">
                                            {{ $log->deleted_at->format('d-m-Y') }}
                                        </div>

                                        <div class="activity-time">
                                            {{ $log->deleted_at->format('H:i') }}
                                        </div>
                                    @else
                                        <span class="activity-empty">
                                            —
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.activity-logs.show', $log->id) }}"
                                        class="btn btn-sm btn-light">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="activity-empty-state">
                                        <i class="bi bi-clock-history"></i>
                                        <span>Belum ada activity log.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($activityLogs->hasPages())
                <div class="activity-log-pagination">
                    {{ $activityLogs->links() }}
                </div>
            @endif

        </div>

    </div>
@endsection
