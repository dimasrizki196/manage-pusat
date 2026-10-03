@extends('layouts.barista')

@section('title', 'Barang Masuk')

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
                    <span>Barang Masuk</span>
                </div>
                <h1>Barang Masuk</h1>
                <p>Catat penerimaan barang yang masuk ke dalam stok.</p>
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
                        <h2>Catat Barang Masuk</h2>
                        <p>Pilih sumber barang sebelum mengisi data penerimaan.</p>
                    </div>
                    <div class="transaction-header-icon">
                        <i class="bi bi-box-arrow-in-down"></i>
                    </div>
                </div>

                <form action="{{ route('barista.transactions.incoming.store') }}" method="POST"
                    id="incomingTransactionForm">
                    @csrf

                    <div class="transaction-form-body">
                        <div class="form-section">
                            <div class="form-section-title">
                                <span class="section-number">1</span>
                                <div>
                                    <h3>Sumber Barang</h3>
                                    <p>Tentukan asal barang yang diterima.</p>
                                </div>
                            </div>

                            <div class="source-options">
                                <label class="source-option" for="source-pengajuan">
                                    <input type="radio" name="transaction_source" id="source-pengajuan" value="pengajuan"
                                        {{ old('transaction_source', 'pengajuan') === 'pengajuan' ? 'checked' : '' }}>

                                    <span class="source-option-content">
                                        <span class="source-option-icon source-icon-request">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </span>

                                        <span class="source-option-text">
                                            <strong>Dari Pengajuan</strong>
                                            <small>Barang berasal dari pengajuan restock yang sudah disetujui.</small>
                                        </span>
                                    </span>

                                    <span class="source-option-check">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </span>
                                </label>

                                <label class="source-option" for="source-urgent">
                                    <input type="radio" name="transaction_source" id="source-urgent" value="urgent"
                                        {{ old('transaction_source') === 'urgent' ? 'checked' : '' }}>

                                    <span class="source-option-content">
                                        <span class="source-option-icon source-icon-urgent">
                                            <i class="bi bi-lightning-charge"></i>
                                        </span>

                                        <span class="source-option-text">
                                            <strong>Urgent</strong>
                                            <small>Barang masuk langsung tanpa melalui pengajuan restock.</small>
                                        </span>
                                    </span>

                                    <span class="source-option-check">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-section" id="requestSection">
                            <div class="form-section-title">
                                <span class="section-number">2</span>
                                <div>
                                    <h3>Pengajuan Restock</h3>
                                    <p>Pilih pengajuan yang sudah disetujui.</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="restock_request_id">
                                    Pengajuan Restock
                                    <span class="required">*</span>
                                </label>

                                <select name="restock_request_id" id="restock_request_id"
                                    class="form-control @error('restock_request_id') is-invalid @enderror">
                                    <option value="">Pilih pengajuan</option>

                                    @forelse ($approvedRequests as $request)
                                        <option value="{{ $request->id }}" data-item-id="{{ $request->item_id }}"
                                            data-item-name="{{ $request->item?->name }}"
                                            data-item-unit="{{ $request->item?->unit?->name }}"
                                            data-request-quantity="{{ $request->quantity }}"
                                            {{ old('restock_request_id') == $request->id ? 'selected' : '' }}>
                                            {{ $request->item?->name }}
                                            - {{ number_format($request->quantity, 2, ',', '.') }}
                                            {{ $request->item?->unit?->name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>
                                            Belum ada pengajuan yang disetujui
                                        </option>
                                    @endforelse
                                </select>

                                @error('restock_request_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="request-info" id="requestInfo">
                                <div class="request-info-icon">
                                    <i class="bi bi-info-circle"></i>
                                </div>

                                <div>
                                    <strong>Informasi Pengajuan</strong>
                                    <p>
                                        Jumlah diterima tidak harus sama dengan jumlah yang diajukan.
                                        Isi sesuai jumlah barang yang benar-benar diterima.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">
                                <span class="section-number">3</span>
                                <div>
                                    <h3>Data Barang</h3>
                                    <p>Periksa barang dan masukkan jumlah yang diterima.</p>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group form-group-wide">
                                    <label for="item_id">
                                        Barang
                                        <span class="required">*</span>
                                    </label>

                                    <select name="item_id" id="item_id"
                                        class="form-control @error('item_id') is-invalid @enderror">
                                        <option value="">Pilih barang</option>

                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" data-stock="{{ $item->stock }}"
                                                data-unit="{{ $item->unit?->name }}"
                                                {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('item_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="quantity">
                                        Jumlah Diterima
                                        <span class="required">*</span>
                                    </label>

                                    <div class="quantity-input-wrapper">
                                        <input type="number" name="quantity" id="quantity"
                                            class="form-control @error('quantity') is-invalid @enderror"
                                            value="{{ old('quantity') }}" min="0.01" step="0.01" placeholder="0"
                                            autocomplete="off">

                                        <span class="quantity-unit" id="quantityUnit">Unit</span>
                                    </div>

                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="stock-preview" id="stockPreview">
                                <div class="stock-preview-icon">
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
                                    <span>Stok setelah masuk</span>
                                    <strong id="stockAfter">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="transaction-form-footer">
                        <a href="{{ route('barista.dashboard') }}" class="btn btn-light">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary" id="submitIncoming">
                            <i class="bi bi-check-lg"></i>
                            Catat Barang Masuk
                        </button>
                    </div>
                </form>
            </div>

            <aside class="transaction-side-card">
                <div class="side-card-header">
                    <div class="side-card-icon">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>

                    <div>
                        <h3>Transaksi</h3>
                        <p>Pilih jenis transaksi yang ingin dicatat.</p>
                    </div>
                </div>

                <div class="transaction-navigation">
                    <a href="{{ route('barista.transactions.incoming') }}" class="transaction-nav-item active">
                        <span class="transaction-nav-icon incoming">
                            <i class="bi bi-box-arrow-in-down"></i>
                        </span>

                        <span>
                            <strong>Barang Masuk</strong>
                            <small>Penerimaan barang</small>
                        </span>

                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <a href="{{ route('barista.transactions.outgoing') }}" class="transaction-nav-item">
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
                        <span class="guide-badge request">
                            <i class="bi bi-file-earmark-text"></i>
                        </span>
                        <p>
                            <strong>Dari Pengajuan</strong>
                            Gunakan jika barang berasal dari pengajuan restock yang sudah disetujui.
                        </p>
                    </div>

                    <div class="guide-item">
                        <span class="guide-badge urgent">
                            <i class="bi bi-lightning-charge"></i>
                        </span>
                        <p>
                            <strong>Urgent</strong>
                            Gunakan untuk barang yang masuk tanpa melalui pengajuan restock.
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
            const form = document.getElementById('incomingTransactionForm');
            const sourceInputs = document.querySelectorAll('input[name="transaction_source"]');
            const requestSection = document.getElementById('requestSection');
            const requestSelect = document.getElementById('restock_request_id');
            const itemSelect = document.getElementById('item_id');
            const quantityInput = document.getElementById('quantity');
            const quantityUnit = document.getElementById('quantityUnit');
            const currentStock = document.getElementById('currentStock');
            const stockAfter = document.getElementById('stockAfter');
            const stockPreview = document.getElementById('stockPreview');
            const submitButton = document.getElementById('submitIncoming');

            const formatNumber = function(value) {
                const number = Number(value);

                if (Number.isNaN(number)) {
                    return '-';
                }

                return new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 2
                }).format(number);
            };

            const getSelectedSource = function() {
                const selected = document.querySelector(
                    'input[name="transaction_source"]:checked'
                );

                return selected ? selected.value : null;
            };

            const updateItemUnit = function() {
                const selectedOption = itemSelect.options[itemSelect.selectedIndex];

                if (!selectedOption || !selectedOption.value) {
                    quantityUnit.textContent = 'Unit';
                    return;
                }

                quantityUnit.textContent = selectedOption.dataset.unit || 'Unit';
            };

            const updateStockPreview = function() {
                const selectedOption = itemSelect.options[itemSelect.selectedIndex];

                if (!selectedOption || !selectedOption.value) {
                    stockPreview.classList.remove('show');
                    currentStock.textContent = '-';
                    stockAfter.textContent = '-';
                    return;
                }

                const stock = Number(selectedOption.dataset.stock || 0);
                const quantity = Number(quantityInput.value || 0);

                currentStock.textContent = formatNumber(stock);
                stockAfter.textContent = formatNumber(stock + quantity);
                stockPreview.classList.add('show');
            };

            const setItemFromRequest = function() {
                const selectedOption = requestSelect.options[
                    requestSelect.selectedIndex
                ];

                if (!selectedOption || !selectedOption.value) {
                    itemSelect.value = '';
                    updateItemUnit();
                    updateStockPreview();
                    return;
                }

                const itemId = selectedOption.dataset.itemId;

                if (itemId) {
                    itemSelect.value = itemId;
                }

                updateItemUnit();
                updateStockPreview();
            };

            const toggleSource = function() {
                const source = getSelectedSource();

                if (source === 'pengajuan') {
                    requestSection.classList.remove('hidden-section');
                    requestSelect.disabled = false;
                    itemSelect.disabled = true;
                    itemSelect.classList.add('auto-filled');
                    setItemFromRequest();
                } else {
                    requestSection.classList.add('hidden-section');
                    requestSelect.disabled = true;
                    requestSelect.value = '';
                    itemSelect.disabled = false;
                    itemSelect.classList.remove('auto-filled');
                    updateItemUnit();
                    updateStockPreview();
                }
            };

            sourceInputs.forEach(function(input) {
                input.addEventListener('change', toggleSource);
            });

            requestSelect.addEventListener('change', function() {
                setItemFromRequest();
            });

            itemSelect.addEventListener('change', function() {
                updateItemUnit();
                updateStockPreview();
            });

            quantityInput.addEventListener('input', updateStockPreview);

            form.addEventListener('submit', function() {
                itemSelect.disabled = false;
                requestSelect.disabled = false;

                submitButton.disabled = true;
                submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            Menyimpan...
        `;
            });

            toggleSource();
        });
    </script>
@endpush
