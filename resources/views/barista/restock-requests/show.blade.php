@extends('layouts.barista')

@section('title', 'Detail Ajuan Restock')


@section('content')
    <div class="page-container restock-page-barista">
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-breadcrumb">
                    <a href="{{ route('barista.restock-requests.index') }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Pengajuan Restock</span>
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Detail Ajuan Restock</span>
                </div>

                <h1 class="page-title">Detail Ajuan Restock</h1>
                <p class="page-subtitle">
                    Informasi lengkap pengajuan restock barang.
                </p>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('barista.restock-requests.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        @php
            $statusClass = match ($restockRequest->status) {
                'approved' => 'status-approved',
                'rejected' => 'status-rejected',
                default => 'status-pending',
            };

            $statusLabel = match ($restockRequest->status) {
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                default => 'Pending',
            };
        @endphp

        @if (session('success'))
            <div class="restock-alert restock-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="restock-alert restock-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="restock-show-status">
            <span class="restock-status-badge {{ $statusClass }}">
                @if ($restockRequest->status === 'approved')
                    <i class="bi bi-check-circle-fill"></i>
                @elseif ($restockRequest->status === 'rejected')
                    <i class="bi bi-x-circle-fill"></i>
                @else
                    <i class="bi bi-clock-fill"></i>
                @endif

                {{ $statusLabel }}
            </span>
        </div>

        <div class="restock-detail-grid">
            <div class="restock-detail-card">
                <div class="restock-detail-card-header">
                    <div>
                        <h2>Informasi Barang</h2>
                        <p>Detail barang yang diajukan untuk restock.</p>
                    </div>

                    @if ($restockRequest->status === 'pending')
                        <a href="{{ route('barista.restock-requests.edit', $restockRequest->id) }}"
                            class="restock-detail-edit">
                            <i class="bi bi-pencil"></i>
                            <span>Edit</span>
                        </a>
                    @endif
                </div>

                <div class="restock-detail-list">
                    <div class="restock-detail-row">
                        <span>Nama Barang</span>
                        <strong>
                            {{ $restockRequest->item?->name ?? '-' }}
                        </strong>
                    </div>

                    <div class="restock-detail-row">
                        <span>Kategori</span>
                        <strong>
                            {{ $restockRequest->item?->category?->name ?? '-' }}
                        </strong>
                    </div>

                    <div class="restock-detail-row">
                        <span>Satuan</span>
                        <strong>
                            {{ $restockRequest->item?->unit?->name ?? '-' }}
                        </strong>
                    </div>

                    <div class="restock-detail-row">
                        <span>Jumlah Restock</span>
                        <strong>
                            {{ rtrim(rtrim(number_format((float) $restockRequest->quantity, 2, ',', '.'), '0'), ',') }}
                            {{ $restockRequest->item?->unit?->name ?? '' }}
                        </strong>
                    </div>
                </div>
            </div>

            <div class="restock-detail-card">
                <div class="restock-detail-card-header">
                    <div>
                        <h2>Informasi Pengajuan</h2>
                        <p>Informasi waktu dan status pengajuan.</p>
                    </div>
                </div>

                <div class="restock-detail-list">
                    <div class="restock-detail-row">
                        <span>Diajukan</span>
                        <strong>
                            @if ($restockRequest->activity?->created_at)
                                {{ $restockRequest->activity->created_at->format('d-m-Y H:i') }}
                            @else
                                -
                            @endif
                        </strong>
                    </div>

                    <div class="restock-detail-row">
                        <span>Status</span>
                        <strong>
                            {{ $statusLabel }}
                        </strong>
                    </div>

                    <div class="restock-detail-row restock-detail-row-note">
                        <span>Catatan</span>
                        <strong>
                            {{ $restockRequest->note ?: '-' }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        @if ($restockRequest->status === 'approved')
            <div class="restock-detail-card restock-detail-card-full">
                <div class="restock-detail-card-header">
                    <div>
                        <h2>Informasi Persetujuan</h2>
                        <p>Informasi proses persetujuan dari admin.</p>
                    </div>
                </div>

                <div class="restock-detail-list">
                    <div class="restock-detail-row">
                        <span>Diproses Oleh</span>
                        <strong>
                            {{ $restockRequest->processor?->name ?? '-' }}
                        </strong>
                    </div>

                    <div class="restock-detail-row">
                        <span>Waktu Diproses</span>
                        <strong>
                            {{ $restockRequest->processed_at?->format('d-m-Y H:i') ?? '-' }}
                        </strong>
                    </div>
                </div>

                <div class="restock-approved-info">
                    <i class="bi bi-check-circle-fill"></i>

                    <div>
                        <strong>Ajuan telah disetujui admin.</strong>
                        <p>
                            Persetujuan ini belum menambah stok. Setelah barang
                            benar-benar diterima, catat penerimaannya melalui
                            menu Barang Masuk.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if ($restockRequest->status === 'rejected')
            <div class="restock-detail-card restock-detail-card-full">
                <div class="restock-detail-card-header">
                    <div>
                        <h2>Informasi Penolakan</h2>
                        <p>Informasi proses penolakan dari admin.</p>
                    </div>
                </div>

                <div class="restock-detail-list">
                    <div class="restock-detail-row">
                        <span>Diproses Oleh</span>
                        <strong>
                            {{ $restockRequest->processor?->name ?? '-' }}
                        </strong>
                    </div>

                    <div class="restock-detail-row">
                        <span>Waktu Diproses</span>
                        <strong>
                            {{ $restockRequest->processed_at?->format('d-m-Y H:i') ?? '-' }}
                        </strong>
                    </div>
                </div>

                <div class="restock-rejection-box">
                    <span>Alasan Penolakan</span>
                    <p>
                        {{ $restockRequest->rejection_reason ?: '-' }}
                    </p>
                </div>
            </div>
        @endif

        @if ($restockRequest->status === 'pending')
            <div class="restock-detail-actions">
                <a href="{{ route('barista.restock-requests.edit', $restockRequest->id) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i>
                    <span>Edit Ajuan</span>
                </a>

                <form action="{{ route('barista.restock-requests.destroy', $restockRequest->id) }}" method="POST"
                    onsubmit="return confirm('Hapus ajuan restock ini? Ajuan hanya akan dinonaktifkan dan tidak dihapus permanen.')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
