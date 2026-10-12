@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Kelola Kategori</h4>
                <p class="text-muted mb-0">Kelola kategori barang pada stok toko.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-semibold mb-1">Periksa kembali data kategori.</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="categorySearch" class="form-control"
                                placeholder="Cari nama kategori...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover" id="categoryTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">No.</th>
                                <th>Nama Kategori</th>
                                <th style="width: 150px;">Status</th>
                                <th class="text-end" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                @php
                                    $isInactive = !is_null($category->activity_deleted_at);
                                @endphp
                                <tr>
                                    <td>{{ $categories->firstItem() + $loop->index }}</td>
                                    <td class="category-name fw-medium">
                                        {{ $category->name }}
                                    </td>
                                    <td>
                                        @if ($isInactive)
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary">
                                                Nonaktif
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-success-subtle text-success">
                                                Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                @if (!$isInactive)
                                                    <li>
                                                        <button type="button" class="dropdown-item js-edit-category"
                                                            data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item text-danger js-delete-category"
                                                            data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                                            <i class="bi bi-archive me-2"></i>Nonaktifkan
                                                        </button>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form
                                                            action="{{ route('admin.categories.restore', $category->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Aktifkan kembali kategori {{ addslashes($category->name) }}?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="bi bi-arrow-counterclockwise me-2"></i>
                                                                Aktifkan kembali
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        Belum ada data kategori.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $categories->firstItem() ?? 0 }}–{{ $categories->lastItem() ?? 0 }}
                        dari {{ $categories->total() }} kategori
                    </small>
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal tambah kategori --}}
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="createCategoryName" class="form-label">Nama kategori</label>
                        <input type="text" name="name" id="createCategoryName" class="form-control" maxlength="100"
                            placeholder="Contoh: Biji Kopi" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal edit kategori --}}
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="editCategoryName" class="form-label">Nama kategori</label>
                        <input type="text" name="name" id="editCategoryName" class="form-control" maxlength="100"
                            required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal konfirmasi nonaktifkan --}}
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="deleteCategoryForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body text-center p-4">
                        <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex
                        align-items-center justify-content-center mb-3"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-archive fs-3"></i>
                        </div>
                        <h5 class="fw-bold">Nonaktifkan Kategori?</h5>
                        <p class="text-muted mb-0">
                            Kategori <strong id="deleteCategoryName"></strong> akan dinonaktifkan.
                            Kamu dapat mengaktifkannya kembali nanti.
                        </p>
                    </div>
                    <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Nonaktifkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editCategoryModal');
            const deleteModal = document.getElementById('deleteCategoryModal');

            const editForm = document.getElementById('editCategoryForm');
            const editName = document.getElementById('editCategoryName');
            const deleteForm = document.getElementById('deleteCategoryForm');
            const deleteName = document.getElementById('deleteCategoryName');

            const updateUrl = @json(route('admin.categories.update', ['category' => '__ID__']));
            const deleteUrl = @json(route('admin.categories.destroy', ['category' => '__ID__']));

            document.querySelectorAll('.js-edit-category').forEach(function(button) {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;

                    editForm.action = updateUrl.replace('__ID__', id);
                    editName.value = this.dataset.name;

                    bootstrap.Modal.getOrCreateInstance(editModal).show();
                });
            });

            document.querySelectorAll('.js-delete-category').forEach(function(button) {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;

                    deleteForm.action = deleteUrl.replace('__ID__', id);
                    deleteName.textContent = this.dataset.name;

                    bootstrap.Modal.getOrCreateInstance(deleteModal).show();
                });
            });

            document.getElementById('categorySearch').addEventListener('input', function() {
                const keyword = this.value.toLowerCase();

                document.querySelectorAll('#categoryTable tbody tr').forEach(function(row) {
                    const name = row.querySelector('.category-name');

                    if (name) {
                        row.style.display = name.textContent.toLowerCase().includes(keyword) ?
                            '' :
                            'none';
                    }
                });
            });
        });
    </script>
@endpush