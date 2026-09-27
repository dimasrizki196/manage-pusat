@extends('layouts.app')

@section('title', 'Kelola Users')

@section('content')
    <div class="container-fluid py-4 users-page">

        {{-- HEADER --}}
        <div class="users-page-header">

            <div>
                <h1 class="users-page-title">
                    Kelola Users
                </h1>

                <p class="users-page-description">
                    Kelola akun admin dan barista Kopi Pusat.
                </p>
            </div>

            <a href="{{ route('admin.users.create') }}" class="btn btn-coffee">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah User
            </a>

        </div>


        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif


        {{-- FILTER --}}
        <div class="users-filter">

            <div class="users-filter-body">

                <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Cari User
                        </label>

                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Nama, username, atau nomor HP">

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Role
                        </label>

                        <select name="role" class="form-select">

                            <option value="">
                                Semua Role
                            </option>

                            <option value="admin" @selected(request('role') === 'admin')>
                                Admin
                            </option>

                            <option value="barista" @selected(request('role') === 'barista')>
                                Barista
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3 d-flex align-items-end">

                        <button type="submit" class="btn btn-coffee w-100">
                            <i class="bi bi-search me-1"></i>
                            Cari
                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="users-table-card">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th class="ps-4">
                                #
                            </th>

                            <th>
                                User
                            </th>

                            <th>
                                Username
                            </th>

                            <th>
                                No. HP
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-end pe-4">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($users as $user)
                            <tr>

                                <td class="ps-4">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>


                                <td>

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>

                                        <div>

                                            <div class="fw-semibold">
                                                {{ $user->name }}
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    {{ $user->username }}
                                </td>


                                <td>
                                    {{ $user->phone_number ?? '-' }}
                                </td>


                                <td>

                                    @if ($user->role === 'admin')
                                        <span class="badge bg-dark">
                                            Admin
                                        </span>
                                    @else
                                        <span class="badge bg-coffee-soft">
                                            Barista
                                        </span>
                                    @endif

                                </td>


                                <td>

                                    @if ($user->trashed())
                                        <span class="badge bg-danger-subtle text-danger">
                                            Nonaktif
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">
                                            Aktif
                                        </span>
                                    @endif

                                </td>


                                <td class="text-end pe-4">

                                    <button type="button" class="users-action-button"
                                        onclick="openUserActionMenu(event, {
                                            id: {{ $user->id }},
                                            name: @js($user->name),
                                            role: @js($user->role),
                                            isTrashed: {{ $user->trashed() ? 'true' : 'false' }},
                                            isSelf: {{ $user->id === auth()->id() ? 'true' : 'false' }},

                                            showUrl: @js(route('admin.users.show', $user)),
                                            editUrl: @js(route('admin.users.edit', $user)),
                                            updateUrl: @js(route('admin.users.update', $user)),
                                            destroyUrl: @js(route('admin.users.destroy', $user)),
                                            restoreUrl: @js(route('admin.users.restore', $user)),
                                            forceDeleteUrl: @js(route('admin.users.forceDestroy', $user))
                                        })">
                                        <i class="bi bi-three-dots"></i>
                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-people fs-2 d-block mb-2"></i>

                                        Belum ada user.

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

            {{-- DETAIL --}}
            <a id="userActionShow" href="#" class="user-action-item">
                <i class="bi bi-eye"></i>
                <span>Lihat Detail</span>
            </a>


            {{-- EDIT --}}
            <a id="userActionEdit" href="#" class="user-action-item">
                <i class="bi bi-pencil"></i>
                <span>Edit</span>
            </a>


            <div id="userActionDivider" class="user-action-divider"></div>


            {{-- NONAKTIFKAN --}}
            <form id="userActionStatusForm" method="POST" style="display: none;">
                @csrf
                @method('DELETE')

                <button type="button" id="userActionStatus" class="user-action-item user-action-button-item">
                    <i class="bi bi-person-slash"></i>
                    <span>Nonaktifkan</span>
                </button>
            </form>


            {{-- AKTIFKAN --}}
            <form id="userActionRestoreForm" method="POST" style="display: none;">
                @csrf
                @method('PATCH')

                <button type="button" id="userActionRestore" class="user-action-item user-action-button-item">
                    <i class="bi bi-person-check"></i>
                    <span>Aktifkan</span>
                </button>
            </form>

            {{-- HARD DELETE --}}
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
        function openUserActionMenu(event, user) {

            event.stopPropagation();

            const menu = document.getElementById('userActionMenu');

            const showButton = document.getElementById('userActionShow');

            const editButton = document.getElementById('userActionEdit');

            const divider = document.getElementById('userActionDivider');

            const statusForm = document.getElementById('userActionStatusForm');
            const statusButton = document.getElementById('userActionStatus');

            const restoreForm = document.getElementById('userActionRestoreForm');
            const restoreButton = document.getElementById('userActionRestore');

            const forceDeleteForm = document.getElementById('userActionForceDeleteForm');
            const forceDeleteButton = document.getElementById('userActionForceDelete');


            statusButton.addEventListener('click', function() {

                const form =
                    document.getElementById('userActionStatusForm');

                const userName =
                    this.dataset.userName;

                openConfirmation({

                    title: 'Nonaktifkan Barista',

                    message: `Apakah Anda yakin ingin menonaktifkan ${userName}? ` +
                        `Barista tidak akan dapat digunakan untuk login.`,

                    action: form.action,

                    method: 'DELETE',

                    type: 'warning',

                    submitText: 'Ya, Nonaktifkan',

                    icon: 'bi-person-slash',

                });

            });

            restoreButton.addEventListener('click', function() {

                const form =
                    document.getElementById('userActionRestoreForm');

                const userName =
                    this.dataset.userName;

                openConfirmation({

                    title: 'Aktifkan Barista',

                    message: `Apakah Anda yakin ingin mengaktifkan kembali ${userName}?`,

                    action: form.action,

                    method: 'PATCH',

                    type: 'warning',

                    submitText: 'Ya, Aktifkan',

                    icon: 'bi-person-check',

                });

            });

            forceDeleteButton.addEventListener('click', function() {

                const form =
                    document.getElementById('userActionForceDeleteForm');

                const userName =
                    this.dataset.userName;

                openConfirmation({

                    title: 'Hapus Permanen',

                    message: `Apakah Anda yakin ingin menghapus ${userName} secara permanen? ` +
                        `Data yang dihapus tidak dapat dikembalikan.`,

                    action: form.action,

                    method: 'DELETE',

                    type: 'danger',

                    submitText: 'Ya, Hapus Permanen',

                    icon: 'bi-trash3',

                });

            });


            /*
            |--------------------------------------------------------------------------
            | Set URL
            |--------------------------------------------------------------------------
            */

            showButton.href = user.showUrl;

            editButton.href = user.editUrl;

            statusForm.action = user.destroyUrl;

            restoreForm.action = user.restoreUrl;

            forceDeleteForm.action = user.forceDeleteUrl;


            /*
            |--------------------------------------------------------------------------
            | Reset
            |--------------------------------------------------------------------------
            */

            showButton.style.display = 'flex';

            editButton.style.display = 'none';

            statusForm.style.display = 'none';

            restoreForm.style.display = 'none';

            forceDeleteForm.style.display = 'none';

            divider.style.display = 'none';


            /*
            |--------------------------------------------------------------------------
            | Admin / Self
            |--------------------------------------------------------------------------
            |
            | Admin tidak boleh dimodifikasi melalui halaman users.
            |
            */

            if (user.isSelf) {

                /*
                 * Admin yang sedang login hanya boleh melihat detail.
                 */

                showButton.style.display = 'flex';

            }

            /*
            |--------------------------------------------------------------------------
            | Admin lain
            |--------------------------------------------------------------------------
            */
            else if (user.role === 'admin') {

                /*
                 * Admin lain juga hanya boleh dilihat.
                 */

                showButton.style.display = 'flex';

            }

            /*
            |--------------------------------------------------------------------------
            | Barista
            |--------------------------------------------------------------------------
            */
            else {

                divider.style.display = 'block';

                editButton.style.display = 'flex';

                /*
                 * User aktif
                 */

                if (!user.isTrashed) {

                    statusForm.style.display = 'block';

                    forceDeleteForm.style.display = 'block';

                }

                /*
                 * User nonaktif
                 */
                else {

                    restoreForm.style.display = 'block';

                    forceDeleteForm.style.display = 'block';

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Show menu
            |--------------------------------------------------------------------------
            */

            menu.style.display = 'block';


            /*
            |--------------------------------------------------------------------------
            | Position
            |--------------------------------------------------------------------------
            */

            const buttonRect =
                event.currentTarget.getBoundingClientRect();

            const menuWidth =
                menu.offsetWidth;

            const menuHeight =
                menu.offsetHeight;


            let left =
                buttonRect.right - menuWidth;

            let top =
                buttonRect.bottom + 8;


            /*
            |--------------------------------------------------------------------------
            | Prevent overflow kanan
            |--------------------------------------------------------------------------
            */

            if (left < 8) {

                left = 8;

            }


            /*
            |--------------------------------------------------------------------------
            | Prevent overflow kiri
            |--------------------------------------------------------------------------
            */

            if (left + menuWidth > window.innerWidth - 8) {

                left =
                    window.innerWidth -
                    menuWidth -
                    8;

            }


            /*
            |--------------------------------------------------------------------------
            | Prevent overflow bawah
            |--------------------------------------------------------------------------
            */

            if (top + menuHeight > window.innerHeight - 8) {

                top =
                    buttonRect.top -
                    menuHeight -
                    8;

            }


            /*
            |--------------------------------------------------------------------------
            | Prevent overflow atas
            |--------------------------------------------------------------------------
            */

            if (top < 8) {

                top = 8;

            }


            menu.style.left = `${left}px`;

            menu.style.top = `${top}px`;

        }


        /*
        |--------------------------------------------------------------------------
        | Close menu
        |--------------------------------------------------------------------------
        */

        document.addEventListener('click', function(event) {

            const menu =
                document.getElementById('userActionMenu');

            if (!menu) {
                return;
            }


            if (
                !menu.contains(event.target) &&
                !event.target.closest('.users-action-button')
            ) {

                menu.style.display = 'none';

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Close on scroll
        |--------------------------------------------------------------------------
        */

        window.addEventListener(
            'scroll',
            function() {

                const menu =
                    document.getElementById('userActionMenu');

                if (menu) {

                    menu.style.display = 'none';

                }

            },
            true
        );
    </script>
@endpush
