@extends('layouts.barista')

@section('title', 'Detail Ajuan Restock')

@push('styles')
    @vite('resources/css/barista/restock-requests.css')
@endpush

@section('content')
<div class="restock-show">
    <div class="restock-show-header">
        <div>
            <a href="{{ route('barista.restock-requests.index') }}" class="back-link">
                ← Kembali
            </a>
            <h1>Detail Ajuan Restock</h1>
            <p>Informasi lengkap pengajuan restock barang.</p>
        </div>

        @php
            $statusClass = match ($restockRequest->status) {
                'approved' => 'status-approved',
                'rejected' => 'status-rejected',
                default => 'status-pending',
            };
        @endphp

        <span class="status-badge {{ $statusClass }}">
            {{ ucfirst($restockRequest->status) }}
        </span>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="restock-detail-grid">
        <div class="detail-card">
            <div class="detail-card-header">
                <h3>Informasi Barang</h3>

                @if ($restockRequest->status === 'pending')
                    <a href="{{ route('barista.restock-requests.edit', $restockRequest->id) }}"
                       class="btn-edit">
                        Edit
                    </a>
                @endif
            </div>

            <div class="detail-list">
                <div class="detail-row">
                    <span>Nama Barang</span>
                    <strong>{{ $restockRequest->item?->name ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Kategori</span>
                    <strong>{{ $restockRequest->item?->category?->name ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Satuan</span>
                    <strong>{{ $restockRequest->item?->unit?->name ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Jumlah Restock</span>
                    <strong>
                        {{ rtrim(rtrim(number_format((float) $restockRequest->quantity, 2, ',', '.'), '0'), ',') }}
                        {{ $restockRequest->item?->unit?->name ?? '' }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-header">
                <h3>Informasi Pengajuan</h3>
            </div>

            <div class="detail-list">
                <div class="detail-row">
                    <span>Diajukan</span>
                    <strong>
                        @if ($restockRequest->activity?->created_at)
                            {{ $restockRequest->activity->created_at->format('d-m-Y H:i') }}
                        @else
                            -
                        @endif
                    </strong>
                </div>

                <div class="detail-row">
                    <span>Status</span>
                    <strong>{{ ucfirst($restockRequest->status) }}</strong>
                </div>

                <div class="detail-row">
                    <span>Catatan</span>
                    <strong>
                        {{ $restockRequest->note ?: '-' }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    @if ($restockRequest->status === 'approved')
        <div class="detail-card">
            <div class="detail-card-header">
                <h3>Informasi Persetujuan</h3>
            </div>

            <div class="detail-list">
                <div class="detail-row">
                    <span>Diproses Oleh</span>
                    <strong>
                        {{ $restockRequest->processor?->name ?? '-' }}
                    </strong>
                </div>

                <div class="detail-row">
                    <span>Waktu Diproses</span>
                    <strong>
                        {{ $restockRequest->processed_at?->format('d-m-Y H:i') ?? '-' }}
                    </strong>
                </div>
            </div>

            <div class="approved-info">
                Ajuan telah disetujui oleh admin dan stok barang telah ditambahkan.
            </div>
        </div>
    @endif

    @if ($restockRequest->status === 'rejected')
        <div class="detail-card">
            <div class="detail-card-header">
                <h3>Informasi Penolakan</h3>
            </div>

            <div class="detail-list">
                <div class="detail-row">
                    <span>Diproses Oleh</span>
                    <strong>
                        {{ $restockRequest->processor?->name ?? '-' }}
                    </strong>
                </div>

                <div class="detail-row">
                    <span>Waktu Diproses</span>
                    <strong>
                        {{ $restockRequest->processed_at?->format('d-m-Y H:i') ?? '-' }}
                    </strong>
                </div>
            </div>

            <div class="rejection-box">
                <span>Alasan Penolakan</span>
                <p>
                    {{ $restockRequest->rejection_reason ?: '-' }}
                </p>
            </div>
        </div>
    @endif

    @if ($restockRequest->status === 'pending')
        <div class="detail-actions">
            <a href="{{ route('barista.restock-requests.edit', $restockRequest->id) }}"
               class="btn-primary">
                Edit Ajuan
            </a>

            <form action="{{ route('barista.restock-requests.destroy', $restockRequest->id) }}"
                  method="POST"
                  onsubmit="return confirm('Hapus ajuan restock ini? Ajuan hanya akan dinonaktifkan dan tidak dihapus permanen.')">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn-danger">
                    Hapus
                </button>
            </form>
        </div>
    @endif
</div>
@endsection