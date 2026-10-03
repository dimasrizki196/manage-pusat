@extends('layouts.barista')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="page-container transaction-page">
        <div class="page-header">
            <div>
                <div class="breadcrumb">
                    <a href="{{ route('barista.dashboard') }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Riwayat Transaksi</span>
                </div>

                <h1>Riwayat Transaksi</h1>

                <p>
                    Lihat seluruh aktivitas barang masuk dan barang keluar.
                </p>
            </div>
        </div>

        <div class="history-card">
            <div class="history-card-header">
                <div>
                    <h2>Riwayat Transaksi</h2>
                    <p>
                        Daftar transaksi barang yang telah dicatat oleh pengguna.
                    </p>
                </div>

                <div class="history-header-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>

            <form action="{{ route('barista.transactions.history') }}" method="GET" class="history-filter">
                <div class="history-filter-group history-search-group">
                    <label for="search">Cari Barang</label>

                    <div class="history-search-wrapper">
                        <i class="bi bi-search"></i>

                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Cari nama barang...">
                    </div>
                </div>

                <div class="history-filter-group">
                    <label for="transaction_type">Jenis Transaksi</label>

                    <select name="transaction_type" id="transaction_type">
                        <option value="">Semua Jenis</option>
                        <option value="masuk" {{ request('transaction_type') === 'masuk' ? 'selected' : '' }}>
                            Barang Masuk
                        </option>
                        <option value="keluar" {{ request('transaction_type') === 'keluar' ? 'selected' : '' }}>
                            Barang Keluar
                        </option>
                    </select>
                </div>

                <div class="history-filter-group">
                    <label for="transaction_source">Sumber</label>

                    <select name="transaction_source" id="transaction_source">
                        <option value="">Semua Sumber</option>
                        <option value="pengajuan" {{ request('transaction_source') === 'pengajuan' ? 'selected' : '' }}>
                            Pengajuan
                        </option>
                        <option value="urgent" {{ request('transaction_source') === 'urgent' ? 'selected' : '' }}>
                            Urgent
                        </option>
                    </select>
                </div>

                <div class="history-filter-group">
                    <label for="date_from">Dari Tanggal</label>

                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="history-filter-group">
                    <label for="date_to">Sampai Tanggal</label>

                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="history-filter-actions">
                    <button type="submit" class="btn-history-filter">
                        <i class="bi bi-funnel"></i>
                        Filter
                    </button>

                    <a href="{{ route('barista.transactions.history') }}" class="btn-history-reset" title="Reset filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>

            <div class="history-summary">
                <div class="history-summary-left">
                    <div class="history-summary-icon">
                        <i class="bi bi-list-ul"></i>
                    </div>

                    <div>
                        <strong>Daftar Transaksi</strong>
                        <span>
                            Menampilkan
                            {{ $transactions->firstItem() ?? 0 }}
                            -
                            {{ $transactions->lastItem() ?? 0 }}
                            dari
                            {{ $transactions->total() }}
                            transaksi
                        </span>
                    </div>
                </div>
            </div>

            <div class="history-table-wrapper">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Barang</th>
                            <th>Jenis</th>
                            <th>Sumber</th>
                            <th>Jumlah</th>
                            <th>Stok</th>
                            <th>Petugas</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($transactions as $index => $transaction)
                            <tr>
                                <td class="history-number">
                                    {{ $transactions->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="history-item">
                                        <div class="history-item-icon">
                                            <i class="bi bi-box-seam"></i>
                                        </div>

                                        <div>
                                            <strong>
                                                {{ $transaction->item?->name ?? '-' }}
                                            </strong>

                                            <span>
                                                {{ $transaction->item?->category?->name ?? 'Tanpa kategori' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if ($transaction->transaction_type === 'masuk')
                                        <span class="transaction-badge incoming">
                                            <i class="bi bi-arrow-down"></i>
                                            Masuk
                                        </span>
                                    @else
                                        <span class="transaction-badge outgoing">
                                            <i class="bi bi-arrow-up"></i>
                                            Keluar
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($transaction->transaction_type === 'masuk')
                                        @if ($transaction->transaction_source === 'pengajuan')
                                            <span class="source-badge request">
                                                <i class="bi bi-file-earmark-text"></i>
                                                Pengajuan
                                            </span>

                                            @if ($transaction->restockRequest)
                                                <small class="source-reference">
                                                    id - {{ $transaction->restockRequest->id }}
                                                </small>
                                            @endif
                                        @elseif ($transaction->transaction_source === 'urgent')
                                            <span class="source-badge urgent">
                                                <i class="bi bi-lightning-charge"></i>
                                                Urgent
                                            </span>
                                        @else
                                            <span class="source-empty">-</span>
                                        @endif
                                    @else
                                        <span class="source-empty">-</span>
                                    @endif
                                </td>

                                <td>
                                    <div
                                        class="history-quantity
                                    {{ $transaction->transaction_type === 'masuk' ? 'quantity-incoming' : 'quantity-outgoing' }}">
                                        <strong>
                                            {{ $transaction->transaction_type === 'masuk' ? '+' : '-' }}
                                            {{ number_format((float) $transaction->quantity, 2, ',', '.') }}
                                        </strong>

                                        <span>
                                            {{ $transaction->item?->unit?->name ?? 'Unit' }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <div class="history-stock">
                                        <span>
                                            {{ number_format((float) $transaction->stock_before, 2, ',', '.') }}
                                        </span>

                                        <i class="bi bi-arrow-right"></i>

                                        <strong>
                                            {{ number_format((float) $transaction->stock_after, 2, ',', '.') }}
                                        </strong>
                                    </div>
                                </td>

                                <td>
                                    <div class="history-user">
                                        <div class="history-user-avatar">
                                            {{ strtoupper(substr($transaction->user?->name ?? '?', 0, 1)) }}
                                        </div>

                                        <span>
                                            {{ $transaction->user?->name ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <div class="history-date">
                                        @if ($transaction->activity?->created_at)
                                            <strong>
                                                {{ $transaction->activity->created_at->format('d/m/Y') }}
                                            </strong>

                                            <span>
                                                {{ $transaction->activity->created_at->format('H:i') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="history-empty">
                                        <div class="history-empty-icon">
                                            <i class="bi bi-clock-history"></i>
                                        </div>

                                        <strong>Belum ada riwayat transaksi</strong>

                                        <span>
                                            Belum terdapat transaksi yang sesuai
                                            dengan filter yang dipilih.
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="history-pagination">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
