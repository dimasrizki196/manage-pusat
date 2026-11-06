@extends('layouts.app')

@section('title', 'Detail Barang')

@push('styles')
  @vite('resources/css/admin/items.css')
@endpush

@section('content')
  @php
    $ropData = $item->rop_data;

    $formatNumber = function ($value) {
        return rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',');
    };

    $isInactive = $item->isInactive();
    $isLowStock = !$isInactive && $item->isLowStock();
    $isBelowRop = !$isInactive && $ropData['needs_restock'];
  @endphp

  <div class="admin-items-page item-show-page">

    {{-- BREADCRUMB --}}
    <div class="items-breadcrumb">
      <i class="bi bi-grid"></i>
      <a href="{{ route('admin.dashboard') }}">Dashboard</a>
      <i class="bi bi-chevron-right"></i>
      <a href="{{ route('admin.items.index') }}">Barang</a>
      <i class="bi bi-chevron-right"></i>
      <span>Detail Barang</span>
    </div>

    {{-- PAGE HEADER --}}
    <div class="items-page-header">
      <div class="items-page-header-content">
        <h1 class="items-page-title">Detail Barang</h1>
        <p class="items-page-subtitle">
          Informasi lengkap mengenai barang dan riwayat aktivitasnya.
        </p>
      </div>

      <div class="items-page-header-actions">
        <a href="{{ route('admin.items.index') }}" class="items-secondary-button">
          <i class="bi bi-arrow-left"></i>
          <span>Kembali</span>
        </a>
      </div>
    </div>

    {{-- CONTENT --}}
    <div class="item-show-grid">

      {{-- INFORMASI BARANG --}}
      <div class="item-detail-card">

        <div class="item-detail-card-header">
          <div>
            <span class="item-detail-label">INFORMASI BARANG</span>
            <h5 class="item-detail-title">
              {{ $item->name }}
            </h5>
          </div>

          @if ($isInactive)
            <span class="item-status-badge inactive">
              Nonaktif
            </span>
          @else
            <span class="item-status-badge active">
              Aktif
            </span>
          @endif
        </div>

        <div class="item-detail-body">

          {{-- NAMA BARANG --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-box-seam"></i>
            </div>

            <div class="item-info-content">
              <span>Nama Barang</span>
              <strong>{{ $item->name }}</strong>
            </div>
          </div>

          {{-- KATEGORI --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-tags"></i>
            </div>

            <div class="item-info-content">
              <span>Kategori</span>
              <strong>{{ $item->category?->name ?? '-' }}</strong>
            </div>
          </div>

          {{-- SATUAN --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-rulers"></i>
            </div>

            <div class="item-info-content">
              <span>Satuan</span>
              <strong>{{ $item->unit?->name ?? '-' }}</strong>
            </div>
          </div>

          {{-- STOK --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-stack"></i>
            </div>

            <div class="item-info-content">
              <span>Stok Saat Ini</span>
              <strong>
                {{ $formatNumber($item->stock) }}
                {{ $item->unit?->name ?? '' }}
              </strong>
            </div>
          </div>

          {{-- MINIMUM STOK --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-exclamation-triangle"></i>
            </div>

            <div class="item-info-content">
              <span>Minimum Stok</span>
              <strong>
                {{ $formatNumber($item->stock_min) }}
                {{ $item->unit?->name ?? '' }}
              </strong>
            </div>
          </div>

          {{-- LOW STOCK --}}
          @if ($isLowStock)
            <div class="item-stock-warning">
              <i class="bi bi-exclamation-circle"></i>
              <span>
                Stok barang berada di bawah atau sama dengan batas minimum.
              </span>
            </div>
          @endif

        </div>
      </div>

      {{-- INFORMASI REORDER POINT --}}
      <div class="item-detail-card">

        <div class="item-detail-card-header">
          <div>
            <span class="item-detail-label">PERENCANAAN STOK</span>
            <h5 class="item-detail-title">
              Reorder Point (ROP)
            </h5>
          </div>

          <div class="item-header-icon">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
        </div>

        <div class="item-detail-body">

          {{-- LEAD TIME --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-calendar3"></i>
            </div>

            <div class="item-info-content">
              <span>Lead Time</span>
              <strong>
                {{ $ropData['lead_time_days'] }} hari
              </strong>
            </div>
          </div>

          {{-- SAFETY STOCK --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-shield-check"></i>
            </div>

            <div class="item-info-content">
              <span>Safety Stock</span>
              <strong>
                {{ $formatNumber($ropData['safety_stock']) }}
                {{ $item->unit?->name ?? '' }}
              </strong>
            </div>
          </div>

          {{-- TOTAL PEMAKAIAN --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-box-arrow-up"></i>
            </div>

            <div class="item-info-content">
              <span>Total Pemakaian (30 Hari)</span>
              <strong>
                {{ $formatNumber($ropData['total_usage']) }}
                {{ $item->unit?->name ?? '' }}
              </strong>
            </div>
          </div>

          {{-- RATA-RATA PEMAKAIAN --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-graph-up"></i>
            </div>

            <div class="item-info-content">
              <span>Rata-rata Pemakaian per Hari</span>
              <strong>
                {{ $formatNumber($ropData['average_daily_usage']) }}
                {{ $item->unit?->name ?? '' }}/hari
              </strong>
            </div>
          </div>

          {{-- KEBUTUHAN LEAD TIME --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-hourglass-split"></i>
            </div>

            <div class="item-info-content">
              <span>Kebutuhan Selama Lead Time</span>
              <strong>
                {{ $formatNumber($ropData['lead_time_demand']) }}
                {{ $item->unit?->name ?? '' }}
              </strong>
            </div>
          </div>

          {{-- REORDER POINT --}}
          <div class="item-info-row">
            <div class="item-info-icon">
              <i class="bi bi-calculator"></i>
            </div>

            <div class="item-info-content">
              <span>Reorder Point</span>
              <strong>
                {{ $formatNumber($ropData['rop']) }}
                {{ $item->unit?->name ?? '' }}
              </strong>
            </div>
          </div>

          {{-- STATUS ROP --}}
          @if ($isInactive)
            <div class="item-stock-warning">
              <i class="bi bi-info-circle"></i>
              <span>
                Perhitungan ROP tidak digunakan karena barang sedang nonaktif.
              </span>
            </div>
          @elseif ($isBelowRop)
            <div class="item-stock-warning">
              <i class="bi bi-exclamation-circle"></i>
              <span>
                Stok saat ini sudah mencapai atau berada di bawah nilai ROP.
                Barang perlu direstok.
              </span>
            </div>
          @else
            <div class="item-stock-warning safe">
              <i class="bi bi-check-circle"></i>
              <span>
                Stok saat ini masih berada di atas nilai ROP.
                Kondisi stok aman.
              </span>
            </div>
          @endif

        </div>
      </div>

      {{-- RIWAYAT AKTIVITAS --}}
      <div class="item-detail-card">

        <div class="item-detail-card-header">
          <div>
            <span class="item-detail-label">AKTIVITAS</span>
            <h5 class="item-detail-title">
              Riwayat Aktivitas
            </h5>
          </div>

          <div class="item-header-icon">
            <i class="bi bi-clock-history"></i>
          </div>
        </div>

        <div class="item-activity-list">

          {{-- CREATED --}}
          @if ($item->activity?->created_at)
            <div class="item-activity-row">
              <div class="item-activity-icon created">
                <i class="bi bi-plus-lg"></i>
              </div>

              <div class="item-activity-content">
                <strong>Barang dibuat</strong>

                <span>
                  {{ $item->activity->created_at->format('d-m-Y H:i') }}
                </span>

                <small>
                  Oleh:
                  {{ $item->activity->creator?->name ?? '-' }}
                </small>
              </div>
            </div>
          @endif

          {{-- UPDATED --}}
          @if ($item->activity?->updated_at)
            <div class="item-activity-row">
              <div class="item-activity-icon updated">
                <i class="bi bi-pencil"></i>
              </div>

              <div class="item-activity-content">
                <strong>Barang diperbarui</strong>

                <span>
                  {{ $item->activity->updated_at->format('d-m-Y H:i') }}
                </span>

                <small>
                  Oleh:
                  {{ $item->activity->updater?->name ?? '-' }}
                </small>
              </div>
            </div>
          @endif

          {{-- DELETED / NONAKTIF --}}
          @if ($item->activity?->deleted_at)
            <div class="item-activity-row">
              <div class="item-activity-icon deleted">
                <i class="bi bi-trash"></i>
              </div>

              <div class="item-activity-content">
                <strong>Barang dinonaktifkan</strong>

                <span>
                  {{ $item->activity->deleted_at->format('d-m-Y H:i') }}
                </span>

                <small>
                  Oleh:
                  {{ $item->activity->deleter?->name ?? '-' }}
                </small>
              </div>
            </div>
          @endif

          {{-- EMPTY --}}
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
