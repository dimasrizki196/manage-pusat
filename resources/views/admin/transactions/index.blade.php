@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@push('styles')
    @vite('resources/css/admin/transactions.css')
@endpush

@section('content')
    <div class="transaction-page">
        <div class="transaction-page-header">
            <div class="transaction-page-header-content">
                <div class="transaction-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Riwayat Transaksi</span>
                </div>
                <h1 class="transaction-page-title">Riwayat Transaksi</h1>
                <p class="transaction-page-subtitle">
                    Kelola dan pantau seluruh transaksi stok barang.
                </p>
            </div>
            <div class="transaction-page-header-actions">
                <button type="button" class="transaction-primary-button" onclick="openTransactionModal()">
                    <i class="bi bi-plus-lg"></i>
                    <span>Transaksi Baru</span>
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="transaction-alert transaction-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="transaction-alert transaction-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        @endif

        <div class="transaction-summary-grid">
            <div class="transaction-summary-card">
                <div class="transaction-summary-icon">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div class="transaction-summary-content">
                    <span>Total Transaksi</span>
                    <strong>{{ $transactions->total() }}</strong>
                </div>
            </div>

            <div class="transaction-summary-card">
                <div class="transaction-summary-icon transaction-icon-in">
                    <i class="bi bi-arrow-down-left"></i>
                </div>
                <div class="transaction-summary-content">
                    <span>Barang Masuk</span>
                    <strong>{{ $transactions->where('transaction_type', 'masuk')->count() }}</strong>
                </div>
            </div>

            <div class="transaction-summary-card">
                <div class="transaction-summary-icon transaction-icon-out">
                    <i class="bi bi-arrow-up-right"></i>
                </div>
                <div class="transaction-summary-content">
                    <span>Barang Keluar</span>
                    <strong>{{ $transactions->where('transaction_type', 'keluar')->count() }}</strong>
                </div>
            </div>
        </div>

        <div class="transaction-filter-card">
            <form method="GET" action="{{ route('admin.transactions.index') }}" class="transaction-filter-form">
                <div class="transaction-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang...">
                </div>

                <select name="transaction_type" class="transaction-filter-select">
                    <option value="">Semua transaksi</option>
                    <option value="masuk" @selected(request('transaction_type') === 'masuk')>
                        Barang masuk
                    </option>
                    <option value="keluar" @selected(request('transaction_type') === 'keluar')>
                        Barang keluar
                    </option>
                </select>

                <button type="submit" class="transaction-filter-button">
                    <i class="bi bi-funnel"></i>
                    <span>Filter</span>
                </button>

                @if (request()->hasAny(['search', 'transaction_type']))
                    <a href="{{ route('admin.transactions.index') }}" class="transaction-reset-button">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="transaction-table-card">
            <div class="transaction-table-header">
                <div>
                    <h2>Data Transaksi</h2>
                    <p>Histori perubahan stok barang.</p>
                </div>
                <span class="transaction-table-total">
                    {{ $transactions->total() }} transaksi
                </span>
            </div>

            <div class="transaction-table-wrapper">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th width="55">NO</th>
                            <th>BARANG</th>
                            <th>JENIS</th>
                            <th>JUMLAH</th>
                            <th>STOK SEBELUM</th>
                            <th>STOK SESUDAH</th>
                            <th>DILAKUKAN OLEH</th>
                            <th>WAKTU</th>
                            <th width="60">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="transaction-number">
                                    {{ $transactions->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <div class="transaction-item">
                                        <div class="transaction-item-icon">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <div class="transaction-item-content">
                                            <strong>{{ $transaction->item->name }}</strong>
                                            <span>{{ $transaction->item->category->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if ($transaction->transaction_type === 'masuk')
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
                                    <div class="transaction-quantity">
                                        <strong>
                                            {{ rtrim(rtrim(number_format($transaction->quantity, 2, ',', '.'), '0'), ',') }}
                                        </strong>
                                        <span class="transaction-unit">
                                            {{ $transaction->item->unit->name ?? '' }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span class="transaction-stock-value">
                                        {{ rtrim(rtrim(number_format($transaction->stock_before, 2, ',', '.'), '0'), ',') }}
                                    </span>
                                </td>

                                <td>
                                    <strong class="transaction-stock-after">
                                        {{ rtrim(rtrim(number_format($transaction->stock_after, 2, ',', '.'), '0'), ',') }}
                                    </strong>
                                </td>

                                <td>
                                    <div class="transaction-user">
                                        <div class="transaction-user-avatar">
                                            {{ strtoupper(substr($transaction->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span>{{ $transaction->user->name ?? '-' }}</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="transaction-date">
                                        <strong>
                                            {{ $transaction->activity?->created_at?->format('d-m-Y') ?? '-' }}
                                        </strong>
                                        <span>
                                            {{ $transaction->activity?->created_at?->format('H:i') ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <a href="{{ route('admin.transactions.show', $transaction) }}"
                                        class="transaction-detail-button" title="Lihat detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="transaction-empty">
                                        <div class="transaction-empty-icon">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </div>
                                        <strong>Belum ada transaksi</strong>
                                        <span>Belum terdapat riwayat transaksi stok.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="transaction-pagination">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="transactionModal" class="transaction-modal" aria-hidden="true">
        <div class="transaction-modal-backdrop" onclick="closeTransactionModal()"></div>

        <div class="transaction-modal-dialog">
            <div class="transaction-modal-header">
                <div>
                    <div class="transaction-modal-title-icon">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <div>
                        <h3>Transaksi Stok</h3>
                        <p>Catat barang masuk atau keluar.</p>
                    </div>
                </div>

                <button type="button" onclick="closeTransactionModal()" class="transaction-modal-close"
                    aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.transactions.store') }}" class="transaction-form">
                @csrf

                <div class="transaction-form-group">
                    <label for="transaction_item">Barang</label>

                    <select id="transaction_item" name="item_id" onchange="updateTransactionStock()" required>
                        <option value="">Pilih barang</option>

                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" data-stock="{{ $item->stock }}"
                                data-unit="{{ $item->unit->name ?? '' }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="transaction-current-stock">
                    <div class="transaction-current-stock-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>

                    <div>
                        <span>Stok saat ini</span>
                        <strong id="currentStock">-</strong>
                        <small id="currentStockUnit"></small>
                    </div>
                </div>

                <div class="transaction-form-group">
                    <label>Jenis Transaksi</label>

                    <div class="transaction-type-options">
                        <label class="transaction-type-option">
                            <input type="radio" name="transaction_type" value="masuk" checked>

                            <span>
                                <i class="bi bi-arrow-down-left"></i>
                                <strong>Barang Masuk</strong>
                                <small>Menambah stok</small>
                            </span>
                        </label>

                        <label class="transaction-type-option">
                            <input type="radio" name="transaction_type" value="keluar">

                            <span>
                                <i class="bi bi-arrow-up-right"></i>
                                <strong>Barang Keluar</strong>
                                <small>Mengurangi stok</small>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="transaction-form-group">
                    <label for="transaction_quantity">Jumlah</label>

                    <div class="transaction-quantity-wrapper">
                        <input type="number" id="transaction_quantity" name="quantity" min="0.01" step="0.01"
                            placeholder="Contoh: 10" required>

                        <span id="quantityUnit"></span>
                    </div>

                    <small id="quantityWarning" class="transaction-quantity-warning"></small>
                </div>

                <div class="transaction-modal-actions">
                    <button type="button" class="transaction-cancel-button" onclick="closeTransactionModal()">
                        Batal
                    </button>

                    <button type="submit" class="transaction-submit-button">
                        <i class="bi bi-check-lg"></i>
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const transactionModal = document.getElementById('transactionModal');
        const itemSelect = document.getElementById('transaction_item');
        const currentStock = document.getElementById('currentStock');
        const currentStockUnit = document.getElementById('currentStockUnit');
        const quantityUnit = document.getElementById('quantityUnit');
        const quantityInput = document.getElementById('transaction_quantity');
        const quantityWarning = document.getElementById('quantityWarning');

        function openTransactionModal() {
            transactionModal.classList.add('show');
            transactionModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        }

        function closeTransactionModal() {
            transactionModal.classList.remove('show');
            transactionModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
        }

        function updateTransactionStock() {
            const option = itemSelect.options[itemSelect.selectedIndex];

            if (!option || !option.value) {
                currentStock.textContent = '-';
                currentStockUnit.textContent = '';
                quantityUnit.textContent = '';
                validateTransactionQuantity();
                return;
            }

            const stock = option.dataset.stock;
            const unit = option.dataset.unit;

            currentStock.textContent = stock;
            currentStockUnit.textContent = unit;
            quantityUnit.textContent = unit;

            validateTransactionQuantity();
        }

        function validateTransactionQuantity() {
            const selectedType = document.querySelector(
                'input[name="transaction_type"]:checked'
            )?.value;

            const stock = parseFloat(
                itemSelect.options[itemSelect.selectedIndex]?.dataset.stock || 0
            );

            const quantity = parseFloat(quantityInput.value || 0);

            if (selectedType === 'keluar' && quantity > stock) {
                quantityWarning.textContent =
                    'Jumlah barang keluar melebihi stok yang tersedia.';
                quantityWarning.classList.add('show');
                return false;
            }

            quantityWarning.textContent = '';
            quantityWarning.classList.remove('show');

            return true;
        }

        quantityInput.addEventListener('input', validateTransactionQuantity);

        document.querySelectorAll('input[name="transaction_type"]').forEach(function(radio) {
            radio.addEventListener('change', validateTransactionQuantity);
        });

        document.addEventListener('keydown', function(event) {
            if (
                event.key === 'Escape' &&
                transactionModal.classList.contains('show')
            ) {
                closeTransactionModal();
            }
        });
    </script>
@endpush
