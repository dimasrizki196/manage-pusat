@extends('layouts.app')

@section('title', 'Ajuan Restock')

@push('styles')
    @vite('resources/css/admin/restock-requests.css')
@endpush

@section('content')
<div class="restock-page">

    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Ajuan Restock</h1>
            <p class="page-subtitle">
                Kelola pengajuan restock barang dari barista.
            </p>
        </div>
    </div>

    <div class="restock-card">

        {{-- FILTER --}}
        <div class="restock-filter">

            <form method="GET"
                action="{{ route('admin.restock-requests.index') }}"
                class="restock-filter-form">

                <div class="restock-search">
                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari barang atau barista..."
                    >
                </div>

                <select name="status" class="restock-filter-select">
                    <option value="">Semua Status</option>

                    <option value="pending"
                        {{ request('status') === 'pending' ? 'selected' : '' }}>
                        Menunggu
                    </option>

                    <option value="approved"
                        {{ request('status') === 'approved' ? 'selected' : '' }}>
                        Disetujui
                    </option>

                    <option value="rejected"
                        {{ request('status') === 'rejected' ? 'selected' : '' }}>
                        Ditolak
                    </option>
                </select>

                <button type="submit" class="restock-filter-button">
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.restock-requests.index') }}"
                        class="restock-reset-button">
                        Reset
                    </a>
                @endif

            </form>

        </div>

        {{-- TABLE --}}
        <div class="restock-table-wrapper">

            <table class="restock-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Barang</th>
                        <th>Barista</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($restockRequests as $request)

                    <tr>

                        <td>
                            {{ $restockRequests->firstItem() + $loop->index }}
                        </td>

                        <td>
                            <div class="restock-item">
                                <strong>
                                    {{ $request->item->name }}
                                </strong>

                                <span>
                                    {{ $request->item->category->name ?? '-' }}
                                    ·
                                    {{ $request->item->unit->name ?? '-' }}
                                </span>
                            </div>
                        </td>

                        <td>
                            <div class="restock-user">
                                <div class="restock-avatar">
                                    {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                </div>

                                <span>
                                    {{ $request->user->name }}
                                </span>
                            </div>
                        </td>

                        <td>
                            <strong>
                                {{ rtrim(rtrim(number_format($request->quantity, 2, ',', '.'), '0'), ',') }}
                            </strong>

                            <span class="restock-unit">
                                {{ $request->item->unit->name ?? '' }}
                            </span>
                        </td>

                        <td>
                            {{ $request->created_at->format('d-m-Y') }}

                            <small class="restock-time">
                                {{ $request->created_at->format('H:i') }}
                            </small>
                        </td>

                        <td>

                            @if($request->status === 'pending')

                                <span class="restock-status restock-status-pending">
                                    <i class="bi bi-clock"></i>
                                    Menunggu
                                </span>

                            @elseif($request->status === 'approved')

                                <span class="restock-status restock-status-approved">
                                    <i class="bi bi-check-circle"></i>
                                    Disetujui
                                </span>

                            @else

                                <span class="restock-status restock-status-rejected">
                                    <i class="bi bi-x-circle"></i>
                                    Ditolak
                                </span>

                            @endif

                        </td>

                        <td>

                            <a href="{{ route('admin.restock-requests.show', $request->id) }}"
                                class="restock-action-button"
                                title="Lihat detail">

                                <i class="bi bi-eye"></i>

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7">

                            <div class="restock-empty">

                                <div class="restock-empty-icon">
                                    <i class="bi bi-clipboard-check"></i>
                                </div>

                                <strong>
                                    Belum ada ajuan restock
                                </strong>

                                <span>
                                    Ajuan dari barista akan muncul di halaman ini.
                                </span>

                            </div>

                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        @if($restockRequests->hasPages())

            <div class="restock-pagination">
                {{ $restockRequests->links() }}
            </div>

        @endif

    </div>

</div>
@endsection