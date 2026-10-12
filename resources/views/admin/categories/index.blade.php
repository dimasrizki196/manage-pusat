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
                            <th width="130">Status</th>
                            <th width="70">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            <tr class="{{ $category->trashed() ? 'master-row-inactive' : '' }}">
                                <td>
                                    <span class="master-number">{{ $categories->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <span class="master-name">{{ $category->name }}</span>
                                </td>
                                <td>
                                    @if ($category->trashed())
                                        <span class="master-status master-status-inactive">
                                            <i class="bi bi-pause-circle"></i>
                                            Nonaktif
                                        </span>
                                    @else
                                        <span class="master-status master-status-active">
                                            <i class="bi bi-check-circle"></i>
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="master-action-button js-category-action"
                                        data-category-id="{{ $category->id }}"
                                        data-category-status="{{ $category->trashed() ? 'inactive' : 'active' }}"
                                        aria-label="Aksi kategori {{ $category->name }}" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="master-empty">
                                        <div class="master-empty-icon">
                                            <i class="bi bi-tags"></i>
                                        </div>
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
                <div class="master-pagination">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- GLOBAL ACTION MENU --}}
    <div id="categoryActionMenu" class="master-global-action-menu" hidden>
        <button type="button" id="categoryActionEdit">
            <i class="bi bi-pencil"></i>
            <span>Edit</span>
        </button>
        <button type="button" id="categoryActionDeactivate" class="danger">
            <i class="bi bi-pause-circle"></i>
            <span>Nonaktifkan</span>
        </button>
        <button type="button" id="categoryActionRestore">
            <i class="bi bi-arrow-counterclockwise"></i>
            <span>Aktifkan kembali</span>
        </button>
    </div>

    {{-- CREATE MODAL --}}
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
                            <i class="bi bi-check2"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- EDIT MODAL --}}
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
                            <i class="bi bi-check2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DEACTIVATE CONFIRMATION --}}
    <div class="modal fade" id="deactivateCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-confirm-modal">
                <div class="master-confirm-icon danger">
                    <i class="bi bi-pause-circle"></i>
                </div>
                <h5>Nonaktifkan Kategori?</h5>
                <p>
                    Kategori <strong id="deactivateCategoryName"></strong> akan dinonaktifkan.
                    Data tidak dihapus permanen dan masih dapat diaktifkan kembali.
                </p>
                <div class="master-confirm-actions">
                    <button type="button" class="master-secondary-button" data-bs-dismiss="modal">Batal</button>
                    <form id="deactivateCategoryForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="master-danger-button">
                            <i class="bi bi-pause-circle"></i>
                            Ya, Nonaktifkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- RESTORE CONFIRMATION --}}
    <div class="modal fade" id="restoreCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-confirm-modal">
                <div class="master-confirm-icon">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </div>
                <h5>Aktifkan Kembali Kategori?</h5>
                <p>
                    Anda yakin ingin mengaktifkan kembali kategori
                    <strong id="restoreCategoryName"></strong>?
                </p>
                <div class="master-confirm-actions">
                    <button type="button" class="master-secondary-button" data-bs-dismiss="modal">Batal</button>
                    <form id="restoreCategoryForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="master-primary-button">
                            <i class="bi bi-check2-circle"></i>
                            Ya, Aktifkan
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
            const actionDeactivate = document.getElementById('categoryActionDeactivate');
            const actionRestore = document.getElementById('categoryActionRestore');

            const editModalElement = document.getElementById('editCategoryModal');
            const deactivateModalElement = document.getElementById('deactivateCategoryModal');
            const restoreModalElement = document.getElementById('restoreCategoryModal');

            const editForm = document.getElementById('editCategoryForm');
            const editInput = document.getElementById('edit_category_name');
            const deactivateForm = document.getElementById('deactivateCategoryForm');
            const deactivateName = document.getElementById('deactivateCategoryName');
            const restoreForm = document.getElementById('restoreCategoryForm');
            const restoreName = document.getElementById('restoreCategoryName');

            const categoryUpdateUrl = @json(route('admin.categories.update', ['category' => '__CATEGORY_ID__']));
            const categoryDeactivateUrl = @json(route('admin.categories.destroy', ['category' => '__CATEGORY_ID__']));
            const categoryRestoreUrl = @json(route('admin.categories.restore', ['id' => '__CATEGORY_ID__']));

            let activeButton = null;
            let activeCategory = null;

            function buildCategoryUrl(template, id) {
                return template.replace('__CATEGORY_ID__', encodeURIComponent(id));
            }

            function closeActionMenu() {
                if (!actionMenu) return;

                actionMenu.hidden = true;

                if (activeButton) {
                    activeButton.setAttribute('aria-expanded', 'false');
                }

                activeButton = null;
                activeCategory = null;
            }

            function positionActionMenu(button) {
                if (!actionMenu || !button) return;

                actionMenu.hidden = false;

                const buttonRect = button.getBoundingClientRect();
                const menuRect = actionMenu.getBoundingClientRect();
                const padding = 10;

                let top = buttonRect.bottom + 6;
                let left = buttonRect.right - menuRect.width;

                left = Math.max(padding, Math.min(left, window.innerWidth - menuRect.width - padding));

                if (top + menuRect.height > window.innerHeight - padding) {
                    top = buttonRect.top - menuRect.height - 6;
                }

                top = Math.max(padding, top);

                actionMenu.style.top = `${top}px`;
                actionMenu.style.left = `${left}px`;
            }

            function openActionMenu(button) {
                const categoryId = button.dataset.categoryId;

                if (!categoryId) return;

                if (activeButton === button && !actionMenu.hidden) {
                    closeActionMenu();
                    return;
                }

                closeActionMenu();

                activeButton = button;
                activeCategory = {
                    id: categoryId,
                    name: button.closest('tr')?.querySelector('.master-name')?.textContent.trim() || '',
                    status: button.dataset.categoryStatus
                };

                actionEdit.hidden = activeCategory.status === 'inactive';
                actionDeactivate.hidden = activeCategory.status === 'inactive';
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
                if (!activeCategory || !editForm || !editInput) return;

                editForm.action = buildCategoryUrl(categoryUpdateUrl, activeCategory.id);
                editInput.value = activeCategory.name;

                closeActionMenu();
                bootstrap.Modal.getOrCreateInstance(editModalElement).show();

                setTimeout(function() {
                    editInput.focus();
                    editInput.select();
                }, 300);
            });

            actionDeactivate.addEventListener('click', function() {
                if (!activeCategory || !deactivateForm || !deactivateName) return;

                deactivateForm.action = buildCategoryUrl(categoryDeactivateUrl, activeCategory.id);
                deactivateName.textContent = activeCategory.name;

                closeActionMenu();
                bootstrap.Modal.getOrCreateInstance(deactivateModalElement).show();
            });

            actionRestore.addEventListener('click', function() {
                if (!activeCategory || !restoreForm || !restoreName) return;

                restoreForm.action = buildCategoryUrl(categoryRestoreUrl, activeCategory.id);
                restoreName.textContent = activeCategory.name;

                closeActionMenu();
                bootstrap.Modal.getOrCreateInstance(restoreModalElement).show();
            });

            document.addEventListener('click', function(event) {
                if (!event.target.closest('.js-category-action') &&
                    !event.target.closest('#categoryActionMenu')) {
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
