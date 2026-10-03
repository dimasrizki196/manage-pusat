<aside class="app-sidebar">

    {{-- SIDEBAR HEADER --}}
    <div class="sidebar-header">
        <a href="{{ route('barista.dashboard') }}" class="sidebar-brand">
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

            <a href="{{ route('barista.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('barista.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                <span>Dashboard</span>
            </a>
        </div>

        {{-- OPERASIONAL --}}
        <div class="sidebar-section">
            <span class="sidebar-section-title">OPERASIONAL</span>

            <a href="{{ route('barista.restock-requests.index') }}"
                class="sidebar-link {{ request()->routeIs('barista.restock-requests.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Pengajuan Restock</span>
            </a>

            <a href="{{ route('barista.transactions.incoming') }}"
                class="sidebar-link {{ request()->routeIs('barista.transactions.incoming') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-down"></i>
                <span>Barang Masuk</span>
            </a>

            <a href="{{ route('barista.transactions.outgoing') }}"
                class="sidebar-link {{ request()->routeIs('barista.transactions.outgoing') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-up"></i>
                <span>Barang Keluar</span>
            </a>

            <a href="{{ route('barista.transactions.history') }}"
                class="sidebar-link {{ request()->routeIs('barista.transactions.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Transaksi</span>
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
