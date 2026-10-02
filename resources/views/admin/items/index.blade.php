@extends('layouts.app')

@section('title', 'Master Barang')

@section('content')
    <div class="items-page">
        <div class="items-page-header">
            <div>
                <h1 class="items-page-title">Master Barang</h1>
                <p class="items-page-subtitle">Kelola data barang, kategori, satuan, dan batas minimum stok.</p>
            </div>
            <button type="button" class="items-add-button" onclick="openItemCreateModal()">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Barang</span>
            </button>
        </div>

        @if (session('success'))
            <div class="items-alert items-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="items-alert items-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

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
                        <option value="active" @selected(request('status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" class="items-filter-button">
                    <i class="bi bi-funnel"></i>
                    <span>Filter</span>
                </button>
                @if (request()->hasAny(['search', 'category_id', 'status']))
                    <a href="{{ route('admin.items.index') }}" class="items-filter-reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="items-card">
            <div class="items-card-header">
                <div>
                    <h5>Daftar Barang</h5>
                    <span>{{ $items->total() }} barang</span>
                </div>
            </div>

            <div class="items-table-wrapper">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th width="55">NO</th>
                            <th>BARANG</th>
                            <th>KATEGORI</th>
                            <th>SATUAN</th>
                            <th>STOK</th>
                            <th>MIN. STOK</th>
                            <th>STATUS</th>
                            <th width="65">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $index => $item)
                            @php
                                $isInactive = $item->activity && $item->activity->deleted_at !== null;
                                $isLowStock = !$isInactive && $item->stock <= $item->stock_min;
                                $stock = rtrim(rtrim(number_format($item->stock, 2, ',', '.'), '0'), ',');
                                $stockMin = rtrim(rtrim(number_format($item->stock_min, 2, ',', '.'), '0'), ',');
                            @endphp
                            <tr class="{{ $isInactive ? 'item-row-inactive' : '' }}">
                                <td>{{ $items->firstItem() + $index }}</td>
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
                                    <span class="item-category">{{ $item->category?->name ?? '-' }}</span>
                                </td>
                                <td>{{ $item->unit?->name ?? '-' }}</td>
                                <td>
                                    <strong class="{{ $isLowStock ? 'stock-low' : '' }}">{{ $stock }}</strong>
                                </td>
                                <td>{{ $stockMin }}</td>
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
                                    <div class="item-action-wrapper">
                                        <button type="button" class="item-action-button"
                                            onclick="openItemActionMenu(this)">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <div class="item-action-menu">
                                            <a href="{{ route('admin.items.show', $item->id) }}">
                                                <i class="bi bi-eye"></i>
                                                Lihat
                                            </a>
                                            @if (!$isInactive)
                                                <button type="button"
                                                    onclick="openItemEditModal({{ $item->id }}, {{ Js::from($item->name) }}, {{ $item->category_id }}, {{ $item->unit_id }}, {{ $item->stock_min }})">
                                                    <i class="bi bi-pencil"></i>
                                                    Edit
                                                </button>
                                                <button type="button" class="action-warning"
                                                    onclick="confirmItemAction('{{ route('admin.items.destroy', $item->id) }}', {{ Js::from($item->name) }}, 'nonaktifkan')">
                                                    <i class="bi bi-slash-circle"></i>
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button type="button" class="action-success"
                                                    onclick="confirmItemAction('{{ route('admin.items.restore', $item->id) }}', {{ Js::from($item->name) }}, 'aktifkan')">
                                                    <i class="bi bi-check-circle"></i>
                                                    Aktifkan
                                                </button>
                                            @endif
                                            <button type="button" class="action-danger"
                                                onclick="confirmItemAction('{{ route('admin.items.forceDelete', $item->id) }}', {{ Js::from($item->name) }}, 'hapus permanen')">
                                                <i class="bi bi-trash3"></i>
                                                Hapus Permanen
                                            </button>
                                        </div>
                                    </div>
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
                                        <p>Tidak ada data barang yang sesuai dengan filter.</p>
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

    <div class="modal fade" id="itemFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content items-modal">
                <div class="modal-header">
                    <div>
                        <h5 id="itemFormModalTitle">Tambah Barang</h5>
                        <p>Isi informasi barang dengan benar.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="itemForm" method="POST" action="{{ route('admin.items.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="itemFormMethod" value="POST">
                    <div class="modal-body">
                        <div class="items-form-group">
                            <label for="itemName">Nama Barang</label>
                            <input type="text" id="itemName" name="name" class="form-control"
                                placeholder="Contoh: Arabica Gayo" required>
                        </div>
                        <div class="items-form-row">
                            <div class="items-form-group">
                                <label for="itemCategory">Kategori</label>
                                <select id="itemCategory" name="category_id" class="form-select" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categories as $category)
                                        @if (!$category->activity || !$category->activity->deleted_at)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="items-form-group">
                                <label for="itemUnit">Satuan</label>
                                <select id="itemUnit" name="unit_id" class="form-select" required>
                                    <option value="">Pilih satuan</option>
                                    @foreach ($units as $unit)
                                        @if (!$unit->activity || !$unit->activity->deleted_at)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="items-form-group">
                            <label for="itemStockMin">Stok Minimum</label>
                            <input type="number" id="itemStockMin" name="stock_min" class="form-control"
                                min="0" step="0.01" value="0" required>
                            <small>Stok awal barang otomatis dimulai dari 0.</small>
                        </div>
                        <div class="items-stock-info">
                            <i class="bi bi-info-circle"></i>
                            <span>Perubahan stok dilakukan melalui transaksi barang masuk dan barang keluar.</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="itemFormSubmit">Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const itemFormModal = new bootstrap.Modal(document.getElementById('itemFormModal'));

        function openItemCreateModal() {
            const form = document.getElementById('itemForm');
            form.action = "{{ route('admin.items.store') }}";
            document.getElementById('itemFormMethod').value = 'POST';
            document.getElementById('itemFormModalTitle').textContent = 'Tambah Barang';
            document.getElementById('itemFormSubmit').textContent = 'Simpan Barang';
            document.getElementById('itemName').value = '';
            document.getElementById('itemCategory').value = '';
            document.getElementById('itemUnit').value = '';
            document.getElementById('itemStockMin').value = '0';
            itemFormModal.show();
        }

        function openItemEditModal(id, name, categoryId, unitId, stockMin) {
            const form = document.getElementById('itemForm');
            form.action = "{{ url('admin/items') }}/" + id;
            document.getElementById('itemFormMethod').value = 'PUT';
            document.getElementById('itemFormModalTitle').textContent = 'Edit Barang';
            document.getElementById('itemFormSubmit').textContent = 'Simpan Perubahan';
            document.getElementById('itemName').value = name;
            document.getElementById('itemCategory').value = categoryId;
            document.getElementById('itemUnit').value = unitId;
            document.getElementById('itemStockMin').value = stockMin;
            itemFormModal.show();
        }

        function openItemActionMenu(button) {
            document.querySelectorAll('.item-action-menu.show').forEach(menu => {
                if (menu !== button.nextElementSibling) menu.classList.remove('show');
            });
            const menu = button.nextElementSibling;
            menu.classList.toggle('show');
            if (menu.classList.contains('show')) {
                const rect = button.getBoundingClientRect();
                menu.style.top = (rect.bottom + window.scrollY + 6) + 'px';
                menu.style.left = (rect.right + window.scrollX - menu.offsetWidth) + 'px';
            }
        }

        function confirmItemAction(url, itemName, action) {
            if (action === 'nonaktifkan') {
                openConfirmation({
                    title: 'Nonaktifkan Barang',
                    message: `Apakah Anda yakin ingin menonaktifkan ${itemName}? Barang masih dapat diaktifkan kembali nanti.`,
                    action: url,
                    method: 'DELETE',
                    type: 'warning',
                    submitText: 'Ya, Nonaktifkan',
                    icon: 'bi-slash-circle'
                });
            } else if (action === 'aktifkan') {
                openConfirmation({
                    title: 'Aktifkan Barang',
                    message: `Apakah Anda yakin ingin mengaktifkan kembali ${itemName}?`,
                    action: url,
                    method: 'PATCH',
                    type: 'success',
                    submitText: 'Ya, Aktifkan',
                    icon: 'bi-check-circle'
                });
            } else if (action === 'hapus permanen') {
                openConfirmation({
                    title: 'Hapus Barang Permanen',
                    message: `Apakah Anda yakin ingin menghapus ${itemName} secara permanen? Data yang dihapus tidak dapat dikembalikan.`,
                    action: url,
                    method: 'DELETE',
                    type: 'danger',
                    submitText: 'Ya, Hapus Permanen',
                    icon: 'bi-trash3'
                });
            }
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.item-action-wrapper')) {
                document.querySelectorAll('.item-action-menu.show').forEach(menu => menu.classList.remove('show'));
            }
        });

        window.addEventListener('scroll', function() {
            document.querySelectorAll('.item-action-menu.show').forEach(menu => menu.classList.remove('show'));
        }, true);
    </script>
@endpush
