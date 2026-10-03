@extends('layouts.app')

@section('title', 'Detail Ajuan Restock')

@push('styles')
    @vite('resources/css/admin/restock-requests.css')
@endpush

@section('content')
    @php
        $isDeleted = $restockRequest->activity?->deleted_at !== null;
        $quantity = rtrim(rtrim(number_format($restockRequest->quantity, 2, ',', '.'), '0'), ',');
    @endphp

    <div class="restock-page">
        <div class="restock-page-header">
            <div class="restock-page-header-content">
                <div class="restock-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <a href="{{ route('admin.restock-requests.index') }}">Ajuan Restock</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Detail</span>
                </div>

                <div class="restock-detail-header-row">
                    <div>
                        <h1 class="restock-page-title">Detail Ajuan Restock</h1>
                        <p class="restock-page-subtitle">
                            Informasi lengkap pengajuan restock barang.
                        </p>
                    </div>

                    <a href="{{ route('admin.restock-requests.index') }}" class="restock-secondary-button">
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="restock-alert restock-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="restock-alert restock-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- HEADER DETAIL --}}
        <div class="restock-detail-card">
            <div class="restock-detail-header">
                <div class="restock-detail-title">
                    <div class="restock-detail-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>

                    <div>
                        <h2>
                            {{ $restockRequest->item?->name ?? '-' }}
                        </h2>

                        <span>
                            {{ $restockRequest->item?->category?->name ?? '-' }}
                            <span class="restock-dot">·</span>
                            {{ $restockRequest->item?->unit?->name ?? '-' }}
                        </span>
                    </div>
                </div>

                @if ($isDeleted)
                    <span class="restock-status restock-status-deleted">
                        <i class="bi bi-trash3"></i>
                        Dihapus
                    </span>
                @elseif ($restockRequest->status === 'pending')
                    <span class="restock-status restock-status-pending">
                        <i class="bi bi-clock"></i>
                        Menunggu
                    </span>
                @elseif ($restockRequest->status === 'approved')
                    <span class="restock-status restock-status-approved">
                        <i class="bi bi-check-circle"></i>
                        Disetujui
                    </span>
                @elseif ($restockRequest->status === 'rejected')
                    <span class="restock-status restock-status-rejected">
                        <i class="bi bi-x-circle"></i>
                        Ditolak
                    </span>
                @endif
            </div>

            <div class="restock-detail-grid">
                <div class="restock-detail-item">
                    <span class="detail-label">Barang</span>
                    <strong>
                        {{ $restockRequest->item?->name ?? '-' }}
                    </strong>
                </div>

                <div class="restock-detail-item">
                    <span class="detail-label">Kategori</span>
                    <strong>
                        {{ $restockRequest->item?->category?->name ?? '-' }}
                    </strong>
                </div>

                <div class="restock-detail-item">
                    <span class="detail-label">Satuan</span>
                    <strong>
                        {{ $restockRequest->item?->unit?->name ?? '-' }}
                    </strong>
                </div>

                <div class="restock-detail-item">
                    <span class="detail-label">Jumlah Pengajuan</span>
                    <strong class="detail-quantity">
                        {{ $quantity }}
                        {{ $restockRequest->item?->unit?->name ?? '' }}
                    </strong>
                </div>
            </div>
        </div>

        {{-- INFORMASI PENGAJUAN --}}
        <div class="restock-detail-card">
            <div class="restock-section-title">
                <i class="bi bi-person"></i>
                <span>Informasi Pengajuan</span>
            </div>

            <div class="restock-detail-grid">
                <div class="restock-detail-item">
                    <span class="detail-label">Barista</span>

                    <div class="detail-user">
                        <div class="restock-avatar">
                            {{ strtoupper(substr($restockRequest->user?->name ?? '-', 0, 1)) }}
                        </div>

                        <strong>
                            {{ $restockRequest->user?->name ?? '-' }}
                        </strong>
                    </div>
                </div>

                <div class="restock-detail-item">
                    <span class="detail-label">Tanggal Pengajuan</span>

                    @if ($restockRequest->activity?->created_at)
                        <strong>
                            {{ $restockRequest->activity->created_at->format('d-m-Y') }}
                        </strong>

                        <small class="detail-muted">
                            {{ $restockRequest->activity->created_at->format('H:i') }}
                        </small>
                    @else
                        <strong>-</strong>
                    @endif
                </div>

                <div class="restock-detail-item restock-detail-full">
                    <span class="detail-label">Catatan Barista</span>

                    @if ($restockRequest->note)
                        <div class="detail-note">
                            {{ $restockRequest->note }}
                        </div>
                    @else
                        <span class="detail-muted">
                            Tidak ada catatan.
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- INFORMASI PROSES --}}
        <div class="restock-detail-card">
            <div class="restock-section-title">
                <i class="bi bi-person-check"></i>
                <span>Informasi Proses</span>
            </div>

            <div class="restock-detail-grid">
                <div class="restock-detail-item">
                    <span class="detail-label">Diproses Oleh</span>

                    @if ($restockRequest->processor)
                        <div class="detail-user">
                            <div class="restock-avatar">
                                {{ strtoupper(substr($restockRequest->processor->name, 0, 1)) }}
                            </div>

                            <strong>
                                {{ $restockRequest->processor->name }}
                            </strong>
                        </div>
                    @else
                        <span class="detail-muted">
                            Belum diproses
                        </span>
                    @endif
                </div>

                <div class="restock-detail-item">
                    <span class="detail-label">Waktu Proses</span>

                    @if ($restockRequest->processed_at)
                        <strong>
                            {{ $restockRequest->processed_at->format('d-m-Y') }}
                        </strong>

                        <small class="detail-muted">
                            {{ $restockRequest->processed_at->format('H:i') }}
                        </small>
                    @else
                        <span class="detail-muted">
                            Belum diproses
                        </span>
                    @endif
                </div>

                @if ($restockRequest->status === 'rejected')
                    <div class="restock-detail-item restock-detail-full">
                        <span class="detail-label">Alasan Penolakan</span>

                        <div class="detail-rejection">
                            <i class="bi bi-x-circle"></i>
                            <span>
                                {{ $restockRequest->rejection_reason ?? '-' }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- INFORMASI STATUS RESTOCK --}}
        @if ($restockRequest->status === 'approved')
            <div class="restock-detail-card">
                <div class="restock-section-title">
                    <i class="bi bi-info-circle"></i>
                    <span>Informasi Restock</span>
                </div>

                <div class="restock-approved-info">
                    <div class="restock-approved-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div>
                        <strong>Pengajuan telah disetujui</strong>
                        <span>
                            Persetujuan belum menambah stok barang.
                            Stok akan bertambah setelah barang benar-benar diterima
                            dan dicatat melalui menu Barang Masuk.
                        </span>
                    </div>
                </div>
            </div>
        @endif

        {{-- ACTIVITY --}}
        <div class="restock-detail-card">
            <div class="restock-section-title">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Aktivitas</span>
            </div>

            <div class="restock-timeline">
                <div class="restock-timeline-item">
                    <div class="timeline-icon timeline-icon-created">
                        <i class="bi bi-plus-circle"></i>
                    </div>

                    <div class="timeline-content">
                        <strong>Pengajuan dibuat</strong>

                        <span>
                            {{ $restockRequest->activity?->creator?->name ?? ($restockRequest->user?->name ?? '-') }}
                        </span>

                        @if ($restockRequest->activity?->created_at)
                            <small>
                                {{ $restockRequest->activity->created_at->format('d-m-Y H:i') }}
                            </small>
                        @endif
                    </div>
                </div>

                @if ($restockRequest->processed_at)
                    <div class="restock-timeline-item">
                        <div
                            class="timeline-icon {{ $restockRequest->status === 'approved' ? 'timeline-icon-approved' : 'timeline-icon-rejected' }}">
                            @if ($restockRequest->status === 'approved')
                                <i class="bi bi-check-circle"></i>
                            @else
                                <i class="bi bi-x-circle"></i>
                            @endif
                        </div>

                        <div class="timeline-content">
                            <strong>
                                Ajuan
                                {{ $restockRequest->status === 'approved' ? 'disetujui' : 'ditolak' }}
                            </strong>

                            <span>
                                {{ $restockRequest->processor?->name ?? '-' }}
                            </span>

                            <small>
                                {{ $restockRequest->processed_at->format('d-m-Y H:i') }}
                            </small>
                        </div>
                    </div>
                @endif

                @if ($restockRequest->activity?->deleted_at)
                    <div class="restock-timeline-item">
                        <div class="timeline-icon timeline-icon-deleted">
                            <i class="bi bi-trash3"></i>
                        </div>

                        <div class="timeline-content">
                            <strong>Ajuan dihapus</strong>

                            <span>
                                {{ $restockRequest->activity->deleter?->name ?? '-' }}
                            </span>

                            <small>
                                {{ $restockRequest->activity->deleted_at->format('d-m-Y H:i') }}
                            </small>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ACTION --}}
        <div class="restock-detail-actions">
            @if (!$isDeleted && $restockRequest->status === 'pending')
                <div class="restock-detail-action-group">
                    <button type="button" class="detail-button detail-button-warning"
                        onclick="openRejectModal(
                        '{{ route('admin.restock-requests.reject', $restockRequest->id) }}',
                        {{ Js::from($restockRequest->item?->name ?? '-') }}
                    )">
                        <i class="bi bi-x-circle"></i>
                        Tolak
                    </button>

                    <button type="button" class="detail-button detail-button-success"
                        onclick="confirmRestockAction(
                        '{{ route('admin.restock-requests.approve', $restockRequest->id) }}',
                        {{ Js::from($restockRequest->item?->name ?? '-') }},
                        'approve'
                    )">
                        <i class="bi bi-check-circle"></i>
                        Setujui
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div class="restock-modal" id="rejectModal">
        <div class="restock-modal-overlay" onclick="closeRejectModal()"></div>

        <div class="restock-modal-dialog">
            <div class="restock-modal-header">
                <div class="restock-modal-icon restock-modal-icon-warning">
                    <i class="bi bi-x-circle"></i>
                </div>

                <div>
                    <h5>Tolak Pengajuan</h5>
                    <p id="rejectItemName"></p>
                </div>

                <button type="button" class="restock-modal-close" onclick="closeRejectModal()">
                    <i class="bi bi-x"></i>
                </button>
            </div>

            <form method="POST" id="rejectForm">
                @csrf
                @method('PATCH')

                <div class="restock-modal-body">
                    <label for="rejection_reason">
                        Alasan Penolakan
                        <span>*</span>
                    </label>

                    <textarea name="rejection_reason" id="rejection_reason" rows="4" maxlength="1000"
                        placeholder="Masukkan alasan pengajuan ditolak..." required></textarea>
                </div>

                <div class="restock-modal-footer">
                    <button type="button" class="restock-modal-cancel" onclick="closeRejectModal()">
                        Batal
                    </button>

                    <button type="submit" class="restock-modal-submit">
                        <i class="bi bi-x-circle"></i>
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmRestockAction(url, itemName, action) {
            let title = '';
            let message = '';
            let method = '';
            let submitText = '';
            let icon = '';

            if (action === 'approve') {
                title = 'Setujui Pengajuan';
                message =
                    `Apakah Anda yakin ingin menyetujui pengajuan restock barang ${itemName}? Persetujuan ini tidak langsung menambah stok.`;
                method = 'PATCH';
                submitText = 'Ya, Setujui';
                icon = 'bi-check-circle';
            }

            openConfirmation({
                title: title,
                message: message,
                action: url,
                method: method,
                type: 'success',
                submitText: submitText,
                icon: icon
            });
        }

        function openRejectModal(url, itemName) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            const itemNameElement = document.getElementById('rejectItemName');
            const textarea = document.getElementById('rejection_reason');

            form.action = url;
            itemNameElement.textContent = `Barang: ${itemName}`;
            textarea.value = '';

            modal.classList.add('show');
            document.body.classList.add('modal-open');

            setTimeout(() => textarea.focus(), 100);
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');

            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    </script>
@endpush
