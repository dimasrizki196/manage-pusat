@extends('layouts.app')

@section('title', 'Ajuan Restock')

@push('styles')
  @vite('resources/css/admin/restock-requests.css')
@endpush

@section('content')
  <div class="restock-page">
    {{-- PAGE HEADER --}}
    <div class="restock-page-header">
      <div class="restock-page-header-content">
        <div class="restock-breadcrumb">
          <a href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
          <i class="bi bi-chevron-right"></i>
          <span>Ajuan Restock</span>
        </div>

        <h1 class="restock-page-title">Ajuan Restock</h1>
        <p class="restock-page-subtitle">
          Kelola pengajuan restock barang dari barista.
        </p>
      </div>
    </div>

    {{-- FLASH MESSAGE --}}
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

    {{-- MAIN CARD --}}
    <div class="restock-card">

      {{-- FILTER --}}
      <div class="restock-filter">
        <form method="GET" action="{{ route('admin.restock-requests.index') }}" class="restock-filter-form">

          <div class="restock-search">
            <i class="bi bi-search"></i>
            <input type="text" name="search" value="{{ request('search') }}"
              placeholder="Cari barang atau barista...">
          </div>

          <select name="status" class="restock-filter-select">
            <option value="">Semua Status</option>
            <option value="pending" @selected(request('status') === 'pending')>
              Menunggu
            </option>
            <option value="approved" @selected(request('status') === 'approved')>
              Disetujui
            </option>
            <option value="rejected" @selected(request('status') === 'rejected')>
              Ditolak
            </option>
            <option value="deleted" @selected(request('status') === 'deleted')>
              Dihapus
            </option>
          </select>

          <button type="submit" class="restock-filter-button">
            <i class="bi bi-funnel"></i>
            <span>Filter</span>
          </button>

          @if (request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.restock-requests.index') }}" class="restock-reset-button">
              <i class="bi bi-arrow-counterclockwise"></i>
              Reset
            </a>
          @endif
        </form>
      </div>

      {{-- TABLE HEADER --}}
      <div class="restock-card-header">
        <div>
          <h2>Daftar Ajuan Restock</h2>
          <p>{{ $restockRequests->total() }} pengajuan</p>
        </div>
      </div>

      {{-- TABLE --}}
      <div class="restock-table-wrapper">
        <table class="restock-table">
          <thead>
            <tr>
              <th width="55">NO</th>
              <th>BARANG</th>
              <th>BARISTA</th>
              <th>JUMLAH</th>
              <th>TANGGAL</th>
              <th>STATUS</th>
              <th width="65">AKSI</th>
            </tr>
          </thead>

          <tbody>
            @forelse ($restockRequests as $request)
              @php
                $isDeleted = $request->activity?->deleted_at !== null;
                $quantity = rtrim(rtrim(number_format($request->quantity, 2, ',', '.'), '0'), ',');
                $itemName = $request->item?->name ?? '-';
              @endphp

              <tr class="{{ $isDeleted ? 'restock-row-deleted' : '' }}">

                {{-- NUMBER --}}
                <td class="restock-number">
                  {{ $restockRequests->firstItem() + $loop->index }}
                </td>

                {{-- ITEM --}}
                <td>
                  <div class="restock-item">
                    <div class="restock-item-icon">
                      <i class="bi bi-box-seam"></i>
                    </div>

                    <div class="restock-item-content">
                      <strong>{{ $itemName }}</strong>
                      <span>
                        {{ $request->item?->category?->name ?? '-' }}
                        <span class="restock-dot">·</span>
                        {{ $request->item?->unit?->name ?? '-' }}
                      </span>
                    </div>
                  </div>
                </td>

                {{-- USER --}}
                <td>
                  <div class="restock-user">
                    <div class="restock-avatar">
                      {{ strtoupper(substr($request->user?->name ?? '-', 0, 1)) }}
                    </div>

                    <span>{{ $request->user?->name ?? '-' }}</span>
                  </div>
                </td>

                {{-- QUANTITY --}}
                <td>
                  <div class="restock-quantity">
                    <strong>{{ $quantity }}</strong>
                    <span>{{ $request->item?->unit?->name ?? '' }}</span>
                  </div>
                </td>

                {{-- DATE --}}
                <td>
                  @if ($request->activity?->created_at)
                    <div class="restock-date">
                      <strong>
                        {{ $request->activity->created_at->format('d-m-Y') }}
                      </strong>
                      <span>
                        {{ $request->activity->created_at->format('H:i') }}
                      </span>
                    </div>
                  @else
                    <span class="restock-muted">-</span>
                  @endif
                </td>

                {{-- STATUS --}}
                <td>
                  @if ($isDeleted)
                    <span class="restock-status restock-status-deleted">
                      <i class="bi bi-trash3"></i>
                      Dihapus
                    </span>
                  @elseif ($request->status === 'pending')
                    <span class="restock-status restock-status-pending">
                      <i class="bi bi-clock"></i>
                      Menunggu
                    </span>
                  @elseif ($request->status === 'approved')
                    <span class="restock-status restock-status-approved">
                      <i class="bi bi-check-circle"></i>
                      Disetujui
                    </span>
                  @elseif ($request->status === 'rejected')
                    <span class="restock-status restock-status-rejected">
                      <i class="bi bi-x-circle"></i>
                      Ditolak
                    </span>
                  @else
                    <span class="restock-status">
                      {{ ucfirst($request->status ?? '-') }}
                    </span>
                  @endif
                </td>

                {{-- ACTION --}}
                <td>
                  <div class="restock-action-wrapper">
                    <button type="button" class="restock-action-button" onclick="openRestockActionMenu(this)"
                      aria-label="Menu aksi" aria-expanded="false">
                      <i class="bi bi-three-dots"></i>
                    </button>

                    <div class="restock-action-menu">

                      {{-- DETAIL --}}
                      <a href="{{ route('admin.restock-requests.show', $request->id) }}">
                        <i class="bi bi-eye"></i>
                        Lihat Detail
                      </a>

                      @if ($isDeleted)
                        {{-- RESTORE --}}
                        <button type="button" class="action-success"
                          onclick="confirmRestockAction(
                                                        '{{ route('admin.restock-requests.restore', $request->id) }}',
                                                        {{ Js::from($itemName) }},
                                                        'restore'
                                                    )">
                          <i class="bi bi-arrow-counterclockwise"></i>
                          Pulihkan
                        </button>

                        {{-- FORCE DELETE --}}
                        <button type="button" class="action-danger"
                          onclick="confirmRestockAction(
                                                        '{{ route('admin.restock-requests.force-destroy', $request->id) }}',
                                                        {{ Js::from($itemName) }},
                                                        'force-delete'
                                                    )">
                          <i class="bi bi-trash3"></i>
                          Hapus Permanen
                        </button>
                      @else
                        @if ($request->status === 'pending')
                          {{-- APPROVE --}}
                          <button type="button" class="action-success"
                            onclick="confirmRestockAction(
                                                            '{{ route('admin.restock-requests.approve', $request->id) }}',
                                                            {{ Js::from($itemName) }},
                                                            'approve'
                                                        )">
                            <i class="bi bi-check-circle"></i>
                            Setujui
                          </button>

                          {{-- REJECT --}}
                          <button type="button" class="action-warning"
                            onclick="openRejectModal(
                                                            '{{ route('admin.restock-requests.reject', $request->id) }}',
                                                            {{ Js::from($itemName) }}
                                                        )">
                            <i class="bi bi-x-circle"></i>
                            Tolak
                          </button>
                        @endif

                        {{-- DELETE --}}
                        <button type="button" class="action-danger"
                          onclick="confirmRestockAction(
                                                        '{{ route('admin.restock-requests.destroy', $request->id) }}',
                                                        {{ Js::from($itemName) }},
                                                        'delete'
                                                    )">
                          <i class="bi bi-trash3"></i>
                          Hapus
                        </button>
                      @endif
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7">
                  <div class="restock-empty">
                    <div class="restock-empty-icon">
                      <i class="bi bi-clipboard-check"></i>
                    </div>

                    <strong>Belum ada ajuan restock</strong>
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
      @if ($restockRequests->hasPages())
        <div class="restock-pagination">
          {{ $restockRequests->appends(request()->query())->links() }}
        </div>
      @endif
    </div>
  </div>

  {{-- MODAL REJECT --}}
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

        <button type="button" class="restock-modal-close" onclick="closeRejectModal()" aria-label="Tutup modal">
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
            placeholder="Masukkan alasan pengajuan ditolak..."></textarea>

          <div id="rejectionReasonError" class="invalid-feedback d-none">
          </div>

          <div id="rejectServerError" class="restock-alert restock-alert-danger d-none">
          </div>

          <small class="detail-muted">
            Maksimal 1.000 karakter.
          </small>
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
     * DROPDOWN AKSI
     */
    function closeRestockActionMenus() {
      document.querySelectorAll('.restock-action-menu.show').forEach(menu => {
        menu.classList.remove('show');

        const button = menu.previousElementSibling;
        if (button) {
          button.setAttribute('aria-expanded', 'false');
        }
      });
    }

    function openRestockActionMenu(button) {
      const menu = button.nextElementSibling;
      const isAlreadyOpen = menu.classList.contains('show');

      closeRestockActionMenus();

      if (!isAlreadyOpen) {
        menu.classList.add('show');
        button.setAttribute('aria-expanded', 'true');

        const rect = button.getBoundingClientRect();

        menu.style.top = `${rect.bottom + window.scrollY + 6}px`;
        menu.style.left = `${rect.right + window.scrollX - menu.offsetWidth}px`;
      }
    }

    /**
     * KONFIRMASI AKSI
     */
    function confirmRestockAction(url, itemName, action) {
      closeRestockActionMenus();

      let title = '';
      let message = '';
      let method = '';
      let submitText = '';
      let icon = '';
      let type = 'danger';

      if (action === 'approve') {
        title = 'Setujui Pengajuan';
        message =
          `Apakah Anda yakin ingin menyetujui pengajuan restock barang ${itemName}? Persetujuan ini tidak langsung menambah stok. Stok bertambah saat barang diterima melalui Barang Masuk.`;
        method = 'PATCH';
        submitText = 'Ya, Setujui';
        icon = 'bi-check-circle';
        type = 'success';
      }

      if (action === 'delete') {
        title = 'Hapus Pengajuan';
        message = `Pengajuan ${itemName} akan dipindahkan ke data terhapus dan masih dapat dipulihkan. Lanjutkan?`;
        method = 'DELETE';
        submitText = 'Ya, Hapus';
        icon = 'bi-trash3';
      }

      if (action === 'restore') {
        title = 'Pulihkan Pengajuan';
        message = `Apakah Anda yakin ingin memulihkan pengajuan ${itemName}?`;
        method = 'PATCH';
        submitText = 'Ya, Pulihkan';
        icon = 'bi-arrow-counterclockwise';
        type = 'success';
      }

      if (action === 'force-delete') {
        title = 'Hapus Permanen';
        message = `Pengajuan ${itemName} akan dihapus secara permanen dan tidak dapat dipulihkan. Lanjutkan?`;
        method = 'DELETE';
        submitText = 'Ya, Hapus Permanen';
        icon = 'bi-trash3';
      }

      openConfirmation({
        title,
        message,
        action: url,
        method,
        type,
        submitText,
        icon
      });
    }

    /**
     * MODAL PENOLAKAN
     */
    function openRejectModal(url, itemName) {
      const modal = document.getElementById('rejectModal');
      const form = document.getElementById('rejectForm');
      const textarea = document.getElementById('rejection_reason');

      closeRestockActionMenus();

      form.action = url;
      form.reset();

      clearRejectErrors();

      textarea.value = '';
      document.getElementById('rejectItemName').textContent = `Barang: ${itemName}`;

      modal.classList.add('show');
      document.body.classList.add('modal-open');

      setTimeout(() => textarea.focus(), 100);
    }

    function closeRejectModal() {
      const modal = document.getElementById('rejectModal');

      modal.classList.remove('show');
      document.body.classList.remove('modal-open');
    }

    /**
     * VALIDASI MODAL PENOLAKAN
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

    function showRejectError(message) {
      const textarea = document.getElementById('rejection_reason');
      const reasonError = document.getElementById('rejectionReasonError');

      textarea.classList.add('is-invalid');
      reasonError.textContent = message;
      reasonError.classList.remove('d-none');
    }

    function showRejectServerError(message) {
      const serverError = document.getElementById('rejectServerError');

      serverError.textContent = message;
      serverError.classList.remove('d-none');
    }

    /**
     * SUBMIT PENOLAKAN DENGAN AJAX
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
      let isSubmitting = false;

      form.addEventListener('submit', async function(event) {
        event.preventDefault();

        if (isSubmitting) {
          return;
        }

        clearRejectErrors();

        isSubmitting = true;
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
            if (response.status === 419) {
              throw new Error('Sesi telah berakhir. Silakan muat ulang halaman.');
            }

            throw new Error('Respons server tidak sesuai. Silakan coba kembali.');
          }

          const result = await response.json();

          /**
           * VALIDASI GAGAL
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

          if (!response.ok || result.success !== true) {
            throw new Error(
              result.message || 'Penolakan pengajuan gagal diproses.'
            );
          }

          /**
           * BERHASIL
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
          isSubmitting = false;
          submitButton.disabled = false;
          cancelButton.disabled = false;
          submitButton.innerHTML = originalButtonContent;
        }
      });

      textarea.addEventListener('input', clearRejectErrors);
    });

    /**
     * KLIK DI LUAR DROPDOWN
     */
    document.addEventListener('click', function(event) {
      if (!event.target.closest('.restock-action-wrapper')) {
        closeRestockActionMenus();
      }
    });

    /**
     * TUTUP DROPDOWN KETIKA SCROLL
     */
    window.addEventListener('scroll', closeRestockActionMenus, true);

    /**
     * TUTUP MODAL DENGAN TOMBOL ESCAPE
     */
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeRejectModal();
        closeRestockActionMenus();
      }
    });
  </script>
@endpush
