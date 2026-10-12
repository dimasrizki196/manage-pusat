@extends('layouts.app')

@section('title', 'Kelola Unit')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Kelola Unit</h4>
                <p class="text-muted mb-0">Kelola satuan untuk barang persediaan toko.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUnitModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Unit
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
                <div class="fw-semibold mb-1">Periksa kembali data unit.</div>
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
                            <input type="text" id="unitSearch" class="form-control" placeholder="Cari nama unit...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover" id="unitTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">No.</th>
                                <th>Nama Unit</th>
                                <th style="width: 150px;">Status</th>
                                <th class="text-end" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($units as $unit)
                                @php
                                    $isInactive = !is_null($unit->activity_deleted_at);
                                @endphp
                                <tr>
                                    <td>{{ $units->firstItem() + $loop->index }}</td>
                                    <td class="unit-name fw-medium">{{ $unit->name }}</td>
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
                                                        <button type="button" class="dropdown-item js-edit-unit"
                                                            data-id="{{ $unit->id }}" data-name="{{ $unit->name }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item text-danger js-delete-unit"
                                                            data-id="{{ $unit->id }}" data-name="{{ $unit->name }}">
                                                            <i class="bi bi-archive me-2"></i>Nonaktifkan
                                                        </button>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('admin.units.restore', $unit->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Aktifkan kembali unit ini?')">
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
                                        Belum ada data unit.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $units->firstItem() ?? 0 }}–{{ $units->lastItem() ?? 0 }}
                        dari {{ $units->total() }} unit
                    </small>
                    {{ $units->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal tambah unit --}}
    <div class="modal fade" id="createUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.units.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="createUnitName" class="form-label">Nama unit</label>
                        <input type="text" name="name" id="createUnitName" class="form-control" maxlength="100"
                            placeholder="Contoh: Gram, Kilogram, Liter" required>
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

    {{-- Modal edit unit --}}
    <div class="modal fade" id="editUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="editUnitForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="editUnitName" class="form-label">Nama unit</label>
                        <input type="text" name="name" id="editUnitName" class="form-control" maxlength="100"
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
    <div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="deleteUnitForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body text-center p-4">
                        <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex
                        align-items-center justify-content-center mb-3"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-archive fs-3"></i>
                        </div>
                        <h5 class="fw-bold">Nonaktifkan Unit?</h5>
                        <p class="text-muted mb-0">
                            Unit <strong id="deleteUnitName"></strong> akan dinonaktifkan.
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
            const editModal = document.getElementById('editUnitModal');
            const deleteModal = document.getElementById('deleteUnitModal');
            const editForm = document.getElementById('editUnitForm');
            const editName = document.getElementById('editUnitName');
            const deleteForm = document.getElementById('deleteUnitForm');
            const deleteName = document.getElementById('deleteUnitName');

            const updateUrl = @json(route('admin.units.update', ['unit' => '__ID__']));
            const deleteUrl = @json(route('admin.units.destroy', ['unit' => '__ID__']));

            document.querySelectorAll('.js-edit-unit').forEach(function(button) {
                button.addEventListener('click', function() {
                    editForm.action = updateUrl.replace('__ID__', this.dataset.id);
                    editName.value = this.dataset.name;
                    bootstrap.Modal.getOrCreateInstance(editModal).show();
                });
            });

            document.querySelectorAll('.js-delete-unit').forEach(function(button) {
                button.addEventListener('click', function() {
                    deleteForm.action = deleteUrl.replace('__ID__', this.dataset.id);
                    deleteName.textContent = this.dataset.name;
                    bootstrap.Modal.getOrCreateInstance(deleteModal).show();
                });
            });

            document.getElementById('unitSearch').addEventListener('input', function() {
                const keyword = this.value.toLowerCase();

                document.querySelectorAll('#unitTable tbody tr').forEach(function(row) {
                    const name = row.querySelector('.unit-name');

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