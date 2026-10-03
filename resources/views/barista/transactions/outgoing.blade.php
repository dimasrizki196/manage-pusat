@extends('layouts.barista')

@section('title', 'Barang Keluar')

@section('content')
    <div class="page-container transaction-page">
        <div class="page-header">
            <div>
                <div class="breadcrumb">
                    <a href="{{ route('barista.dashboard') }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Barang Keluar</span>
                </div>
                <h1>Barang Keluar</h1>
                <p>Catat pengeluaran barang dari stok.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success transaction-alert">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger transaction-alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>
                    <strong>Data belum dapat disimpan.</strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="transaction-layout">
            <div class="transaction-main-card">
                <div class="transaction-card-header">
                    <div>
                        <h2>Catat Barang Keluar</h2>
                        <p>Catat barang yang digunakan atau dikeluarkan dari stok.</p>
                    </div>

                    <div class="transaction-header-icon outgoing-header-icon">
                        <i class="bi bi-box-arrow-up"></i>
                    </div>
                </div>

                <form action="{{ route('barista.transactions.outgoing.store') }}" method="POST"
                    id="outgoingTransactionForm">
                    @csrf

                    <div class="transaction-form-body">
                        <div class="form-section">
                            <div class="form-section-title">
                                <span class="section-number">1</span>

                                <div>
                                    <h3>Pilih Barang</h3>
                                    <p>Pilih barang yang akan dikeluarkan dari stok.</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="item_id">
                                    Barang
                                    <span class="required">*</span>
                                </label>

                                <select name="item_id" id="item_id"
                                    class="form-control @error('item_id') is-invalid @enderror">
                                    <option value="">Pilih barang</option>

                                    @forelse ($items as $item)
                                        <option value="{{ $item->id }}" data-stock="{{ $item->stock }}"
                                            data-unit="{{ $item->unit?->name }}"
                                            {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>
                                            Tidak ada barang dengan stok tersedia
                                        </option>
                                    @endforelse
                                </select>

                                @error('item_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="selected-item-info" id="selectedItemInfo">
                                <div class="selected-item-icon">
                                    <i class="bi bi-box-seam"></i>
                                </div>

                                <div class="selected-item-content">
                                    <span>Barang dipilih</span>
                                    <strong id="selectedItemName">-</strong>
                                </div>

                                <div class="selected-item-stock">
                                    <span>Stok tersedia</span>
                                    <strong id="selectedItemStock">-</strong>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">
                                <span class="section-number">2</span>

                                <div>
                                    <h3>Jumlah Barang</h3>
                                    <p>Masukkan jumlah barang yang akan dikeluarkan.</p>
                                </div>
                            </div>

                            <div class="form-group quantity-group">
                                <label for="quantity">
                                    Jumlah Keluar
                                    <span class="required">*</span>
                                </label>

                                <div class="quantity-input-wrapper">
                                    <input type="number" name="quantity" id="quantity"
                                        class="form-control @error('quantity') is-invalid @enderror"
                                        value="{{ old('quantity') }}" min="0.01" step="0.01" placeholder="0"
                                        autocomplete="off">

                                    <span class="quantity-unit" id="quantityUnit">
                                        Unit
                                    </span>
                                </div>

                                @error('quantity')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="quantity-warning" id="quantityWarning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <span>
                                        Jumlah barang keluar melebihi stok yang tersedia.
                                    </span>
                                </div>
                            </div>

                            <div class="stock-preview" id="stockPreview">
                                <div class="stock-preview-icon outgoing-preview-icon">
                                    <i class="bi bi-box-seam"></i>
                                </div>

                                <div class="stock-preview-content">
                                    <span>Stok saat ini</span>
                                    <strong id="currentStock">-</strong>
                                </div>

                                <div class="stock-preview-arrow">
                                    <i class="bi bi-arrow-right"></i>
                                </div>

                                <div class="stock-preview-content">
                                    <span>Stok setelah keluar</span>
                                    <strong id="stockAfter">-</strong>
                                </div>
                            </div>
                        </div>

                        <div class="transaction-note">
                            <div class="transaction-note-icon">
                                <i class="bi bi-info-circle"></i>
                            </div>

                            <div>
                                <strong>Perhatian</strong>

                                <p>
                                    Barang keluar tidak dapat melebihi stok yang tersedia.
                                    Pastikan jumlah yang dicatat sesuai dengan barang yang benar-benar dikeluarkan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="transaction-form-footer">
                        <a href="{{ route('barista.dashboard') }}" class="btn btn-light">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-danger" id="submitOutgoing" disabled>
                            <i class="bi bi-check-lg"></i>
                            Catat Barang Keluar
                        </button>
                    </div>
                </form>
            </div>

            <aside class="transaction-side-card">
                <div class="side-card-header">
                    <div class="side-card-icon outgoing-side-icon">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>

                    <div>
                        <h3>Transaksi</h3>
                        <p>Pilih jenis transaksi yang ingin dicatat.</p>
                    </div>
                </div>

                <div class="transaction-navigation">
                    <a href="{{ route('barista.transactions.incoming') }}" class="transaction-nav-item">
                        <span class="transaction-nav-icon incoming">
                            <i class="bi bi-box-arrow-in-down"></i>
                        </span>

                        <span>
                            <strong>Barang Masuk</strong>
                            <small>Penerimaan barang</small>
                        </span>

                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <a href="{{ route('barista.transactions.outgoing') }}" class="transaction-nav-item active">
                        <span class="transaction-nav-icon outgoing">
                            <i class="bi bi-box-arrow-up"></i>
                        </span>

                        <span>
                            <strong>Barang Keluar</strong>
                            <small>Pengeluaran barang</small>
                        </span>

                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                <div class="side-card-divider"></div>

                <div class="source-guide">
                    <h4>
                        <i class="bi bi-lightbulb"></i>
                        Panduan
                    </h4>

                    <div class="guide-item">
                        <span class="guide-badge outgoing">
                            <i class="bi bi-box-arrow-up"></i>
                        </span>

                        <p>
                            <strong>Barang Keluar</strong>
                            Gunakan untuk mencatat barang yang digunakan atau dikeluarkan dari stok.
                        </p>
                    </div>

                    <div class="guide-item">
                        <span class="guide-badge warning">
                            <i class="bi bi-exclamation-triangle"></i>
                        </span>

                        <p>
                            <strong>Periksa Stok</strong>
                            Jumlah barang keluar tidak boleh melebihi stok yang tersedia.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('outgoingTransactionForm');
            const itemSelect = document.getElementById('item_id');
            const quantityInput = document.getElementById('quantity');
            const quantityUnit = document.getElementById('quantityUnit');
            const selectedItemInfo = document.getElementById('selectedItemInfo');
            const selectedItemName = document.getElementById('selectedItemName');
            const selectedItemStock = document.getElementById('selectedItemStock');
            const stockPreview = document.getElementById('stockPreview');
            const currentStock = document.getElementById('currentStock');
            const stockAfter = document.getElementById('stockAfter');
            const quantityWarning = document.getElementById('quantityWarning');
            const submitButton = document.getElementById('submitOutgoing');

            let availableStock = 0;

            const formatNumber = function(value) {
                const number = Number(value);

                if (Number.isNaN(number)) {
                    return '-';
                }

                return new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 2
                }).format(number);
            };

            const updateItem = function() {
                const selectedOption = itemSelect.options[itemSelect.selectedIndex];

                if (!selectedOption || !selectedOption.value) {
                    availableStock = 0;

                    selectedItemInfo.classList.remove('show');
                    stockPreview.classList.remove('show');

                    selectedItemName.textContent = '-';
                    selectedItemStock.textContent = '-';
                    currentStock.textContent = '-';
                    stockAfter.textContent = '-';
                    quantityUnit.textContent = 'Unit';

                    validateQuantity();

                    return;
                }

                availableStock = Number(selectedOption.dataset.stock || 0);

                selectedItemName.textContent = selectedOption.textContent.trim();
                selectedItemStock.textContent = formatNumber(availableStock);
                currentStock.textContent = formatNumber(availableStock);
                quantityUnit.textContent = selectedOption.dataset.unit || 'Unit';

                selectedItemInfo.classList.add('show');
                stockPreview.classList.add('show');

                updateStockPreview();
            };

            const updateStockPreview = function() {
                const quantity = Number(quantityInput.value || 0);

                if (!itemSelect.value) {
                    return;
                }

                const remainingStock = availableStock - quantity;

                currentStock.textContent = formatNumber(availableStock);

                if (quantity > 0) {
                    stockAfter.textContent = formatNumber(
                        Math.max(remainingStock, 0)
                    );
                } else {
                    stockAfter.textContent = formatNumber(availableStock);
                }

                validateQuantity();
            };

            const validateQuantity = function() {
                const quantity = Number(quantityInput.value || 0);
                const hasItem = Boolean(itemSelect.value);
                const hasQuantity = quantity > 0;
                const exceedsStock = hasItem && hasQuantity && quantity > availableStock;

                if (exceedsStock) {
                    quantityInput.classList.add('is-invalid');
                    quantityWarning.classList.add('show');
                    submitButton.disabled = true;
                    return;
                }

                quantityWarning.classList.remove('show');

                if (quantityInput.value && hasQuantity) {
                    quantityInput.classList.remove('is-invalid');
                }

                submitButton.disabled = !(
                    hasItem &&
                    hasQuantity &&
                    quantity <= availableStock
                );
            };

            itemSelect.addEventListener('change', function() {
                updateItem();
            });

            quantityInput.addEventListener('input', function() {
                updateStockPreview();
            });

            form.addEventListener('submit', function(event) {
                const quantity = Number(quantityInput.value || 0);

                if (
                    !itemSelect.value ||
                    quantity <= 0 ||
                    quantity > availableStock
                ) {
                    event.preventDefault();
                    validateQuantity();
                    return;
                }

                submitButton.disabled = true;

                submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            Menyimpan...
        `;
            });

            updateItem();

            if (quantityInput.value) {
                updateStockPreview();
            }
        });
    </script>
@endpush
