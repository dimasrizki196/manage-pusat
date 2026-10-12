@extends('layouts.app')

@section('title', 'Master Kategori')

@push('styles')
    @vite('resources/css/admin/categories.css')
@endpush

@section('content')
    <div class="admin-master-page">
        <div class="master-page-header">
            <div class="master-page-header-content">
                <div class="master-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Master Kategori</span>
                </div>

                <h1 class="master-page-title">Master Kategori</h1>
                <p class="master-page-subtitle">Kelola kategori yang digunakan pada barang.</p>
            </div>

            <div class="master-page-header-actions">
                <button type="button" class="master-primary-button" data-bs-toggle="modal"
                    data-bs-target="#createCategoryModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Kategori</span>
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="master-alert master-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="master-alert master-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="master-alert master-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="master-card">
            <div class="master-card-header">
                <div>
                    <h2>Daftar Kategori</h2>
                    <p>Daftar kategori yang tersedia untuk barang.</p>
                </div>
                <span class="master-card-total">Total: {{ $categories->total() }} kategori</span>
            </div>

            <div class="master-table-wrapper">
                <table class="master-table">
                    <thead>
                        <tr>
                            <th width="70">No</th>
                            <th>Nama Kategori</th>
                            <th width="110">Status</th>
                            <th width="70">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            @php
                                $isActive = empty($category->activity_deleted_at);
                            @endphp
                            <tr>
                                <td>
                                    <span class="master-number">{{ $categories->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <span class="master-name">{{ $category->name }}</span>
                                </td>
                                <td>
                                    <span class="master-status {{ $isActive ? 'active' : 'inactive' }}">
                                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="master-action-button js-category-action"
                                        data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}"
                                        data-category-status="{{ $isActive ? 'active' : 'inactive' }}"
                                        aria-label="Aksi kategori" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="master-empty">
                                        <div class="master-empty-icon"><i class="bi bi-tags"></i></div>
                                        <strong>Belum ada data kategori</strong>
                                        <span>Tambahkan kategori untuk digunakan pada data barang.</span>
                                        <button type="button" class="master-primary-button master-empty-button"
                                            data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                                            <i class="bi bi-plus-lg"></i>
                                            Tambah Kategori
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="master-pagination">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>

    <div id="categoryActionMenu" class="master-global-action-menu" hidden>
        <button type="button" id="categoryActionEdit">
            <i class="bi bi-pencil"></i><span>Edit</span>
        </button>
        <button type="button" id="categoryActionDelete" class="danger">
            <i class="bi bi-trash3"></i><span>Nonaktifkan</span>
        </button>
        <button type="button" id="categoryActionRestore">
            <i class="bi bi-arrow-counterclockwise"></i><span>Aktifkan</span>
        </button>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Tambah Kategori</h5>
                        <p>Tambahkan kategori baru.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label for="create_category_name" class="master-form-label">Nama Kategori</label>
                        <input type="text" id="create_category_name" name="name" class="master-form-input"
                            placeholder="Contoh: Biji Kopi" value="{{ old('name') }}" required autocomplete="off">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="master-secondary-button" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="master-primary-button">
                            <i class="bi bi-check2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Edit Kategori</h5>
                        <p>Perbarui informasi kategori.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <label for="edit_category_name" class="master-form-label">Nama Kategori</label>
                        <input type="text" id="edit_category_name" name="name" class="master-form-input" required
                            autocomplete="off">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="master-secondary-button" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="master-primary-button">
                            <i class="bi bi-check2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Nonaktifkan --}}
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-confirm-modal">
                <div class="master-confirm-icon danger"><i class="bi bi-trash3"></i></div>
                <h5>Nonaktifkan Kategori?</h5>
                <p>Anda yakin ingin menonaktifkan kategori <strong id="deleteCategoryName"></strong>?</p>
                <div class="master-confirm-actions">
                    <button type="button" class="master-secondary-button" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteCategoryForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="master-danger-button">
                            <i class="bi bi-trash3"></i>Ya, Nonaktifkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Aktifkan --}}
    <div class="modal fade" id="restoreCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-confirm-modal">
                <div class="master-confirm-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
                <h5>Aktifkan Kategori?</h5>
                <p>Anda yakin ingin mengaktifkan kembali kategori <strong id="restoreCategoryName"></strong>?</p>
                <div class="master-confirm-actions">
                    <button type="button" class="master-secondary-button" data-bs-dismiss="modal">Batal</button>
                    <form id="restoreCategoryForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="master-primary-button">
                            <i class="bi bi-check2"></i>Ya, Aktifkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const actionMenu = document.getElementById('categoryActionMenu');
            const actionEdit = document.getElementById('categoryActionEdit');
            const actionDelete = document.getElementById('categoryActionDelete');
            const actionRestore = document.getElementById('categoryActionRestore');
            const editModalElement = document.getElementById('editCategoryModal');
            const deleteModalElement = document.getElementById('deleteCategoryModal');
            const restoreModalElement = document.getElementById('restoreCategoryModal');
            const editForm = document.getElementById('editCategoryForm');
            const editInput = document.getElementById('edit_category_name');
            const deleteForm = document.getElementById('deleteCategoryForm');
            const deleteName = document.getElementById('deleteCategoryName');
            const restoreForm = document.getElementById('restoreCategoryForm');
            const restoreName = document.getElementById('restoreCategoryName');
            const updateUrl = @json(route('admin.categories.update', ['category' => '__CATEGORY_ID__']));
            const deleteUrl = @json(route('admin.categories.destroy', ['category' => '__CATEGORY_ID__']));
            const restoreUrl = @json(route('admin.categories.restore', ['category' => '__CATEGORY_ID__']));
            let activeButton = null;
            let activeCategory = null;

            function buildUrl(template, id) {
                return template.replace('__CATEGORY_ID__', encodeURIComponent(id));
            }

            function closeActionMenu() {
                actionMenu.hidden = true;
                activeButton?.setAttribute('aria-expanded', 'false');
                activeButton = null;
                activeCategory = null;
            }

            function positionActionMenu(button) {
                actionMenu.hidden = false;
                const rect = button.getBoundingClientRect();
                const menuRect = actionMenu.getBoundingClientRect();
                const padding = 10;
                let top = rect.bottom + 6;
                let left = rect.right - menuRect.width;

                if (left < padding) left = padding;
                if (left + menuRect.width > window.innerWidth - padding) left = window.innerWidth - menuRect.width -
                    padding;
                if (top + menuRect.height > window.innerHeight - padding) top = rect.top - menuRect.height - 6;
                if (top < padding) top = padding;

                actionMenu.style.top = top + 'px';
                actionMenu.style.left = left + 'px';
            }

            function openActionMenu(button) {
                const id = button.dataset.categoryId;
                if (!id) return;

                if (activeButton === button && !actionMenu.hidden) {
                    closeActionMenu();
                    return;
                }

                closeActionMenu();
                activeButton = button;
                activeCategory = {
                    id: id,
                    name: button.dataset.categoryName || '',
                    status: button.dataset.categoryStatus || 'active'
                };

                actionEdit.hidden = activeCategory.status !== 'active';
                actionDelete.hidden = activeCategory.status !== 'active';
                actionRestore.hidden = activeCategory.status !== 'inactive';

                button.setAttribute('aria-expanded', 'true');
                positionActionMenu(button);
            }

            document.querySelectorAll('.js-category-action').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    openActionMenu(this);
                });
            });

            actionEdit.addEventListener('click', function() {
                if (!activeCategory) return;
                editForm.action = buildUrl(updateUrl, activeCategory.id);
                editInput.value = activeCategory.name;
                closeActionMenu();
                bootstrap.Modal.getOrCreateInstance(editModalElement).show();
                setTimeout(() => {
                    editInput.focus();
                    editInput.select();
                }, 300);
            });

            actionDelete.addEventListener('click', function() {
                if (!activeCategory) return;
                deleteForm.action = buildUrl(deleteUrl, activeCategory.id);
                deleteName.textContent = activeCategory.name;
                closeActionMenu();
                bootstrap.Modal.getOrCreateInstance(deleteModalElement).show();
            });

            actionRestore.addEventListener('click', function() {
                if (!activeCategory) return;
                restoreForm.action = buildUrl(restoreUrl, activeCategory.id);
                restoreName.textContent = activeCategory.name;
                closeActionMenu();
                bootstrap.Modal.getOrCreateInstance(restoreModalElement).show();
            });

            document.addEventListener('click', function(event) {
                if (!event.target.closest('.js-category-action') && !event.target.closest(
                        '#categoryActionMenu')) {
                    closeActionMenu();
                }
            });

            window.addEventListener('resize', function() {
                if (activeButton && !actionMenu.hidden) positionActionMenu(activeButton);
            });

            window.addEventListener('scroll', function() {
                if (activeButton && !actionMenu.hidden) positionActionMenu(activeButton);
            }, true);

            document.getElementById('createCategoryModal')?.addEventListener('shown.bs.modal', function() {
                document.getElementById('create_category_name')?.focus();
            });
        });
    </script>
@endpush
