@extends('layouts.barista')

@section('title', 'Pengajuan Restock')

@section('content')
    <div class="page-container restock-page-barista">
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-breadcrumb">
                    <a href="{{ route('barista.dashboard') }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>

                    <i class="bi bi-chevron-right"></i>

                    <span>Pengajuan Restock</span>
                </div>

                <h1 class="page-title">Pengajuan Restock</h1>

                <p class="page-subtitle">
                    Kelola dan pantau pengajuan restock barang Anda.
                </p>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('barista.restock-requests.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>
                    <span>Ajukan Restock</span>
                </a>
            </div>
        </div>

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

        <div class="restock-card">
            <div class="restock-card-header">
                <div>
                    <h2>Daftar Pengajuan</h2>

                    <p>
                        Riwayat pengajuan restock yang Anda buat.
                    </p>
                </div>

                <div class="restock-total">
                    <span>Total:</span>
                    <strong>{{ $restockRequests->total() }}</strong>
                    <span>pengajuan</span>
                </div>
            </div>

            <div class="restock-table-wrapper">
                <table class="restock-table">
                    <thead>
                        <tr>
                            <th class="column-number">No</th>
                            <th>Barang</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Diajukan</th>
                            <th class="column-action"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($restockRequests as $request)
                            @php
                                $quantity = rtrim(
                                    rtrim(number_format((float) $request->quantity, 2, ',', '.'), '0'),
                                    ',',
                                );

                                $statusClass = match ($request->status) {
                                    'pending' => 'status-pending',
                                    'approved' => 'status-approved',
                                    'rejected' => 'status-rejected',
                                    default => 'status-default',
                                };

                                $statusIcon = match ($request->status) {
                                    'pending' => 'bi-clock-fill',
                                    'approved' => 'bi-check-circle-fill',
                                    'rejected' => 'bi-x-circle-fill',
                                    default => 'bi-question-circle-fill',
                                };

                                $statusLabel = match ($request->status) {
                                    'pending' => 'Menunggu',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => ucfirst($request->status),
                                };
                            @endphp

                            <tr>
                                <td class="column-number">
                                    <span class="row-number">
                                        {{ $restockRequests->firstItem() + $loop->index }}
                                    </span>
                                </td>

                                <td>
                                    <div class="restock-item">
                                        <strong>
                                            {{ $request->item?->name ?? '-' }}
                                        </strong>
                                    </div>
                                </td>

                                <td>
                                    <span class="category-text">
                                        {{ $request->item?->category?->name ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="quantity-info">
                                        <strong>{{ $quantity }}</strong>

                                        <span>
                                            {{ $request->item?->unit?->name ?? '' }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span class="restock-status {{ $statusClass }}">
                                        <i class="bi {{ $statusIcon }}"></i>
                                        <span>{{ $statusLabel }}</span>
                                    </span>
                                </td>

                                <td>
                                    <div class="date-info">
                                        @if ($request->activity?->created_at)
                                            <strong>
                                                {{ $request->activity->created_at->format('d M Y') }}
                                            </strong>

                                            <span>
                                                {{ $request->activity->created_at->format('H:i') }}
                                            </span>
                                        @else
                                            <strong>-</strong>
                                            <span>-</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="column-action">
                                    <button type="button" class="action-button" title="Aksi" aria-label="Aksi"
                                        data-id="{{ $request->id }}" data-status="{{ $request->status }}"
                                        onclick="openActionMenu(event, {{ $request->id }})">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    @if ($request->status === 'pending')
                                        <form id="delete-form-{{ $request->id }}"
                                            action="{{ route('barista.restock-requests.destroy', $request->id) }}"
                                            method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="restock-empty">
                                        <div class="restock-empty-icon">
                                            <i class="bi bi-clipboard-x"></i>
                                        </div>

                                        <h3>Belum Ada Pengajuan</h3>

                                        <p>
                                            Anda belum memiliki pengajuan restock.
                                        </p>

                                        <a href="{{ route('barista.restock-requests.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-lg"></i>
                                            <span>Ajukan Restock</span>
                                        </a>
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

    {{-- Global action menu --}}
    <div id="global-action-menu" class="global-action-menu" aria-hidden="true">
        <a id="global-action-detail" href="#" class="action-menu-item">
            <i class="bi bi-eye"></i>
            <span>Lihat Detail</span>
        </a>

        <a id="global-action-edit" href="#" class="action-menu-item">
            <i class="bi bi-pencil"></i>
            <span>Edit</span>
        </a>

        <button type="button" id="global-action-delete" class="action-menu-item action-menu-danger">
            <i class="bi bi-trash"></i>
            <span>Hapus</span>
        </button>
    </div>

    <script>
        const globalActionMenu = document.getElementById('global-action-menu');
        const globalActionDetail = document.getElementById('global-action-detail');
        const globalActionEdit = document.getElementById('global-action-edit');
        const globalActionDelete = document.getElementById('global-action-delete');

        function openActionMenu(event, id) {
            event.stopPropagation();

            const button = event.currentTarget;
            const status = button.dataset.status;
            const rect = button.getBoundingClientRect();

            globalActionDetail.href =
                "{{ url('/barista/restock-requests') }}/" + id;

            globalActionEdit.href =
                "{{ url('/barista/restock-requests') }}/" + id + "/edit";

            globalActionEdit.style.display =
                status === 'pending' ? 'flex' : 'none';

            globalActionDelete.style.display =
                status === 'pending' ? 'flex' : 'none';

            globalActionDelete.onclick = function() {
                confirmDelete(id);
            };

            globalActionMenu.classList.add('show');
            globalActionMenu.setAttribute('aria-hidden', 'false');

            const menuWidth = globalActionMenu.offsetWidth;
            const menuHeight = globalActionMenu.offsetHeight;

            let left = rect.right - menuWidth;
            let top = rect.bottom + 6;

            if (left < 8) {
                left = 8;
            }

            if (left + menuWidth > window.innerWidth - 8) {
                left = window.innerWidth - menuWidth - 8;
            }

            if (top + menuHeight > window.innerHeight - 8) {
                top = rect.top - menuHeight - 6;
            }

            if (top < 8) {
                top = 8;
            }

            globalActionMenu.style.left = `${left}px`;
            globalActionMenu.style.top = `${top}px`;
        }

        function closeActionMenu() {
            globalActionMenu.classList.remove('show');
            globalActionMenu.setAttribute('aria-hidden', 'true');
        }

        function confirmDelete(id) {
            closeActionMenu();

            const confirmed = confirm(
                'Hapus ajuan restock ini?\n\n' +
                'Ajuan hanya akan dinonaktifkan dan tidak dihapus permanen.'
            );

            if (confirmed) {
                const form = document.getElementById(`delete-form-${id}`);

                if (form) {
                    form.submit();
                }
            }
        }

        document.addEventListener('click', function(event) {
            if (
                !event.target.closest('.action-button') &&
                !event.target.closest('.global-action-menu')
            ) {
                closeActionMenu();
            }
        });

        window.addEventListener('scroll', closeActionMenu);
        window.addEventListener('resize', closeActionMenu);
    </script>
@endsection
