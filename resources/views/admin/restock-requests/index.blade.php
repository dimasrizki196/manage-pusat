@extends('layouts.app')

@section('title', 'Ajuan Restock')

@push('styles')
    @vite('resources/css/admin/restock-requests.css')
@endpush

@section('content')
    <div class="restock-page">

        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Ajuan Restock</h1>
                <p class="page-subtitle">
                    Kelola pengajuan restock barang dari barista.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="restock-card">

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

            <div class="restock-card-header">
                <div>
                    <h5>Daftar Ajuan Restock</h5>
                    <span>{{ $restockRequests->total() }} pengajuan</span>
                </div>
            </div>

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
                            @endphp

                            <tr class="{{ $isDeleted ? 'restock-row-deleted' : '' }}">

                                <td>
                                    {{ $restockRequests->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <div class="restock-item">
                                        <strong>
                                            {{ $request->item?->name ?? '-' }}
                                        </strong>

                                        <span>
                                            {{ $request->item?->category?->name ?? '-' }}
                                            ·
                                            {{ $request->item?->unit?->name ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <div class="restock-user">
                                        <div class="restock-avatar">
                                            {{ strtoupper(substr($request->user?->name ?? '-', 0, 1)) }}
                                        </div>

                                        <span>
                                            {{ $request->user?->name ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <strong>{{ $quantity }}</strong>

                                    <span class="restock-unit">
                                        {{ $request->item?->unit?->name ?? '' }}
                                    </span>
                                </td>

                                <td>
                                    @if ($request->activity?->created_at)
                                        {{ $request->activity->created_at->format('d-m-Y') }}

                                        <small class="restock-time">
                                            {{ $request->activity->created_at->format('H:i') }}
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>

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

                                <td>
                                    <div class="restock-action-wrapper">

                                        <button type="button" class="restock-action-button"
                                            onclick="openRestockActionMenu(this)" aria-label="Menu aksi">
                                            <i class="bi bi-three-dots"></i>
                                        </button>

                                        <div class="restock-action-menu">

                                            <a href="{{ route('admin.restock-requests.show', $request->id) }}">
                                                <i class="bi bi-eye"></i>
                                                Lihat Detail
                                            </a>

                                            @if ($isDeleted)
                                                <button type="button" class="action-success"
                                                    onclick="confirmRestockAction(
                                                        '{{ route('admin.restock-requests.restore', $request->id) }}',
                                                        {{ Js::from($request->item?->name ?? '-') }},
                                                        'restore'
                                                    )">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                    Pulihkan
                                                </button>

                                                <button type="button" class="action-danger"
                                                    onclick="confirmRestockAction(
                                                        '{{ route('admin.restock-requests.force-destroy', $request->id) }}',
                                                        {{ Js::from($request->item?->name ?? '-') }},
                                                        'force-delete'
                                                    )">
                                                    <i class="bi bi-trash3"></i>
                                                    Hapus Permanen
                                                </button>
                                            @else
                                                @if ($request->status === 'pending')
                                                    <button type="button" class="action-success"
                                                        onclick="confirmRestockAction(
                                                            '{{ route('admin.restock-requests.approve', $request->id) }}',
                                                            {{ Js::from($request->item?->name ?? '-') }},
                                                            'approve'
                                                        )">
                                                        <i class="bi bi-check-circle"></i>
                                                        Setujui
                                                    </button>

                                                    <button type="button" class="action-warning"
                                                        onclick="openRejectModal(
                                                            '{{ route('admin.restock-requests.reject', $request->id) }}',
                                                            {{ Js::from($request->item?->name ?? '-') }}
                                                        )">
                                                        <i class="bi bi-x-circle"></i>
                                                        Tolak
                                                    </button>
                                                @endif

                                                <button type="button" class="action-danger"
                                                    onclick="confirmRestockAction(
                                                        '{{ route('admin.restock-requests.destroy', $request->id) }}',
                                                        {{ Js::from($request->item?->name ?? '-') }},
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

                                        <strong>
                                            Belum ada ajuan restock
                                        </strong>

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

            @if ($restockRequests->hasPages())
                <div class="restock-pagination">
                    {{ $restockRequests->links() }}
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

                    @error('rejection_reason')
                        <small class="restock-error">
                            {{ $message }}
                        </small>
                    @enderror

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

    {{-- FORM CONFIRMATION --}}
    <form id="restockActionForm" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="_method" id="restockActionMethod">
    </form>

@endsection

@push('scripts')
    <script>
        function openRestockActionMenu(button) {
            document.querySelectorAll('.restock-action-menu.show').forEach(menu => {
                if (menu !== button.nextElementSibling) {
                    menu.classList.remove('show');
                }
            });

            const menu = button.nextElementSibling;

            menu.classList.toggle('show');

            if (menu.classList.contains('show')) {
                const rect = button.getBoundingClientRect();

                menu.style.top =
                    (rect.bottom + window.scrollY + 6) + 'px';

                menu.style.left =
                    (rect.right + window.scrollX - menu.offsetWidth) + 'px';
            }
        }

        function confirmRestockAction(url, itemName, action) {
            let title = '';
            let message = '';
            let method = '';
            let submitText = '';
            let icon = '';

            if (action === 'approve') {
                title = 'Setujui Pengajuan';
                message =
                    `Apakah Anda yakin ingin menyetujui pengajuan restock barang ${itemName}? Stok barang akan bertambah setelah pengajuan disetujui.`;
                method = 'PATCH';
                submitText = 'Ya, Setujui';
                icon = 'bi-check-circle';
            }

            if (action === 'delete') {
                title = 'Hapus Pengajuan';
                message =
                    `Pengajuan ${itemName} akan dipindahkan ke data terhapus dan masih dapat dipulihkan. Lanjutkan?`;
                method = 'DELETE';
                submitText = 'Ya, Hapus';
                icon = 'bi-trash3';
            }

            if (action === 'restore') {
                title = 'Pulihkan Pengajuan';
                message =
                    `Apakah Anda yakin ingin memulihkan pengajuan ${itemName}?`;
                method = 'PATCH';
                submitText = 'Ya, Pulihkan';
                icon = 'bi-arrow-counterclockwise';
            }

            if (action === 'force-delete') {
                title = 'Hapus Permanen';
                message =
                    `Pengajuan ${itemName} akan dihapus secara permanen dan tidak dapat dipulihkan. Lanjutkan?`;
                method = 'DELETE';
                submitText = 'Ya, Hapus Permanen';
                icon = 'bi-trash3';
            }

            openConfirmation({
                title: title,
                message: message,
                action: url,
                method: method,
                type: action === 'restore' ? 'success' : 'danger',
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
            itemNameElement.textContent =
                `Barang: ${itemName}`;

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

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.restock-action-wrapper')) {
                document.querySelectorAll('.restock-action-menu.show')
                    .forEach(menu => menu.classList.remove('show'));
            }
        });

        window.addEventListener('scroll', function() {
            document.querySelectorAll('.restock-action-menu.show')
                .forEach(menu => menu.classList.remove('show'));
        }, true);
    </script>
@endpush
