@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    @vite('resources/css/admin/dashboard.css')
@endpush

@section('content')
    <div class="admin-dashboard">
        {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}
        <div class="dashboard-header">
            <div>
                <div class="dashboard-breadcrumb">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </div>

                <h1>
                    Dashboard
                </h1>

                <p>
                    Pantau kondisi stok, transaksi, dan aktivitas toko secara keseluruhan.
                </p>
            </div>
        </div>

        {{-- =====================================================
         SUMMARY CARDS
    ====================================================== --}}
        <div class="dashboard-summary-grid">
            <a href="{{ route('admin.items.index') }}" class="dashboard-summary-card">
                <div class="summary-card-icon summary-card-icon-blue">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div class="summary-card-content">
                    <span class="summary-card-label">
                        Barang Aktif
                    </span>

                    <strong class="summary-card-value">
                        {{ number_format($totalItems, 0, ',', '.') }}
                    </strong>

                    <small>
                        Total barang yang digunakan
                    </small>
                </div>

                <i class="bi bi-chevron-right summary-card-arrow"></i>
            </a>

            <a href="{{ route('admin.items.index', ['status' => 'active']) }}" class="dashboard-summary-card">
                <div class="summary-card-icon summary-card-icon-orange">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>

                <div class="summary-card-content">
                    <span class="summary-card-label">
                        Stok Menipis
                    </span>

                    <strong class="summary-card-value">
                        {{ number_format($lowStockCount, 0, ',', '.') }}
                    </strong>

                    <small>
                        Barang mencapai batas minimum
                    </small>
                </div>

                <i class="bi bi-chevron-right summary-card-arrow"></i>
            </a>

            <a href="{{ route('admin.restock-requests.index', ['status' => 'pending']) }}" class="dashboard-summary-card">
                <div class="summary-card-icon summary-card-icon-purple">
                    <i class="bi bi-clipboard-check"></i>
                </div>

                <div class="summary-card-content">
                    <span class="summary-card-label">
                        Pengajuan Pending
                    </span>

                    <strong class="summary-card-value">
                        {{ number_format($pendingRestockCount, 0, ',', '.') }}
                    </strong>

                    <small>
                        Menunggu persetujuan admin
                    </small>
                </div>

                <i class="bi bi-chevron-right summary-card-arrow"></i>
            </a>

            <div class="dashboard-summary-card">
                <div class="summary-card-icon summary-card-icon-green">
                    <i class="bi bi-arrow-left-right"></i>
                </div>

                <div class="summary-card-content">
                    <span class="summary-card-label">
                        Transaksi Hari Ini
                    </span>

                    <strong class="summary-card-value">
                        {{ number_format($todayTransactionCount, 0, ',', '.') }}
                    </strong>

                    <small>
                        {{ $todayIncomingCount }} masuk · {{ $todayOutgoingCount }} keluar
                    </small>
                </div>
            </div>
        </div>

        {{-- =====================================================
         TODAY STOCK MOVEMENT
    ====================================================== --}}
        <div class="dashboard-movement-grid">
            <div class="dashboard-movement-card movement-in">
                <div class="movement-icon">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>

                <div class="movement-content">
                    <span>
                        Barang Masuk Hari Ini
                    </span>

                    <strong>
                        {{ rtrim(rtrim(number_format($todayIncoming, 2, ',', '.'), '0'), ',') }}
                    </strong>

                    <small>
                        Total quantity barang masuk
                    </small>
                </div>
            </div>

            <div class="dashboard-movement-card movement-out">
                <div class="movement-icon">
                    <i class="bi bi-box-arrow-up"></i>
                </div>

                <div class="movement-content">
                    <span>
                        Barang Keluar Hari Ini
                    </span>

                    <strong>
                        {{ rtrim(rtrim(number_format($todayOutgoing, 2, ',', '.'), '0'), ',') }}
                    </strong>

                    <small>
                        Total quantity barang keluar
                    </small>
                </div>
            </div>
        </div>

        {{-- =====================================================
         CHARTS
    ====================================================== --}}
        <div class="dashboard-chart-grid">
            <section class="dashboard-card dashboard-chart-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2>
                            Aktivitas Transaksi
                        </h2>

                        <p>
                            Pergerakan barang selama 7 hari terakhir.
                        </p>
                    </div>

                    <div class="dashboard-card-icon">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                </div>

                <div class="dashboard-chart-wrapper">
                    <canvas id="transactionChart"></canvas>
                </div>
            </section>

            <section class="dashboard-card dashboard-chart-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2>
                            Stok per Kategori
                        </h2>

                        <p>
                            Total stok aktif berdasarkan kategori.
                        </p>
                    </div>

                    <div class="dashboard-card-icon">
                        <i class="bi bi-pie-chart"></i>
                    </div>
                </div>

                <div class="dashboard-chart-wrapper dashboard-chart-wrapper-doughnut">
                    <canvas id="stockCategoryChart"></canvas>
                </div>
            </section>
        </div>

        {{-- =====================================================
         LOWER CONTENT
    ====================================================== --}}
        <div class="dashboard-content-grid">
            {{-- LOW STOCK --}}
            <section class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2>
                            Stok Menipis
                        </h2>

                        <p>
                            Barang yang sudah mencapai batas minimum.
                        </p>
                    </div>

                    <a href="{{ route('admin.items.index') }}" class="dashboard-card-link">
                        Lihat Semua
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="dashboard-list">
                    @forelse($lowStockItems->take(6) as $item)
                        @php
                            $stock = (float) $item->stock;
                            $stockMin = (float) $item->stock_min;

                            $percentage = $stockMin > 0 ? min(($stock / $stockMin) * 100, 100) : 0;

                            $formattedStock = rtrim(rtrim(number_format($stock, 2, ',', '.'), '0'), ',');

                            $formattedStockMin = rtrim(rtrim(number_format($stockMin, 2, ',', '.'), '0'), ',');
                        @endphp

                        <a href="{{ route('admin.items.show', $item->id) }}" class="dashboard-list-item">
                            <div class="dashboard-list-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>

                            <div class="dashboard-list-main">
                                <strong>
                                    {{ $item->name }}
                                </strong>

                                <span>
                                    {{ $item->category?->name ?? 'Tanpa Kategori' }}
                                </span>

                                <div class="stock-progress">
                                    <div class="stock-progress-bar" style="width: {{ $percentage }}%;"></div>
                                </div>
                            </div>

                            <div class="dashboard-list-value">
                                <strong>
                                    {{ $formattedStock }}
                                </strong>

                                <span>
                                    / {{ $formattedStockMin }}
                                    {{ $item->unit?->name ?? '' }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="dashboard-empty">
                            <div class="dashboard-empty-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>

                            <strong>
                                Semua stok aman
                            </strong>

                            <p>
                                Belum ada barang yang mencapai batas minimum.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- RESTOCK --}}
            <section class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2>
                            Pengajuan Restock
                        </h2>

                        <p>
                            Pengajuan terbaru dari barista.
                        </p>
                    </div>

                    <a href="{{ route('admin.restock-requests.index') }}" class="dashboard-card-link">
                        Lihat Semua
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="dashboard-list">
                    @forelse($recentRestockRequests as $request)
                        @php
                            $statusClass = match ($request->status) {
                                'pending' => 'status-pending',
                                'approved' => 'status-approved',
                                'rejected' => 'status-rejected',
                                default => 'status-default',
                            };

                            $statusLabel = match ($request->status) {
                                'pending' => 'Menunggu',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                default => ucfirst($request->status),
                            };

                            $statusIcon = match ($request->status) {
                                'pending' => 'bi-clock',
                                'approved' => 'bi-check-circle',
                                'rejected' => 'bi-x-circle',
                                default => 'bi-info-circle',
                            };
                        @endphp

                        <a href="{{ route('admin.restock-requests.show', $request->id) }}" class="dashboard-list-item">
                            <div class="dashboard-list-icon dashboard-list-icon-restock">
                                <i class="bi bi-clipboard-check"></i>
                            </div>

                            <div class="dashboard-list-main">
                                <strong>
                                    {{ $request->item?->name ?? '-' }}
                                </strong>

                                <span>
                                    {{ $request->user?->name ?? '-' }}
                                    ·
                                    {{ $request->activity?->created_at?->format('d M Y H:i') ?? '-' }}
                                </span>
                            </div>

                            <div class="dashboard-request-right">
                                <strong>
                                    {{ rtrim(rtrim(number_format((float) $request->quantity, 2, ',', '.'), '0'), ',') }}
                                    {{ $request->item?->unit?->name ?? '' }}
                                </strong>

                                <span class="dashboard-status {{ $statusClass }}">
                                    <i class="bi {{ $statusIcon }}"></i>
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="dashboard-empty">
                            <div class="dashboard-empty-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </div>

                            <strong>
                                Belum ada pengajuan
                            </strong>

                            <p>
                                Belum terdapat pengajuan restock.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- =====================================================
         RECENT TRANSACTIONS
    ====================================================== --}}
        <section class="dashboard-card dashboard-recent-card">
            <div class="dashboard-card-header">
                <div>
                    <h2>
                        Transaksi Terbaru
                    </h2>

                    <p>
                        Aktivitas transaksi stok terakhir.
                    </p>
                </div>

                <a href="{{ route('admin.transactions.index') }}" class="dashboard-card-link">
                    Lihat Riwayat
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="dashboard-table-wrapper">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Stok Setelah</th>
                            <th>Dilakukan Oleh</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            @php
                                $isIncoming = $transaction->transaction_type === 'masuk';

                                $quantity = rtrim(
                                    rtrim(number_format((float) $transaction->quantity, 2, ',', '.'), '0'),
                                    ',',
                                );

                                $stockAfter = rtrim(
                                    rtrim(number_format((float) $transaction->stock_after, 2, ',', '.'), '0'),
                                    ',',
                                );
                            @endphp

                            <tr>
                                <td>
                                    <div class="dashboard-table-item">
                                        <div class="dashboard-table-icon">
                                            <i class="bi bi-box-seam"></i>
                                        </div>

                                        <div>
                                            <strong>
                                                {{ $transaction->item?->name ?? '-' }}
                                            </strong>

                                            <span>
                                                {{ $transaction->item?->unit?->name ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if ($isIncoming)
                                        <span class="transaction-type transaction-type-in">
                                            <i class="bi bi-arrow-down-left"></i>
                                            Masuk
                                        </span>
                                    @else
                                        <span class="transaction-type transaction-type-out">
                                            <i class="bi bi-arrow-up-right"></i>
                                            Keluar
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <strong>
                                        {{ $quantity }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $stockAfter }}
                                    {{ $transaction->item?->unit?->name ?? '' }}
                                </td>

                                <td>
                                    {{ $transaction->user?->name ?? '-' }}
                                </td>

                                <td>
                                    <div class="dashboard-date">
                                        <strong>
                                            {{ $transaction->activity?->created_at?->format('d M Y') ?? '-' }}
                                        </strong>

                                        <span>
                                            {{ $transaction->activity?->created_at?->format('H:i') ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="dashboard-empty dashboard-empty-table">
                                        <div class="dashboard-empty-icon">
                                            <i class="bi bi-clock-history"></i>
                                        </div>

                                        <strong>
                                            Belum ada transaksi
                                        </strong>

                                        <p>
                                            Belum terdapat transaksi stok.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const transactionCanvas = document.getElementById('transactionChart');
            const stockCategoryCanvas = document.getElementById('stockCategoryChart');

            if (transactionCanvas) {
                new Chart(transactionCanvas, {
                    type: 'bar',
                    data: {
                        labels: @json($transactionChartLabels),
                        datasets: [{
                                label: 'Barang Masuk',
                                data: @json($transactionChartIncoming),
                                borderRadius: 5,
                                maxBarThickness: 24
                            },
                            {
                                label: 'Barang Keluar',
                                data: @json($transactionChartOutgoing),
                                borderRadius: 5,
                                maxBarThickness: 24
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 18
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#8a93a1'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#edf0f3'
                                },
                                ticks: {
                                    precision: 0,
                                    color: '#8a93a1'
                                }
                            }
                        }
                    }
                });
            }

            if (stockCategoryCanvas) {
                new Chart(stockCategoryCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: @json($stockCategoryLabels),
                        datasets: [{
                            data: @json($stockCategoryValues),
                            backgroundColor: [
                                '#7b4b2a',
                                '#a66a3f',
                                '#c48a5a',
                                '#d9a979',
                                '#8b6f47',
                                '#6f4e37',
                                '#b8865b',
                                '#9c6644',
                                '#e0b589',
                                '#795548'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 14
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
