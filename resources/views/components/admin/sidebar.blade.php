<aside class="app-sidebar">

    {{-- SIDEBAR HEADER --}}
    <div class="sidebar-header">

        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-cup-hot-fill"></i>
            </div>

            <div class="sidebar-brand-text">
                <strong>Kopi Pusat</strong>
                <span>Stock Management</span>
            </div>
        </a>

    </div>


    {{-- SIDEBAR MENU --}}
    <nav class="sidebar-nav">

        {{-- =========================
             UTAMA
        ========================== --}}

        <div class="sidebar-section">

            <span class="sidebar-section-title">
                UTAMA
            </span>

            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>

                <span>
                    Dashboard
                </span>
            </a>

        </div>


        {{-- =========================
             MASTER DATA
        ========================== --}}

        <div class="sidebar-section">

            <span class="sidebar-section-title">
                MASTER DATA
            </span>


            {{-- BARANG --}}

            <a href="#" class="sidebar-link disabled" title="Fitur belum tersedia">
                <i class="bi bi-box-seam"></i>

                <span>
                    Barang
                </span>

                <small>
                    Soon
                </small>
            </a>


            {{-- UNIT --}}

            <a href="{{ route('admin.units.index') }}"
                class="sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                <i class="bi bi-rulers"></i>

                <span>
                    Unit
                </span>
            </a>


            {{-- KATEGORI --}}

            <a href="{{ route('admin.categories.index') }}"
                class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>

                <span>
                    Kategori
                </span>
            </a>

        </div>


        {{-- =========================
             MANAJEMEN
        ========================== --}}

        <div class="sidebar-section">

            <span class="sidebar-section-title">
                MANAJEMEN
            </span>


            {{-- PENGGUNA --}}

            <a href="{{ route('admin.users.index') }}"
                class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>

                <span>
                    Pengguna
                </span>
            </a>


            {{-- RIWAYAT TRANSAKSI --}}

            <a href="#" class="sidebar-link disabled" title="Fitur belum tersedia">
                <i class="bi bi-arrow-left-right"></i>

                <span>
                    Riwayat Transaksi
                </span>

                <small>
                    Soon
                </small>
            </a>

        </div>


        {{-- =========================
             SISTEM
        ========================== --}}

        <div class="sidebar-section">

            <span class="sidebar-section-title">
                SISTEM
            </span>


            {{-- ACTIVITY LOG --}}

            <a href="#" class="sidebar-link disabled" title="Fitur belum tersedia">
                <i class="bi bi-clock-history"></i>

                <span>
                    Activity Log
                </span>

                <small>
                    Soon
                </small>
            </a>

        </div>

    </nav>


    {{-- SIDEBAR FOOTER --}}
    <div class="sidebar-footer">

        {{-- PROFILE --}}

        <a href="{{ route('profile.index') }}"
            class="sidebar-profile-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">

            <div class="sidebar-profile-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div class="sidebar-profile-info">

                <strong>
                    {{ auth()->user()->name }}
                </strong>

                <span>
                    {{ ucfirst(auth()->user()->role) }}
                </span>

            </div>

            <i class="bi bi-chevron-right"></i>

        </a>


        {{-- LOGOUT --}}

        <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">

            @csrf

            <button type="submit" class="sidebar-logout-button">
                <i class="bi bi-box-arrow-right"></i>

                <span>
                    Logout
                </span>
            </button>

        </form>

    </div>

</aside>
