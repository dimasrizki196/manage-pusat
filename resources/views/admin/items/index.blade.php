@extends('layouts.app')

@section('title', 'Master Barang')

@push('styles')
    @vite('resources/css/admin/items.css')
@endpush

@section('content')
    <div class="admin-items-page">
        <div class="items-page-header">
            <div class="items-page-header-content">
                <div class="items-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Master Barang</span>
                </div>

                <h1 class="items-page-title">Master Barang</h1>

                <p class="items-page-subtitle">
                    Kelola data barang, kategori, satuan, dan batas minimum stok.
                </p>
            </div>

            <div class="items-page-header-actions">
                <button type="button" class="items-add-button" onclick="openItemCreateModal()">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Barang</span>
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="items-alert items-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="items-alert items-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="items-alert items-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>

                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- FILTER --}}
        <div class="items-filter-card">
            <form method="GET" action="{{ route('admin.items.index') }}" class="items-filter-form">
                <div class="items-search">
                    <i class="bi bi-search"></i>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama barang, kategori, atau satuan">
                </div>

                <div class="items-filter-select">
                    <select name="category_id">
                        <option value="">Semua kategori</option>

                        @foreach ($categories as $category)
                            @if (!$category->activity || !$category->activity->deleted_at)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="items-filter-select">
                    <select name="status">
                        <option value="">Semua status</option>

                        <option value="active" @selected(request('status') === 'active')>
                            Aktif
                        </option>

                        <option value="inactive" @selected(request('status') === 'inactive')>
                            Nonaktif
                        </option>
                    </select>
                </div>

                <button type="submit" class="items-filter-button">
                    <i class="bi bi-funnel"></i>
                    <span>Filter</span>
                </button>

                @if (request()->hasAny(['search', 'category_id', 'status']))
                    <a href="{{ route('admin.items.index') }}" class="items-filter-reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </div>

        {{-- TABLE --}}
        <div class="items-card">
            <div class="items-card-header">
                <div>
                    <h2>Daftar Barang</h2>

                    <p>
                        Data barang yang tersedia pada sistem.
                    </p>
                </div>

                <span class="items-card-total">
                    Total: {{ $items->total() }} barang
                </span>
            </div>

            <div class="items-table-wrapper">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th width="55">No</th>
                            <th>Barang</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th>Min. Stok</th>
                            <th>Status</th>
                            <th width="70">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($items as $index => $item)
                            @php
                                $isInactive = $item->activity?->deleted_at !== null;
                                $isLowStock = !$isInactive && $item->stock <= $item->stock_min;

                                $stock = rtrim(rtrim(number_format($item->stock, 2, ',', '.'), '0'), ',');

                                $stockMin = rtrim(rtrim(number_format($item->stock_min, 2, ',', '.'), '0'), ',');
                            @endphp

                            <tr class="{{ $isInactive ? 'item-row-inactive' : '' }}">
                                <td>
                                    <span class="items-number">
                                        {{ $items->firstItem() + $index }}
                                    </span>
                                </td>

                                <td>
                                    <div class="item-name-wrapper">
                                        <div class="item-icon">
                                            <i class="bi bi-box-seam"></i>
                                        </div>

                                        <div class="item-name-content">
                                            <strong>{{ $item->name }}</strong>

                                            @if ($isLowStock)
                                                <small class="item-low-stock">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                    Stok menipis
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="item-category">
                                        {{ $item->category?->name ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $item->unit?->name ?? '-' }}
                                </td>

                                <td>
                                    <strong class="{{ $isLowStock ? 'stock-low' : '' }}">
                                        {{ $stock }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $stockMin }}
                                </td>

                                <td>
                                    @if ($isInactive)
                                        <span class="item-status item-status-inactive">
                                            <i class="bi bi-dash-circle"></i>
                                            Nonaktif
                                        </span>
                                    @else
                                        <span class="item-status item-status-active">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Aktif
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <button type="button" class="item-action-button js-item-action"
                                        data-item-id="{{ $item->id }}" data-item-name="{{ $item->name }}"
                                        data-item-category="{{ $item->category_id }}"
                                        data-item-unit="{{ $item->unit_id }}" data-item-stock-min="{{ $item->stock_min }}"
                                        data-item-inactive="{{ $isInactive ? '1' : '0' }}" aria-label="Aksi barang"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="items-empty">
                                        <div class="items-empty-icon">
                                            <i class="bi bi-box-seam"></i>
                                        </div>

                                        <h6>Belum ada barang</h6>

                                        <p>
                                            Tidak ada data barang yang sesuai dengan filter.
                                        </p>

                                        <button type="button" class="items-empty-button" onclick="openItemCreateModal()">
                                            <i class="bi bi-plus-lg"></i>
                                            Tambah Barang
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="items-pagination">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- GLOBAL ACTION MENU --}}
    <div id="itemActionMenu" class="items-global-action-menu" hidden>
        <a id="itemActionShow" href="#">
            <i class="bi bi-eye"></i>
            <span>Lihat</span>
        </a>

        <button type="button" id="itemActionEdit">
            <i class="bi bi-pencil"></i>
            <span>Edit</span>
        </button>

        <button type="button" id="itemActionDeactivate" class="warning">
            <i class="bi bi-slash-circle"></i>
            <span>Nonaktifkan</span>
        </button>

        <button type="button" id="itemActionActivate" class="success">
            <i class="bi bi-check-circle"></i>
            <span>Aktifkan</span>
        </button>

        <button type="button" id="itemActionForceDelete" class="danger">
            <i class="bi bi-trash3"></i>
            <span>Hapus Permanen</span>
        </button>
    </div>

    {{-- ITEM FORM MODAL --}}
    <div class="modal fade" id="itemFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content items-modal">
                <div class="modal-header">
                    <div>
                        <h5 id="itemFormModalTitle">Tambah Barang</h5>
                        <p>Isi informasi barang dengan benar.</p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <form id="itemForm" method="POST" action="{{ route('admin.items.store') }}">
                    @csrf

                    <input type="hidden" name="_method" id="itemFormMethod" value="POST">

                    <div class="modal-body">
                        <div class="items-form-group">
                            <label for="itemName">
                                Nama Barang
                            </label>

                            <input type="text" id="itemName" name="name" class="form-control"
                                placeholder="Contoh: Arabica Gayo" required>
                        </div>

                        <div class="items-form-row">
                            <div class="items-form-group">
                                <label for="itemCategory">
                                    Kategori
                                </label>

                                <select id="itemCategory" name="category_id" class="form-select" required>
                                    <option value="">
                                        Pilih kategori
                                    </option>

                                    @foreach ($categories as $category)
                                        @if (!$category->activity || !$category->activity->deleted_at)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="items-form-group">
                                <label for="itemUnit">
                                    Satuan
                                </label>

                                <select id="itemUnit" name="unit_id" class="form-select" required>
                                    <option value="">
                                        Pilih satuan
                                    </option>

                                    @foreach ($units as $unit)
                                        @if (!$unit->activity || !$unit->activity->deleted_at)
                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="items-form-group">
                            <label for="itemStockMin">
                                Stok Minimum
                            </label>

                            <input type="number" id="itemStockMin" name="stock_min" class="form-control"
                                min="0" step="0.01" value="0" required>

                            <small>
                                Stok awal barang otomatis dimulai dari 0.
                            </small>
                        </div>

                        <div class="items-stock-info">
                            <i class="bi bi-info-circle"></i>

                            <span>
                                Perubahan stok dilakukan melalui transaksi
                                barang masuk dan barang keluar.
                            </span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary" id="itemFormSubmit">
                            Simpan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemFormModalElement = document.getElementById('itemFormModal');

            const itemFormModal = itemFormModalElement ?
                bootstrap.Modal.getOrCreateInstance(itemFormModalElement) :
                null;

            const actionMenu = document.getElementById('itemActionMenu');
            const actionShow = document.getElementById('itemActionShow');
            const actionEdit = document.getElementById('itemActionEdit');
            const actionDeactivate = document.getElementById('itemActionDeactivate');
            const actionActivate = document.getElementById('itemActionActivate');
            const actionForceDelete = document.getElementById('itemActionForceDelete');

            const itemForm = document.getElementById('itemForm');
            const itemFormMethod = document.getElementById('itemFormMethod');
            const itemFormTitle = document.getElementById('itemFormModalTitle');
            const itemFormSubmit = document.getElementById('itemFormSubmit');

            const itemName = document.getElementById('itemName');
            const itemCategory = document.getElementById('itemCategory');
            const itemUnit = document.getElementById('itemUnit');
            const itemStockMin = document.getElementById('itemStockMin');

            const itemUpdateUrl = @json(route('admin.items.update', ['item' => '__ITEM_ID__']));

            const itemShowUrl = @json(route('admin.items.show', ['item' => '__ITEM_ID__']));

            const itemDeleteUrl = @json(route('admin.items.destroy', ['item' => '__ITEM_ID__']));

            const itemRestoreUrl = @json(route('admin.items.restore', ['item' => '__ITEM_ID__']));

            const itemForceDeleteUrl = @json(route('admin.items.forceDelete', ['item' => '__ITEM_ID__']));

            let activeButton = null;
            let activeItem = null;

            function buildItemUrl(template, id) {
                return template.replace('__ITEM_ID__', id);
            }

            function closeItemActionMenu() {
                if (!actionMenu) {
                    return;
                }

                actionMenu.hidden = true;

                if (activeButton) {
                    activeButton.setAttribute('aria-expanded', 'false');
                }

                activeButton = null;
                activeItem = null;
            }

            function positionItemActionMenu(button) {
                if (!actionMenu || !button) {
                    return;
                }

                actionMenu.hidden = false;

                const buttonRect = button.getBoundingClientRect();
                const menuRect = actionMenu.getBoundingClientRect();

                const padding = 10;

                let top = buttonRect.bottom + 6;
                let left = buttonRect.right - menuRect.width;

                if (left < padding) {
                    left = padding;
                }

                if (left + menuRect.width > window.innerWidth - padding) {
                    left = window.innerWidth - menuRect.width - padding;
                }

                if (top + menuRect.height > window.innerHeight - padding) {
                    top = buttonRect.top - menuRect.height - 6;
                }

                if (top < padding) {
                    top = padding;
                }

                actionMenu.style.top = `${top}px`;
                actionMenu.style.left = `${left}px`;
            }

            function openItemActionMenu(button) {
                const id = button.dataset.itemId;

                if (!id) {
                    return;
                }

                if (
                    activeButton === button &&
                    !actionMenu.hidden
                ) {
                    closeItemActionMenu();
                    return;
                }

                closeItemActionMenu();

                activeButton = button;

                activeItem = {
                    id: id,
                    name: button.dataset.itemName || '',
                    categoryId: button.dataset.itemCategory || '',
                    unitId: button.dataset.itemUnit || '',
                    stockMin: button.dataset.itemStockMin || '0',
                    inactive: button.dataset.itemInactive === '1'
                };

                button.setAttribute('aria-expanded', 'true');

                actionShow.href = buildItemUrl(
                    itemShowUrl,
                    activeItem.id
                );

                actionEdit.hidden = activeItem.inactive;
                actionDeactivate.hidden = activeItem.inactive;
                actionActivate.hidden = !activeItem.inactive;

                positionItemActionMenu(button);
            }

            document
                .querySelectorAll('.js-item-action')
                .forEach(function(button) {
                    button.addEventListener('click', function(event) {
                        event.stopPropagation();

                        openItemActionMenu(this);
                    });
                });

            window.openItemCreateModal = function() {
                if (!itemForm || !itemFormModal) {
                    return;
                }

                closeItemActionMenu();

                itemForm.action = "{{ route('admin.items.store') }}";

                itemFormMethod.value = 'POST';
                itemFormTitle.textContent = 'Tambah Barang';
                itemFormSubmit.textContent = 'Simpan Barang';

                itemName.value = '';
                itemCategory.value = '';
                itemUnit.value = '';
                itemStockMin.value = '0';

                itemFormModal.show();
            };

            function openItemEditModal() {
                if (!activeItem || !itemFormModal) {
                    return;
                }

                itemForm.action = buildItemUrl(
                    itemUpdateUrl,
                    activeItem.id
                );

                itemFormMethod.value = 'PUT';
                itemFormTitle.textContent = 'Edit Barang';
                itemFormSubmit.textContent = 'Simpan Perubahan';

                itemName.value = activeItem.name;
                itemCategory.value = activeItem.categoryId;
                itemUnit.value = activeItem.unitId;
                itemStockMin.value = activeItem.stockMin;

                closeItemActionMenu();

                itemFormModal.show();
            }

            actionEdit?.addEventListener('click', function() {
                openItemEditModal();
            });

            actionDeactivate?.addEventListener('click', function() {
                if (!activeItem) {
                    return;
                }

                const item = activeItem;

                closeItemActionMenu();

                openConfirmation({
                    title: 'Nonaktifkan Barang',
                    message: `Apakah Anda yakin ingin menonaktifkan ${item.name}? ` +
                        'Barang masih dapat diaktifkan kembali nanti.',
                    action: buildItemUrl(itemDeleteUrl, item.id),
                    method: 'DELETE',
                    type: 'warning',
                    submitText: 'Ya, Nonaktifkan',
                    icon: 'bi-slash-circle'
                });
            });

            actionActivate?.addEventListener('click', function() {
                if (!activeItem) {
                    return;
                }

                const item = activeItem;

                closeItemActionMenu();

                openConfirmation({
                    title: 'Aktifkan Barang',
                    message: `Apakah Anda yakin ingin mengaktifkan kembali ${item.name}?`,
                    action: buildItemUrl(itemRestoreUrl, item.id),
                    method: 'PATCH',
                    type: 'success',
                    submitText: 'Ya, Aktifkan',
                    icon: 'bi-check-circle'
                });
            });

            actionForceDelete?.addEventListener('click', function() {
                if (!activeItem) {
                    return;
                }

                const item = activeItem;

                closeItemActionMenu();

                openConfirmation({
                    title: 'Hapus Barang Permanen',
                    message: `Apakah Anda yakin ingin menghapus ${item.name} secara permanen? ` +
                        'Data yang dihapus tidak dapat dikembalikan.',
                    action: buildItemUrl(itemForceDeleteUrl, item.id),
                    method: 'DELETE',
                    type: 'danger',
                    submitText: 'Ya, Hapus Permanen',
                    icon: 'bi-trash3'
                });
            });

            document.addEventListener('click', function(event) {
                if (
                    !event.target.closest('.js-item-action') &&
                    !event.target.closest('#itemActionMenu')
                ) {
                    closeItemActionMenu();
                }
            });

            window.addEventListener('resize', function() {
                if (activeButton && !actionMenu.hidden) {
                    positionItemActionMenu(activeButton);
                }
            });

            window.addEventListener('scroll', function() {
                if (activeButton && !actionMenu.hidden) {
                    positionItemActionMenu(activeButton);
                }
            }, true);
        });
    </script>
@endpush
