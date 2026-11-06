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
            <h2>{{ $restockRequest->item?->name ?? '-' }}</h2>
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
          <strong>{{ $restockRequest->item?->name ?? '-' }}</strong>
        </div>

        <div class="restock-detail-item">
          <span class="detail-label">Kategori</span>
          <strong>{{ $restockRequest->item?->category?->name ?? '-' }}</strong>
        </div>

        <div class="restock-detail-item">
          <span class="detail-label">Satuan</span>
          <strong>{{ $restockRequest->item?->unit?->name ?? '-' }}</strong>
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
            <strong>{{ $restockRequest->user?->name ?? '-' }}</strong>
          </div>
        </div>

        <div class="restock-detail-item">
          <span class="detail-label">Tanggal Pengajuan</span>

          @if ($restockRequest->activity?->created_at)
            <strong>{{ $restockRequest->activity->created_at->format('d-m-Y') }}</strong>
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
            <div class="detail-note">{{ $restockRequest->note }}</div>
          @else
            <span class="detail-muted">Tidak ada catatan.</span>
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
              <strong>{{ $restockRequest->processor->name }}</strong>
            </div>
          @else
            <span class="detail-muted">Belum diproses</span>
          @endif
        </div>

        <div class="restock-detail-item">
          <span class="detail-label">Waktu Proses</span>

          @if ($restockRequest->processed_at)
            <strong>{{ $restockRequest->processed_at->format('d-m-Y') }}</strong>
            <small class="detail-muted">
              {{ $restockRequest->processed_at->format('H:i') }}
            </small>
          @else
            <span class="detail-muted">Belum diproses</span>
          @endif
        </div>

        @if ($restockRequest->status === 'rejected')
          <div class="restock-detail-item restock-detail-full">
            <span class="detail-label">Alasan Penolakan</span>

            <div class="detail-rejection">
              <i class="bi bi-x-circle"></i>
              <span>{{ $restockRequest->rejection_reason ?? '-' }}</span>
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

    {{-- RIWAYAT AKTIVITAS --}}
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
                Ajuan {{ $restockRequest->status === 'approved' ? 'disetujui' : 'ditolak' }}
              </strong>
              <span>{{ $restockRequest->processor?->name ?? '-' }}</span>
              <small>{{ $restockRequest->processed_at->format('d-m-Y H:i') }}</small>
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
              <span>{{ $restockRequest->activity->deleter?->name ?? '-' }}</span>
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

  {{-- MODAL PENOLAKAN --}}
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
            placeholder="Masukkan alasan pengajuan ditolak...">{{ old('rejection_reason') }}</textarea>

          <div id="rejectionReasonError" class="invalid-feedback d-none"></div>
          <div id="rejectServerError" class="restock-alert restock-alert-danger d-none"></div>
        </div>

        <div class="restock-modal-footer">
          <button type="button" class="restock-modal-cancel" id="rejectCancelButton" onclick="closeRejectModal()">
            Batal
          </button>

          <button type="submit" class="restock-modal-submit" id="rejectSubmitButton">
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
    /**
     * KONFIRMASI PERSETUJUAN
     */
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
        title,
        message,
        action: url,
        method,
        type: 'success',
        submitText,
        icon
      });
    }

    /**
     * BUKA MODAL PENOLAKAN
     */
    function openRejectModal(url, itemName) {
      const modal = document.getElementById('rejectModal');
      const form = document.getElementById('rejectForm');
      const textarea = document.getElementById('rejection_reason');

      form.action = url;
      form.reset();

      clearRejectErrors();

      textarea.value = '';
      document.getElementById('rejectItemName').textContent = `Barang: ${itemName}`;

      modal.classList.add('show');
      document.body.classList.add('modal-open');

      setTimeout(() => textarea.focus(), 100);
    }

    /**
     * TUTUP MODAL PENOLAKAN
     */
    function closeRejectModal() {
      const modal = document.getElementById('rejectModal');

      modal.classList.remove('show');
      document.body.classList.remove('modal-open');
    }

    /**
     * BERSIHKAN PESAN VALIDASI
     */
    function clearRejectErrors() {
      const textarea = document.getElementById('rejection_reason');
      const reasonError = document.getElementById('rejectionReasonError');
      const serverError = document.getElementById('rejectServerError');

      textarea.classList.remove('is-invalid');

      reasonError.textContent = '';
      reasonError.classList.add('d-none');

      serverError.textContent = '';
      serverError.classList.add('d-none');
    }

    /**
     * TAMPILKAN VALIDASI ALASAN PENOLAKAN
     */
    function showRejectError(message) {
      const textarea = document.getElementById('rejection_reason');
      const reasonError = document.getElementById('rejectionReasonError');

      textarea.classList.add('is-invalid');
      reasonError.textContent = message;
      reasonError.classList.remove('d-none');
    }

    /**
     * TAMPILKAN ERROR SERVER
     */
    function showRejectServerError(message) {
      const serverError = document.getElementById('rejectServerError');

      serverError.textContent = message;
      serverError.classList.remove('d-none');
    }

    /**
     * PROSES FORM PENOLAKAN DENGAN AJAX
     */
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('rejectForm');
      const submitButton = document.getElementById('rejectSubmitButton');
      const cancelButton = document.getElementById('rejectCancelButton');
      const textarea = document.getElementById('rejection_reason');

      if (!form || !submitButton || !cancelButton || !textarea) {
        return;
      }

      const originalButtonContent = submitButton.innerHTML;

      form.addEventListener('submit', async function(event) {
        event.preventDefault();

        clearRejectErrors();

        submitButton.disabled = true;
        cancelButton.disabled = true;
        submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status"></span>
                Memproses...
            `;

        try {
          const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const contentType = response.headers.get('content-type') || '';

          if (!contentType.includes('application/json')) {
            throw new Error(
              response.status === 419 ?
              'Sesi telah berakhir. Silakan muat ulang halaman.' :
              'Respons server tidak sesuai. Periksa kembali controller penolakan.'
            );
          }

          const result = await response.json();

          /**
           * VALIDASI FORM GAGAL
           * Modal tetap terbuka.
           */
          if (response.status === 422) {
            if (result.errors?.rejection_reason?.length) {
              showRejectError(result.errors.rejection_reason[0]);
              textarea.focus();
            } else {
              showRejectServerError(
                result.message || 'Pengajuan gagal diproses.'
              );
            }

            return;
          }

          /**
           * ERROR DARI SERVER
           */
          if (!response.ok || result.success !== true) {
            throw new Error(
              result.message || 'Penolakan pengajuan gagal diproses.'
            );
          }

          /**
           * BERHASIL
           * Modal ditutup setelah server mengonfirmasi keberhasilan.
           */
          closeRejectModal();

          if (result.redirect) {
            window.location.href = result.redirect;
          } else {
            window.location.reload();
          }

        } catch (error) {
          showRejectServerError(
            error.message || 'Terjadi kesalahan pada server.'
          );

        } finally {
          submitButton.disabled = false;
          cancelButton.disabled = false;
          submitButton.innerHTML = originalButtonContent;
        }
      });

      /**
       * HAPUS PESAN ERROR KETIKA PENGGUNA MULAI MENGETIK
       */
      textarea.addEventListener('input', function() {
        clearRejectErrors();
      });
    });
  </script>
@endpush
