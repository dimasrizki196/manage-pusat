@extends('layouts.app')

@section('title', 'Master Kategori')

@section('content')

    <div class="admin-master-page">
        <div class="master-page-header">
            <div>
                <h1 class="master-page-title">Master Kategori</h1>
                <p class="master-page-subtitle">Kelola kategori yang digunakan pada barang.</p>
            </div>
            <button type="button" class="master-primary-button" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-lg"></i>
                Tambah Kategori
            </button>
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
                    <span>{{ $categories->total() }} kategori</span>
                </div>
            </div>

            <div class="master-table-wrapper">
                <table class="master-table">
                    <thead>
                        <tr>
                            <th width="70">No</th>
                            <th>Nama Kategori</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $category)
                            <tr>
                                <td>{{ $categories->firstItem() + $index }}</td>
                                <td>
                                    <span class="master-name">{{ $category->name }}</span>
                                </td>
                                <td>
                                    <div class="master-action-wrapper">
                                        <button type="button" class="master-action-button"
                                            onclick="toggleCategoryActionMenu({{ $category->id }})"
                                            aria-label="Aksi kategori">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>

                                        <div id="categoryActionMenu-{{ $category->id }}" class="master-action-menu">
                                            <button type="button"
                                                onclick="openEditCategoryModal({{ $category->id }}, @js($category->name))">
                                                <i class="bi bi-pencil"></i>
                                                Edit
                                            </button>

                                            <button type="button" class="danger"
                                                onclick="confirmDeleteCategory({{ $category->id }}, @js($category->name))">
                                                <i class="bi bi-trash3"></i>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="master-empty">
                                    <i class="bi bi-tags"></i>
                                    <span>Belum ada data kategori.</span>
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

    {{-- CREATE MODAL --}}

    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Tambah Kategori</h5>
                        <p>Tambahkan kategori baru.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

    
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf

                    <div class="modal-body">
                        <label for="create_category_name" class="master-form-label">
                            Nama Kategori
                        </label>

                        <input type="text" id="create_category_name" name="name" class="master-form-input"
                            placeholder="Contoh: Biji Kopi" value="{{ old('name') }}" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="master-secondary-button" data-bs-dismiss="modal">
                            Batal
                        </button>

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

        
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <label for="edit_category_name" class="master-form-label">
                            Nama Kategori
                        </label>

                        <input type="text" id="edit_category_name" name="name" class="master-form-input" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="master-secondary-button" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="master-primary-button">
                            <i class="bi bi-check2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- DELETE CONFIRMATION --}}

    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-confirm-modal">
                <div class="master-confirm-icon danger">
                    <i class="bi bi-trash3"></i>
                </div>

    
                <h5>Hapus Kategori?</h5>

                <p>
                    Anda yakin ingin menghapus
                    <strong id="deleteCategoryName"></strong>?
                </p>

                <div class="master-confirm-actions">
                    <button type="button" class="master-secondary-button" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <form id="deleteCategoryForm" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="master-danger-button">
                            <i class="bi bi-trash3"></i>
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const categoryUpdateUrl = @json(route('admin.categories.update', ['category' => '__CATEGORY_ID__']));

        const categoryDeleteUrl = @json(route('admin.categories.destroy', ['category' => '__CATEGORY_ID__']));

        function buildCategoryUrl(template, id) {
            return template.replace('__CATEGORY_ID__', id);
        }

        function closeCategoryActionMenus() {
            document.querySelectorAll('.master-action-menu').forEach(function(menu) {
                menu.classList.remove('show');
            });
        }

        function toggleCategoryActionMenu(id) {
            const menu = document.getElementById('categoryActionMenu-' + id);

            if (!menu) {
                return;
            }

            const isOpen = menu.classList.contains('show');

            closeCategoryActionMenus();

            if (!isOpen) {
                menu.classList.add('show');
            }
        }

        function openEditCategoryModal(id, name) {
            closeCategoryActionMenus();

            const form = document.getElementById('editCategoryForm');
            const input = document.getElementById('edit_category_name');

            if (!form || !input) {
                return;
            }

            form.action = buildCategoryUrl(categoryUpdateUrl, id);
            input.value = name;

            const modalElement = document.getElementById('editCategoryModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

            modal.show();
        }

        function confirmDeleteCategory(id, name) {
            closeCategoryActionMenus();

            const form = document.getElementById('deleteCategoryForm');
            const nameElement = document.getElementById('deleteCategoryName');

            if (!form || !nameElement) {
                return;
            }

            form.action = buildCategoryUrl(categoryDeleteUrl, id);
            nameElement.textContent = name;

            const modalElement = document.getElementById('deleteCategoryModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

            modal.show();
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.master-action-wrapper')) {
                closeCategoryActionMenus();
            }
        });
    </script>
@endpush
