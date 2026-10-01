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

        {{-- UTAMA --}}
        <div class="sidebar-section">
            <span class="sidebar-section-title">UTAMA</span>

            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                <span>Dashboard</span>
            </a>
        </div>

        {{-- MASTER DATA --}}
        <div class="sidebar-section">
            <span class="sidebar-section-title">MASTER DATA</span>

            <a href="{{ route('admin.items.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.items.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Barang</span>
            </a>

            <a href="{{ route('admin.categories.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span>Kategori</span>
            </a>

            <a href="{{ route('admin.units.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                <i class="bi bi-rulers"></i>
                <span>Satuan</span>
            </a>
        </div>

        {{-- OPERASIONAL --}}
        <div class="sidebar-section">
            <span class="sidebar-section-title">OPERASIONAL</span>

            <a href="{{ route('admin.transactions.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Transaksi Stok</span>
            </a>

            {{-- Jika route restock request sudah dibuat --}}
            @if (Route::has('admin.restock-requests.index'))
                <a href="{{ route('admin.restock-requests.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.restock-requests.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Ajuan Restock</span>
                </a>
            @endif
        </div>

        {{-- MANAJEMEN --}}
        <div class="sidebar-section">
            <span class="sidebar-section-title">MANAJEMEN</span>

            <a href="{{ route('admin.users.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Pengguna</span>
            </a>
        </div>

        {{-- SISTEM --}}
        <div class="sidebar-section">
            <span class="sidebar-section-title">SISTEM</span>

            <a href="{{ route('admin.activity-logs.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Activity Log</span>
            </a>
        </div>
    </nav>

    {{-- SIDEBAR FOOTER --}}
    <div class="sidebar-footer">

        {{-- APPLICATION STATUS --}}
        <div class="sidebar-status">
            <span class="sidebar-status-dot"></span>

            <div class="sidebar-status-info">
                <strong>System Online</strong>
                <span>Kopi Pusat</span>
            </div>
        </div>

        {{-- LOGOUT --}}
        <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
            @csrf

            <button type="submit" class="sidebar-logout-button">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
