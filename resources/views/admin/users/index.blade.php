@extends('layouts.app')

@section('title', 'Kelola Users')

@section('content')
    <div class="users-page">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Kelola Users</h1>
                <p class="page-subtitle">Kelola akun admin dan barista Kopi Pusat.</p>
            </div>
            <div class="page-header-actions">
                <a href="{{ route('admin.users.create') }}" class="btn btn-light users-add-button">
                    <i class="bi bi-plus-lg"></i>
                    Tambah User
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="users-alert">
                <i class="bi bi-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="users-filter-card">
            <form method="GET" action="{{ route('admin.users.index') }}" class="users-filter-form">
                <div class="users-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, username, atau nomor HP">
                </div>

                <div class="users-select">
                    <select name="role">
                        <option value="">Semua Role</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="barista" @selected(request('role') === 'barista')>Barista</option>
                    </select>
                </div>

                <button type="submit" class="users-filter-button">
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>

                @if (request()->filled('search') || request()->filled('role'))
                    <a href="{{ route('admin.users.index') }}" class="users-reset-button">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="users-table-card">
            <div class="users-table-header">
                <div>
                    <h2>Daftar Users</h2>
                    <span>{{ $users->total() }} user</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table users-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Username</th>
                            <th>No. HP</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="users-user">
                                        <div class="users-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->phone_number ?? '—' }}</td>
                                <td>
                                    @if ($user->role === 'admin')
                                        <span class="users-role users-role-admin">Admin</span>
                                    @else
                                        <span class="users-role users-role-barista">Barista</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->trashed())
                                        <span class="users-status users-status-inactive">
                                            <i class="bi bi-x-circle"></i>
                                            Nonaktif
                                        </span>
                                    @else
                                        <span class="users-status users-status-active">
                                            <i class="bi bi-check-circle"></i>
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="users-action-wrapper">
                                        <button type="button" class="users-action-button"
                                            onclick="openUserActionMenu(event, {
                                            id: {{ $user->id }},
                                            name: @js($user->name),
                                            role: @js($user->role),
                                            isTrashed: {{ $user->trashed() ? 'true' : 'false' }},
                                            isSelf: {{ $user->id === auth()->id() ? 'true' : 'false' }},
                                            showUrl: @js(route('admin.users.show', $user)),
                                            editUrl: @js(route('admin.users.edit', $user)),
                                            destroyUrl: @js(route('admin.users.destroy', $user)),
                                            restoreUrl: @js(route('admin.users.restore', $user)),
                                            forceDeleteUrl: @js(route('admin.users.forceDestroy', $user))
                                        })">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="users-empty">
                                        <div class="users-empty-icon">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <strong>Belum ada user</strong>
                                        <span>Data user belum tersedia.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="users-pagination">
                    <div class="users-pagination-info">
                        Menampilkan
                        <strong>{{ $users->firstItem() }}</strong>
                        -
                        <strong>{{ $users->lastItem() }}</strong>
                        dari
                        <strong>{{ $users->total() }}</strong>
                        user
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>

        <div id="userActionMenu" class="user-action-menu" style="display: none;">
            <a id="userActionShow" href="#" class="user-action-item">
                <i class="bi bi-eye"></i>
                <span>Lihat Detail</span>
            </a>

            <a id="userActionEdit" href="#" class="user-action-item">
                <i class="bi bi-pencil"></i>
                <span>Edit</span>
            </a>

            <div id="userActionDivider" class="user-action-divider"></div>

            <form id="userActionStatusForm" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
                <button type="button" id="userActionStatus" class="user-action-item user-action-button-item">
                    <i class="bi bi-person-slash"></i>
                    <span>Nonaktifkan</span>
                </button>
            </form>

            <form id="userActionRestoreForm" method="POST" style="display: none;">
                @csrf
                @method('PATCH')
                <button type="button" id="userActionRestore" class="user-action-item user-action-button-item">
                    <i class="bi bi-person-check"></i>
                    <span>Aktifkan</span>
                </button>
            </form>

            <form id="userActionForceDeleteForm" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
                <button type="button" id="userActionForceDelete"
                    class="user-action-item user-action-button-item user-action-danger">
                    <i class="bi bi-trash3"></i>
                    <span>Hapus Permanen</span>
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentUserAction = null;

        function openUserActionMenu(event, user) {
            event.stopPropagation();

            const menu = document.getElementById('userActionMenu');
            const showButton = document.getElementById('userActionShow');
            const editButton = document.getElementById('userActionEdit');
            const divider = document.getElementById('userActionDivider');
            const statusForm = document.getElementById('userActionStatusForm');
            const restoreForm = document.getElementById('userActionRestoreForm');
            const forceDeleteForm = document.getElementById('userActionForceDeleteForm');

            currentUserAction = user;

            showButton.href = user.showUrl;
            editButton.href = user.editUrl;
            statusForm.action = user.destroyUrl;
            restoreForm.action = user.restoreUrl;
            forceDeleteForm.action = user.forceDeleteUrl;

            showButton.style.display = 'flex';
            editButton.style.display = 'none';
            statusForm.style.display = 'none';
            restoreForm.style.display = 'none';
            forceDeleteForm.style.display = 'none';
            divider.style.display = 'none';

            if (user.isSelf) {
                showButton.style.display = 'flex';
            } else {
                divider.style.display = 'block';
                editButton.style.display = 'flex';

                if (user.isTrashed) {
                    restoreForm.style.display = 'block';
                    forceDeleteForm.style.display = 'block';
                } else {
                    statusForm.style.display = 'block';
                    forceDeleteForm.style.display = 'block';
                }
            }

            menu.style.display = 'block';

            const buttonRect = event.currentTarget.getBoundingClientRect();
            const menuWidth = menu.offsetWidth;
            const menuHeight = menu.offsetHeight;

            let left = buttonRect.right - menuWidth;
            let top = buttonRect.bottom + 8;

            if (left < 8) {
                left = 8;
            }

            if (left + menuWidth > window.innerWidth - 8) {
                left = window.innerWidth - menuWidth - 8;
            }

            if (top + menuHeight > window.innerHeight - 8) {
                top = buttonRect.top - menuHeight - 8;
            }

            if (top < 8) {
                top = 8;
            }

            menu.style.left = `${left}px`;
            menu.style.top = `${top}px`;
        }

        function closeUserActionMenu() {
            const menu = document.getElementById('userActionMenu');

            if (menu) {
                menu.style.display = 'none';
            }
        }

        function confirmUserStatus() {
            if (!currentUserAction) {
                return;
            }

            const form = document.getElementById('userActionStatusForm');

            openConfirmation({
                title: 'Nonaktifkan Barista',
                message: `Apakah Anda yakin ingin menonaktifkan ${currentUserAction.name}? Barista tidak akan dapat digunakan untuk login.`,
                action: form.action,
                method: 'DELETE',
                type: 'warning',
                submitText: 'Ya, Nonaktifkan',
                icon: 'bi-person-slash'
            });
        }

        function confirmUserRestore() {
            if (!currentUserAction) {
                return;
            }

            const form = document.getElementById('userActionRestoreForm');

            openConfirmation({
                title: 'Aktifkan Barista',
                message: `Apakah Anda yakin ingin mengaktifkan kembali ${currentUserAction.name}?`,
                action: form.action,
                method: 'PATCH',
                type: 'warning',
                submitText: 'Ya, Aktifkan',
                icon: 'bi-person-check'
            });
        }

        function confirmUserForceDelete() {
            if (!currentUserAction) {
                return;
            }

            const form = document.getElementById('userActionForceDeleteForm');

            openConfirmation({
                title: 'Hapus Permanen',
                message: `Apakah Anda yakin ingin menghapus ${currentUserAction.name} secara permanen? Data yang dihapus tidak dapat dikembalikan.`,
                action: form.action,
                method: 'DELETE',
                type: 'danger',
                submitText: 'Ya, Hapus Permanen',
                icon: 'bi-trash3'
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('userActionStatus').addEventListener('click', confirmUserStatus);
            document.getElementById('userActionRestore').addEventListener('click', confirmUserRestore);
            document.getElementById('userActionForceDelete').addEventListener('click', confirmUserForceDelete);
        });

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userActionMenu');

            if (!menu) {
                return;
            }

            if (!menu.contains(event.target) && !event.target.closest('.users-action-button')) {
                closeUserActionMenu();
            }
        });

        window.addEventListener('scroll', closeUserActionMenu, true);
    </script>
@endpush
