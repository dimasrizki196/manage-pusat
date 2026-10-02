@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
    <div class="item-show-page">
        <div class="page-header">
            <div class="page-header-content">
                <div class="item-breadcrumb">
                    <a href="{{ route('admin.items.index') }}">
                        <i class="bi bi-box-seam"></i>
                        Barang
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Detail Barang</span>
                </div>
                <h1 class="page-title">Detail Barang</h1>
                <p class="page-subtitle">Informasi lengkap mengenai barang dan riwayat aktivitasnya.</p>
            </div>
            <div class="page-header-actions">
                <a href="{{ route('admin.items.index') }}" class="btn btn-light item-back-button">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="item-show-grid">
            {{-- INFORMASI BARANG --}}
            <div class="item-detail-card">
                <div class="item-detail-card-header">
                    <div>
                        <span class="item-detail-label">INFORMASI BARANG</span>
                        <h5 class="item-detail-title">{{ $item->name }}</h5>
                    </div>
                    @if ($item->isInactive())
                        <span class="item-status-badge inactive">Nonaktif</span>
                    @else
                        <span class="item-status-badge active">Aktif</span>
                    @endif
                </div>
                <div class="item-detail-body">
                    <div class="item-info-row">
                        <div class="item-info-icon"><i class="bi bi-box-seam"></i></div>
                        <div class="item-info-content">
                            <span>Nama Barang</span>
                            <strong>{{ $item->name }}</strong>
                        </div>
                    </div>
                    <div class="item-info-row">
                        <div class="item-info-icon"><i class="bi bi-tags"></i></div>
                        <div class="item-info-content">
                            <span>Kategori</span>
                            <strong>{{ $item->category?->name ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="item-info-row">
                        <div class="item-info-icon"><i class="bi bi-rulers"></i></div>
                        <div class="item-info-content">
                            <span>Satuan</span>
                            <strong>{{ $item->unit?->name ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="item-info-row">
                        <div class="item-info-icon"><i class="bi bi-stack"></i></div>
                        <div class="item-info-content">
                            <span>Stok Saat Ini</span>
                            <strong>{{ rtrim(rtrim(number_format($item->stock, 2, ',', '.'), '0'), ',') }}
                                {{ $item->unit?->name ?? '' }}</strong>
                        </div>
                    </div>
                    <div class="item-info-row">
                        <div class="item-info-icon"><i class="bi bi-exclamation-triangle"></i></div>
                        <div class="item-info-content">
                            <span>Minimum Stok</span>
                            <strong>{{ rtrim(rtrim(number_format($item->stock_min, 2, ',', '.'), '0'), ',') }}
                                {{ $item->unit?->name ?? '' }}</strong>
                        </div>
                    </div>
                    @if (!$item->isInactive() && $item->isLowStock())
                        <div class="item-stock-warning">
                            <i class="bi bi-exclamation-circle"></i>
                            <span>Stok barang berada di bawah atau sama dengan batas minimum.</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ACTIVITY --}}
            <div class="item-detail-card">
                <div class="item-detail-card-header">
                    <div>
                        <span class="item-detail-label">AKTIVITAS</span>
                        <h5 class="item-detail-title">Riwayat Aktivitas</h5>
                    </div>
                    <div class="item-header-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <div class="item-activity-list">
                    @if ($item->activity?->created_at)
                        <div class="item-activity-row">
                            <div class="item-activity-icon created"><i class="bi bi-plus-lg"></i></div>
                            <div class="item-activity-content">
                                <strong>Barang dibuat</strong>
                                <span>{{ $item->activity->created_at->format('d-m-Y H:i') }}</span>
                                <small>Oleh: {{ $item->activity->creator?->name ?? '-' }}</small>
                            </div>
                        </div>
                    @endif

                    @if ($item->activity?->updated_at)
                        <div class="item-activity-row">
                            <div class="item-activity-icon updated"><i class="bi bi-pencil"></i></div>
                            <div class="item-activity-content">
                                <strong>Barang diperbarui</strong>
                                <span>{{ $item->activity->updated_at->format('d-m-Y H:i') }}</span>
                                <small>Oleh: {{ $item->activity->updater?->name ?? '-' }}</small>
                            </div>
                        </div>
                    @endif

                    @if ($item->activity?->deleted_at)
                        <div class="item-activity-row">
                            <div class="item-activity-icon deleted"><i class="bi bi-trash"></i></div>
                            <div class="item-activity-content">
                                <strong>Barang dinonaktifkan</strong>
                                <span>{{ $item->activity->deleted_at->format('d-m-Y H:i') }}</span>
                                <small>Oleh: {{ $item->activity->deleter?->name ?? '-' }}</small>
                            </div>
                        </div>
                    @endif

                    @if (!$item->activity?->created_at && !$item->activity?->updated_at && !$item->activity?->deleted_at)
                        <div class="item-empty-activity">
                            <i class="bi bi-clock-history"></i>
                            <span>Belum ada aktivitas.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
