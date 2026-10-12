@extends('layouts.app')

@section('title', 'Master Unit')

@push('styles')
    @vite('resources/css/admin/units.css')
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
                    <span>Master Unit</span>
                </div>
                <h1 class="master-page-title">Master Unit</h1>
                <p class="master-page-subtitle">Kelola satuan yang digunakan pada barang.</p>
            </div>
            <div class="master-page-header-actions">
                <button type="button" class="master-primary-button" data-bs-toggle="modal"
                    data-bs-target="#createUnitModal">
                    <i class="bi bi-plus-lg"></i><span>Tambah Unit</span>
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="master-alert master-alert-success">
                <i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="master-alert master-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i><span>{{ session('error') }}</span>
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
                    <h2>Daftar Unit</h2>
                    <p>Daftar satuan yang tersedia untuk barang.</p>
                </div>
                <span class="master-card-total">Total: {{ $units->total() }} unit</span>
            </div>

            <div class="master-table-wrapper">
                <table class="master-table">
                    <thead>
                        <tr>
                            <th width="70">No</th>
                            <th>Nama Unit</th>
                            <th width="110">Status</th>
                            <th width="70">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $index => $unit)
                            @php
                                $isActive = empty($unit->activity_deleted_at);
                            @endphp
                            <tr>
                                <td><span class="master-number">{{ $units->firstItem() + $index }}</span></td>
                                <td><span class="master-name">{{ $unit->name }}</span></td>
                                <td>
                                    <span class="master-status {{ $isActive ? 'active' : 'inactive' }}">
                                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="master-action-button js-unit-action"
                                        data-unit-id="{{ $unit->id }}" data-unit-name="{{ $unit->name }}"
                                        data-unit-status="{{ $isActive ? 'active' : 'inactive' }}" aria-label="Aksi unit"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="master-empty">
                                        <div class="master-empty-icon"><i class="bi bi-rulers"></i></div>
                                        <strong>Belum ada data unit</strong>
                                        <span>Tambahkan unit untuk digunakan pada data barang.</span>
                                        <button type="button" class="master-primary-button master-empty-button"
                                            data-bs-toggle="modal" data-bs-target="#createUnitModal">
                                            <i class="bi bi-plus-lg"></i>Tambah Unit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($units->hasPages())
                <div class="master-pagination">{{ $units->links() }}</div>
            @endif
        </div>
    </div>

    <div id="unitActionMenu" class="master-global-action-menu" hidden>
        <button type="button" id="unitActionEdit">
            <i class="bi bi-pencil"></i><span>Edit</span>
        </button>
        <button type="button" id="unitActionDelete" class="danger">
            <i class="bi bi-trash3"></i><span>Nonaktifkan</span>
        </button>
        <button type="button" id="unitActionRestore">
            <i class="bi bi-arrow-counterclockwise"></i><span>Aktifkan</span>
        </button>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="createUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Tambah Unit</h5>
                        <p>Tambahkan unit baru.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="{{ route('admin.units.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label for="create_unit_name" class="master-form-label">Nama Unit</label>
                        <input type="text" id="create_unit_name" name="name" class="master-form-input"
                            placeholder="Contoh: Kg" value="{{ old('name') }}" required autocomplete="off">
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
    <div class="modal fade" id="editUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Edit Unit</h5>
                        <p>Perbarui informasi unit.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="editUnitForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <label for="edit_unit_name" class="master-form-label">Nama Unit</label>
                        <input type="text" id="edit_unit_name" name="name" class="master-form-input" required
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
    <div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-confirm-modal">
                <div class="master-confirm-icon danger"><i class="bi bi-trash3"></i></div>
                <h5>Nonaktifkan Unit?</h5>
                <p>Anda yakin ingin menonaktifkan unit <strong id="deleteUnitName"></strong>?</p>
                <div class="master-confirm-actions">
                    <button type="button" class="master-secondary-button" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteUnitForm" method="POST">
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
    <div class="modal fade" id="restoreUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content master-confirm-modal">
                <div class="master-confirm-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
                <h5>Aktifkan Unit?</h5>
                <p>Anda yakin ingin mengaktifkan kembali unit <strong id="restoreUnitName"></strong>?</p>
                <div class="master-confirm-actions">
                    <button type="button" class="master-secondary-button" data-bs-dismiss="modal">Batal</button>
                    <form id="restoreUnitForm" method="POST">
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
            const actionMenu = document.getElementById('unitActionMenu');
            const actionEdit = document.getElementById('unitActionEdit');
            const actionDelete = document.getElementById('unitActionDelete');
            const actionRestore = document.getElementById('unitActionRestore');
            const editModalElement = document.getElementById('editUnitModal');
            const deleteModalElement = document.getElementById('deleteUnitModal');
            const restoreModalElement = document.getElementById('restoreUnitModal');
            const editForm = document.getElementById('editUnitForm');
            const editInput = document.getElementById('edit_unit_name');
            const deleteForm = document.getElementById('deleteUnitForm');
            const deleteName = document.getElementById('deleteUnitName');
            const restoreForm = document.getElementById('restoreUnitForm');
            const restoreName = document.getElementById('restoreUnitName');
            const updateUrl = @json(route('admin.units.update', ['unit' => '__UNIT_ID__']));
            const deleteUrl = @json(route('admin.units.destroy', ['unit' => '__UNIT_ID__']));
            const restoreUrl = @json(route('admin.units.restore', ['unit' => '__UNIT_ID__']));
            let activeButton = null;
            let activeUnit = null;

            function buildUrl(template, id) {
                return template.replace('__UNIT_ID__', encodeURIComponent(id));
            }

            function closeActionMenu() {
                actionMenu.hidden = true;
                activeButton?.setAttribute('aria-expanded', 'false');
                activeButton = null;
                activeUnit = null;
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
                const id = button.dataset.unitId;
                if (!id) return;

                if (activeButton === button && !actionMenu.hidden) {
                    closeActionMenu();
                    return;
                }

                closeActionMenu();
                activeButton = button;
                activeUnit = {
                    id: id,
                    name: button.dataset.unitName || '',
                    status: button.dataset.unitStatus || 'active'
                };

                actionEdit.hidden = activeUnit.status !== 'active';
                actionDelete.hidden = activeUnit.status !== 'active';
                actionRestore.hidden = activeUnit.status !== 'inactive';

                button.setAttribute('aria-expanded', 'true');
                positionActionMenu(button);
            }

            document.querySelectorAll('.js-unit-action').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    openActionMenu(this);
                });
            });

            actionEdit.addEventListener('click', function() {
                if (!activeUnit) return;
                editForm.action = buildUrl(updateUrl, activeUnit.id);
                editInput.value = activeUnit.name;
                closeActionMenu();
                bootstrap.Modal.getOrCreateInstance(editModalElement).show();
                setTimeout(() => {
                    editInput.focus();
                    editInput.select();
                }, 300);
            });

            actionDelete.addEventListener('click', function() {
                if (!activeUnit) return;
                deleteForm.action = buildUrl(deleteUrl, activeUnit.id);
                deleteName.textContent = activeUnit.name;
                closeActionMenu();
                bootstrap.Modal.getOrCreateInstance(deleteModalElement).show();
            });

            actionRestore.addEventListener('click', function() {
                if (!activeUnit) return;
                restoreForm.action = buildUrl(restoreUrl, activeUnit.id);
                restoreName.textContent = activeUnit.name;
                closeActionMenu();
                bootstrap.Modal.getOrCreateInstance(restoreModalElement).show();
            });

            document.addEventListener('click', function(event) {
                if (!event.target.closest('.js-unit-action') && !event.target.closest('#unitActionMenu')) {
                    closeActionMenu();
                }
            });

            window.addEventListener('resize', function() {
                if (activeButton && !actionMenu.hidden) positionActionMenu(activeButton);
            });

            window.addEventListener('scroll', function() {
                if (activeButton && !actionMenu.hidden) positionActionMenu(activeButton);
            }, true);

            document.getElementById('createUnitModal')?.addEventListener('shown.bs.modal', function() {
                document.getElementById('create_unit_name')?.focus();
            });
        });
    </script>
@endpush