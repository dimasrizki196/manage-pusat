@extends('layouts.barista')

@section('title', 'Dashboard Barista')

@push('styles')
    @vite('resources/css/barista/dashboard.css')
@endpush

@section('content')
    <div class="barista-dashboard">
        <div class="page-header">
            <div class="page-header-content">
                <div class="breadcrumb">
                    <a href="{{ route('barista.dashboard') }}">Dashboard</a>
                </div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Pantau stok dan aktivitas transaksi hari ini.</p>
            </div>
        </div>

        <div class="dashboard-summary-grid">
            <div class="dashboard-summary-card">
                <div class="dashboard-summary-icon icon-items">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="dashboard-summary-content">
                    <span>Total Barang</span>
                    <strong>{{ $totalItems }}</strong>
                    <small>Barang aktif</small>
                </div>
            </div>

            <div class="dashboard-summary-card">
                <div class="dashboard-summary-icon icon-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="dashboard-summary-content">
                    <span>Stok Menipis</span>
                    <strong>{{ $lowStockCount }}</strong>
                    <small>Perlu diperhatikan</small>
                </div>
            </div>

            <div class="dashboard-summary-card">
                <div class="dashboard-summary-icon icon-restock">
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div class="dashboard-summary-content">
                    <span>Pengajuan Diproses</span>
                    <strong>{{ $pendingRestockCount }}</strong>
                    <small>Pengajuan Anda</small>
                </div>
            </div>

            <div class="dashboard-summary-card">
                <div class="dashboard-summary-icon icon-transaction">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div class="dashboard-summary-content">
                    <span>Transaksi Hari Ini</span>
                    <strong>{{ $todayIncoming + $todayOutgoing }}</strong>
                    <small>{{ $todayIncoming }} masuk · {{ $todayOutgoing }} keluar</small>
                </div>
            </div>
        </div>

        <div class="dashboard-main-grid">
            <div class="dashboard-card dashboard-stock-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2>Stok Menipis</h2>
                        <p>Barang yang perlu diperhatikan.</p>
                    </div>
                    <a href="{{ route('barista.transactions.outgoing') }}" class="dashboard-card-link">
                        Barang Keluar
                    </a>
                </div>

                @if ($lowStockItems->count())
                    <div class="dashboard-stock-list">
                        @foreach ($lowStockItems as $item)
                            <div class="dashboard-stock-item">
                                <div class="stock-item-info">
                                    <div class="stock-item-icon">
                                        <i class="bi bi-box"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $item->name }}</strong>
                                        <span>{{ $item->unit?->name ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="stock-item-value">
                                    <strong>{{ rtrim(rtrim(number_format($item->stock, 2, ',', '.'), '0'), ',') }}</strong>
                                    <span>
                                        Min. {{ rtrim(rtrim(number_format($item->stock_min, 2, ',', '.'), '0'), ',') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="dashboard-empty">
                        <div class="dashboard-empty-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <strong>Stok aman</strong>
                        <span>Tidak ada barang yang sedang menipis.</span>
                    </div>
                @endif
            </div>

            <div class="dashboard-card dashboard-transaction-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2>Transaksi Terbaru</h2>
                        <p>Aktivitas transaksi Anda.</p>
                    </div>
                    <a href="{{ route('barista.transactions.history') }}" class="dashboard-card-link">
                        Lihat Semua
                    </a>
                </div>

                @if ($recentTransactions->count())
                    <div class="dashboard-transaction-list">
                        @foreach ($recentTransactions as $transaction)
                            <div class="dashboard-transaction-item">
                                <div
                                    class="transaction-item-icon {{ $transaction->transaction_type === 'masuk' ? 'transaction-in' : 'transaction-out' }}">
                                    <i
                                        class="bi {{ $transaction->transaction_type === 'masuk' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }}"></i>
                                </div>

                                <div class="transaction-item-info">
                                    <strong>{{ $transaction->item?->name ?? '-' }}</strong>
                                    <span>
                                        {{ $transaction->transaction_type === 'masuk' ? 'Barang Masuk' : 'Barang Keluar' }}
                                        · {{ $transaction->activity?->created_at?->format('d M Y, H:i') ?? '-' }}
                                    </span>
                                </div>

                                <div
                                    class="transaction-item-quantity {{ $transaction->transaction_type === 'masuk' ? 'quantity-in' : 'quantity-out' }}">
                                    {{ $transaction->transaction_type === 'masuk' ? '+' : '-' }}{{ rtrim(rtrim(number_format($transaction->quantity, 2, ',', '.'), '0'), ',') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="dashboard-empty">
                        <div class="dashboard-empty-icon">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <strong>Belum ada transaksi</strong>
                        <span>Transaksi Anda akan tampil di sini.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="dashboard-action-card">
            <div>
                <h2>Transaksi Stok</h2>
                <p>Catat barang masuk atau barang keluar dengan cepat.</p>
            </div>

            <div class="dashboard-action-buttons">
                <a href="{{ route('barista.transactions.incoming') }}" class="dashboard-action-button action-in">
                    <i class="bi bi-box-arrow-in-down"></i>
                    Barang Masuk
                </a>

                <a href="{{ route('barista.transactions.outgoing') }}" class="dashboard-action-button action-out">
                    <i class="bi bi-box-arrow-up"></i>
                    Barang Keluar
                </a>

                <a href="{{ route('barista.transactions.history') }}" class="dashboard-action-button action-history">
                    <i class="bi bi-clock-history"></i>
                    Riwayat Transaksi
                </a>
            </div>
        </div>
    </div>
@endsection
