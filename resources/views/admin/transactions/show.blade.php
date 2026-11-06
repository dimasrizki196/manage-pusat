@extends('layouts.app')

@section('title', 'Detail Transaksi')

@push('styles')
  @vite('resources/css/admin/transactions.css')
@endpush

@section('content')
  @php
    $isRestockRequest =
        $transaction->transaction_type === 'masuk' &&
        $transaction->transaction_source === 'pengajuan' &&
        $transaction->restockRequest;

    $unit = $transaction->item?->unit?->name ?? '';
    $formatNumber = fn($value) => rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',');
  @endphp

  <div class="transaction-page">
    <div class="transaction-page-header">
      <div class="transaction-page-header-content">
        <div class="transaction-breadcrumb">
          <a href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>

          <i class="bi bi-chevron-right"></i>

          <a href="{{ route('admin.transactions.index') }}">
            Riwayat Transaksi
          </a>

          <i class="bi bi-chevron-right"></i>
          <span>Detail</span>
        </div>

        <h1 class="transaction-page-title">Detail Transaksi</h1>

        <p class="transaction-page-subtitle">
          Informasi lengkap perubahan stok barang.
        </p>
      </div>

      <div class="transaction-page-header-actions">
        <a href="{{ route('admin.transactions.index') }}" class="transaction-secondary-button">
          <i class="bi bi-arrow-left"></i>
          Kembali
        </a>
      </div>
    </div>

    <div class="transaction-detail-grid">
      <div class="transaction-detail-card">
        <div class="transaction-detail-card-header">
          <div class="transaction-detail-heading">
            <div class="transaction-detail-icon">
              <i class="bi bi-arrow-left-right"></i>
            </div>

            <div>
              <h2>Informasi Transaksi</h2>
              <p>Data pencatatan transaksi stok.</p>
            </div>
          </div>

          @if ($transaction->transaction_type === 'masuk')
            <span class="transaction-type transaction-type-in">
              <i class="bi bi-arrow-down-left"></i>
              Barang Masuk
            </span>
          @else
            <span class="transaction-type transaction-type-out">
              <i class="bi bi-arrow-up-right"></i>
              Barang Keluar
            </span>
          @endif
        </div>

        <div class="transaction-detail-body">
          <div class="transaction-detail-item">
            <span>Barang</span>
            <strong>{{ $transaction->item?->name ?? '-' }}</strong>
          </div>

          <div class="transaction-detail-item">
            <span>Kategori</span>
            <strong>{{ $transaction->item?->category?->name ?? '-' }}</strong>
          </div>

          <div class="transaction-detail-item">
            <span>Satuan</span>
            <strong>{{ $unit ?: '-' }}</strong>
          </div>

          <div class="transaction-detail-item">
            <span>Jenis Transaksi</span>
            <strong>{{ ucfirst($transaction->transaction_type) }}</strong>
          </div>

          @if ($isRestockRequest)
            <div class="transaction-detail-item">
              <span>Sumber Transaksi</span>
              <strong>
                Pengajuan #{{ $transaction->restockRequest->id }}
              </strong>
            </div>

            <div class="transaction-detail-item">
              <span>Jumlah Diajukan</span>
              <strong class="transaction-detail-highlight">
                {{ $formatNumber($transaction->restockRequest->quantity) }}
                {{ $unit }}
              </strong>
            </div>

            <div class="transaction-detail-item">
              <span>Jumlah Masuk</span>
              <strong class="transaction-detail-highlight">
                {{ $formatNumber($transaction->quantity) }}
                {{ $unit }}
              </strong>
            </div>
          @else
            <div class="transaction-detail-item">
              <span>Jumlah</span>
              <strong class="transaction-detail-highlight">
                {{ $formatNumber($transaction->quantity) }}
                {{ $unit }}
              </strong>
            </div>
          @endif

          <div class="transaction-detail-item">
            <span>Dicatat Oleh</span>
            <strong>{{ $transaction->user?->name ?? '-' }}</strong>
          </div>

          <div class="transaction-detail-item">
            <span>Tanggal</span>
            <strong>
              {{ $transaction->activity?->created_at?->format('d-m-Y') ?? '-' }}
            </strong>
          </div>

          <div class="transaction-detail-item">
            <span>Waktu</span>
            <strong>
              {{ $transaction->activity?->created_at?->format('H:i:s') ?? '-' }}
            </strong>
          </div>
        </div>
      </div>

      <div class="transaction-detail-card">
        <div class="transaction-detail-card-header">
          <div class="transaction-detail-heading">
            <div class="transaction-detail-icon">
              <i class="bi bi-box-seam"></i>
            </div>

            <div>
              <h2>Perubahan Stok</h2>
              <p>Perbandingan stok sebelum dan sesudah transaksi.</p>
            </div>
          </div>
        </div>

        <div class="transaction-stock-flow">
          <div class="transaction-stock-box">
            <span>Stok Sebelum</span>

            <strong>{{ $formatNumber($transaction->stock_before) }}</strong>

            <small>{{ $unit }}</small>
          </div>

          <div class="transaction-stock-arrow">
            <i class="bi bi-arrow-right"></i>
          </div>

          <div class="transaction-stock-box transaction-stock-box-result">
            <span>Stok Sesudah</span>

            <strong>{{ $formatNumber($transaction->stock_after) }}</strong>

            <small>{{ $unit }}</small>
          </div>
        </div>

        <div class="transaction-stock-summary">
          @if ($transaction->transaction_type === 'masuk')
            <i class="bi bi-plus-circle"></i>

            <span>
              Stok bertambah sebesar
              <strong>
                {{ $formatNumber($transaction->quantity) }}
                {{ $unit }}
              </strong>
            </span>
          @else
            <i class="bi bi-dash-circle"></i>

            <span>
              Stok berkurang sebesar
              <strong>
                {{ $formatNumber($transaction->quantity) }}
                {{ $unit }}
              </strong>
            </span>
          @endif
        </div>
      </div>

      <div class="transaction-detail-card transaction-detail-card-full">
        <div class="transaction-detail-card-header">
          <div class="transaction-detail-heading">
            <div class="transaction-detail-icon">
              <i class="bi bi-clock-history"></i>
            </div>

            <div>
              <h2>Informasi Aktivitas</h2>
              <p>Informasi pencatatan aktivitas transaksi.</p>
            </div>
          </div>
        </div>

        <div class="transaction-activity-grid">
          <div class="transaction-detail-item">
            <span>ID Transaksi</span>
            <strong>#{{ $transaction->id }}</strong>
          </div>

          <div class="transaction-detail-item">
            <span>ID Activity Log</span>
            <strong>#{{ $transaction->activity_id ?? '-' }}</strong>
          </div>

          <div class="transaction-detail-item">
            <span>Aktivitas Dibuat Oleh</span>
            <strong>
              {{ $transaction->activity?->createdBy?->name ?? ($transaction->user?->name ?? '-') }}
            </strong>
          </div>

          <div class="transaction-detail-item">
            <span>Aktivitas Dibuat Pada</span>
            <strong>
              {{ $transaction->activity?->created_at?->format('d-m-Y H:i:s') ?? '-' }}
            </strong>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
