@extends('layouts.app')

@section('title', 'Master Unit')

@section('content')

    <div class="admin-master-page">
        {{-- HEADER --}}
        <div class="master-page-header">
            <div>
                <h1 class="master-page-title">Master Unit</h1>
                <p class="master-page-subtitle">Kelola satuan yang digunakan pada barang.</p>
            </div>
            <button type="button" class="master-primary-button" data-bs-toggle="modal" data-bs-target="#createUnitModal">
                <i class="bi bi-plus-lg"></i>
                Tambah Unit
            </button>
        </div>

        {{-- ALERT --}}
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

        {{-- TABLE --}}
        <div class="master-card">
            <div class="master-card-header">
                <div>
                    <h2>Daftar Unit</h2>
                    <span>{{ $units->total() }} unit</span>
                </div>
            </div>

            <div class="master-table-wrapper">
                <table class="master-table">
                    <thead>
                        <tr>
                            <th width="70">No</th>
                            <th>Nama Unit</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $index => $unit)
                            <tr>
                                <td>{{ $units->firstItem() + $index }}</td>
                                <td>
                                    <span class="master-name">{{ $unit->name }}</span>
                                </td>
                                <td>
                                    <div class="master-action-wrapper">
                                        <button type="button" class="master-action-button"
                                            onclick="toggleUnitActionMenu({{ $unit->id }})" aria-label="Aksi unit">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <div id="unitActionMenu-{{ $unit->id }}" class="master-action-menu">
                                            <button type="button"
                                                onclick="openEditUnitModal({{ $unit->id }}, @js($unit->name))">
                                                <i class="bi bi-pencil"></i>
                                                Edit
                                            </button>
                                            <button type="button" class="danger"
                                                onclick="confirmDeleteUnit({{ $unit->id }}, @js($unit->name))">
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
                                    <i class="bi bi-box"></i>
                                    <span>Belum ada data unit.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($units->hasPages())
                <div class="master-pagination">
                    {{ $units->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- CREATE MODAL --}}

    <div class="modal fade" id="createUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Tambah Unit</h5>
                        <p>Tambahkan unit baru.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('admin.units.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label for="create_unit_name" class="master-form-label">Nama Unit</label>
                        <input type="text" id="create_unit_name" name="name" class="master-form-input"
                            placeholder="Contoh: Kg" value="{{ old('name') }}" required>
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

    <div class="modal fade" id="editUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Edit Unit</h5>
                        <p>Perbarui informasi unit.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editUnitForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <label for="edit_unit_name" class="master-form-label">Nama Unit</label>
                        <input type="text" id="edit_unit_name" name="name" class="master-form-input" required>
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

    {{-- DELETE CONFIRMATION --}}

    <div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-confirm-modal">
                <div class="master-confirm-icon danger">
                    <i class="bi bi-trash3"></i>
                </div>
                <h5>Hapus Unit?</h5>
                <p>Anda yakin ingin menghapus <strong id="deleteUnitName"></strong>?</p>
                <div class="master-confirm-actions">
                    <button type="button" class="master-secondary-button" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteUnitForm" method="POST">
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
        const unitUpdateUrl = @json(route('admin.units.update', ['unit' => '__UNIT_ID__']));
        const unitDeleteUrl = @json(route('admin.units.destroy', ['unit' => '__UNIT_ID__']));

        function buildUnitUrl(template, id) {
            return template.replace('__UNIT_ID__', id);
        }

        function closeUnitActionMenus() {
            document.querySelectorAll('.master-action-menu').forEach(function(menu) {
                menu.classList.remove('show');
            });
        }

        function toggleUnitActionMenu(id) {
            const menu = document.getElementById('unitActionMenu-' + id);
            if (!menu) return;

            const isOpen = menu.classList.contains('show');
            closeUnitActionMenus();

            if (!isOpen) {
                menu.classList.add('show');
            }
        }

        function openEditUnitModal(id, name) {
            closeUnitActionMenus();

            const form = document.getElementById('editUnitForm');
            const input = document.getElementById('edit_unit_name');

            if (!form || !input) return;

            form.action = buildUnitUrl(unitUpdateUrl, id);
            input.value = name;

            const modalElement = document.getElementById('editUnitModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
        }

        function confirmDeleteUnit(id, name) {
            closeUnitActionMenus();

            const form = document.getElementById('deleteUnitForm');
            const nameElement = document.getElementById('deleteUnitName');

            if (!form || !nameElement) return;

            form.action = buildUnitUrl(unitDeleteUrl, id);
            nameElement.textContent = name;

            const modalElement = document.getElementById('deleteUnitModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.master-action-wrapper')) {
                closeUnitActionMenus();
            }
        });
    </script>
@endpush
