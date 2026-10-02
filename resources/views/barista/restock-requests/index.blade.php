@extends('layouts.barista')

@section('title', 'Pengajuan Restock')

@push('styles')
    @vite('resources/css/barista/restock-requests.css')
@endpush

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-breadcrumb">
                <a href="{{ route('barista.dashboard') }}">
                    <i class="bi bi-grid-1x2"></i>
                    Dashboard
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
                Ajukan Restock
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <div class="restock-table-header">
                <div>
                    <h5 class="mb-1">Daftar Pengajuan</h5>

                    <p class="text-muted mb-0">
                        Riwayat pengajuan restock yang Anda buat.
                    </p>
                </div>

                <div class="restock-total">
                    Total:
                    <strong>{{ $restockRequests->total() }}</strong>
                    pengajuan
                </div>
            </div>

            <div class="table-responsive">
                <table class="table restock-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Barang</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Diajukan</th>
                            <th width="80">Aksi</th>
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
                                {{-- Nomor --}}
                                <td>
                                    <span class="row-number">
                                        {{ $restockRequests->firstItem() + $loop->index }}
                                    </span>
                                </td>

                                {{-- Barang --}}
                                <td>
                                    <div class="item-info">
                                        <div class="item-icon">
                                            <i class="bi bi-box-seam"></i>
                                        </div>

                                        <div>
                                            <strong>
                                                {{ $request->item?->name ?? '-' }}
                                            </strong>

                                            <small>
                                                {{ $request->item?->unit?->name ?? '-' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td>
                                    <span class="category-text">
                                        {{ $request->item?->category?->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Jumlah --}}
                                <td>
                                    <strong>{{ $quantity }}</strong>

                                    <span class="unit-text">
                                        {{ $request->item?->unit?->name ?? '' }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span class="restock-status {{ $statusClass }}">
                                        <i class="bi {{ $statusIcon }}"></i>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Tanggal --}}
                                <td>
                                    <div class="date-info">
                                        @if ($request->activity?->created_at)
                                            <strong>
                                                {{ $request->activity->created_at->format('d M Y') }}
                                            </strong>

                                            <small>
                                                {{ $request->activity->created_at->format('H:i') }}
                                            </small>
                                        @else
                                            <strong>-</strong>
                                            <small>-</small>
                                        @endif
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td>
                                    <div class="action-menu-wrapper">
                                        <button type="button" class="action-button action-menu-toggle" title="Aksi"
                                            aria-label="Aksi" onclick="toggleActionMenu({{ $request->id }})">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>

                                        <div class="action-menu" id="action-menu-{{ $request->id }}">

                                            {{-- Lihat Detail --}}
                                            <a href="{{ route('barista.restock-requests.show', $request->id) }}"
                                                class="action-menu-item">
                                                <i class="bi bi-eye"></i>
                                                <span>Lihat Detail</span>
                                            </a>

                                            {{-- Edit hanya Pending --}}
                                            @if ($request->status === 'pending')
                                                <a href="{{ route('barista.restock-requests.edit', $request->id) }}"
                                                    class="action-menu-item">
                                                    <i class="bi bi-pencil"></i>
                                                    <span>Edit</span>
                                                </a>

                                                {{-- Hapus hanya Pending --}}
                                                <button type="button" class="action-menu-item action-menu-danger"
                                                    onclick="confirmDelete({{ $request->id }})">
                                                    <i class="bi bi-trash"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Form delete --}}
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
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-clipboard-x"></i>
                                        </div>

                                        <h5>Belum Ada Pengajuan</h5>

                                        <p>
                                            Anda belum memiliki pengajuan restock.
                                        </p>

                                        <a href="{{ route('barista.restock-requests.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-lg"></i>
                                            Ajukan Restock
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($restockRequests->hasPages())
                <div class="restock-pagination">
                    {{ $restockRequests->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleActionMenu(id) {
            const currentMenu = document.getElementById(`action-menu-${id}`);

            document.querySelectorAll('.action-menu').forEach(menu => {
                if (menu !== currentMenu) {
                    menu.classList.remove('show');
                }
            });

            currentMenu.classList.toggle('show');
        }

        function confirmDelete(id) {
            document.querySelectorAll('.action-menu').forEach(menu => {
                menu.classList.remove('show');
            });

            const confirmed = confirm(
                'Hapus ajuan restock ini?\n\n' +
                'Ajuan hanya akan dinonaktifkan dan tidak dihapus permanen.'
            );

            if (confirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.action-menu-wrapper')) {
                document.querySelectorAll('.action-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    </script>
@endsection
