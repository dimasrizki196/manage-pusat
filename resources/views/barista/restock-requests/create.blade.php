@extends('layouts.barista')

@section('title', 'Ajukan Restock')

@section('content')
    <div class="page-container restock-page-barista">
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-breadcrumb">
                    <a href="{{ route('barista.restock-requests.index') }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Pengajuan Restock</span>
                    </a>

                    <i class="bi bi-chevron-right"></i>

                    <span>Ajukan Restock</span>
                </div>

                <h1 class="page-title">Ajukan Restock</h1>

                <p class="page-subtitle">
                    Buat pengajuan penambahan stok barang kepada admin.
                </p>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('barista.restock-requests.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        @if (session('error'))
            <div class="restock-alert restock-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="restock-validation-alert">
                <div class="restock-validation-header">
                    <i class="bi bi-exclamation-circle-fill"></i>

                    <strong>
                        Terdapat kesalahan pada pengisian form.
                    </strong>
                </div>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="restock-form-card">
            <div class="restock-form-header">
                <div class="restock-form-icon">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>

                <div>
                    <h2>Form Pengajuan Restock</h2>

                    <p>
                        Isi data barang dan jumlah stok yang dibutuhkan.
                    </p>
                </div>
            </div>

            <form action="{{ route('barista.restock-requests.store') }}" method="POST">
                @csrf

                <div class="restock-form-body">
                    <div class="restock-form-grid">
                        <div class="restock-form-group">
                            <label for="item_id">
                                Barang
                                <span class="required">*</span>
                            </label>

                            <select name="item_id" id="item_id" class="@error('item_id') is-invalid @enderror" required>
                                <option value="">Pilih barang</option>

                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                                        {{ $item->name }}

                                        @if ($item->unit)
                                            - {{ $item->unit->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @error('item_id')
                                <div class="restock-field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="restock-field-help">
                                Hanya barang yang masih aktif yang dapat diajukan.
                            </div>
                        </div>

                        <div class="restock-form-group">
                            <label for="quantity">
                                Jumlah Restock
                                <span class="required">*</span>
                            </label>

                            <div class="restock-input-group">
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}"
                                    min="0.01" step="0.01" placeholder="Contoh: 10"
                                    class="@error('quantity') is-invalid @enderror" required>

                                <span id="quantity-unit">
                                    Satuan
                                </span>
                            </div>

                            @error('quantity')
                                <div class="restock-field-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="restock-form-group restock-form-group-full">
                            <label for="note">
                                Catatan
                            </label>

                            <textarea name="note" id="note" rows="4" maxlength="1000" class="@error('note') is-invalid @enderror"
                                placeholder="Tambahkan catatan atau alasan pengajuan jika diperlukan...">{{ old('note') }}</textarea>

                            @error('note')
                                <div class="restock-field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="restock-field-help">
                                Maksimal 1000 karakter.
                            </div>
                        </div>
                    </div>

                    <div class="restock-form-info">
                        <div class="restock-form-info-icon">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>

                        <div>
                            <strong>Informasi Pengajuan</strong>

                            <p>
                                Pengajuan ini belum menambah stok barang.
                                Stok hanya akan bertambah setelah barang benar-benar
                                diterima dan dicatat sebagai barang masuk.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="restock-form-actions">
                    <a href="{{ route('barista.restock-requests.index') }}" class="btn btn-light">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i>
                        <span>Ajukan Restock</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemSelect = document.getElementById('item_id');
            const quantityUnit = document.getElementById('quantity-unit');

            if (!itemSelect || !quantityUnit) {
                return;
            }

            const items = @json(
                $items->mapWithKeys(function ($item) {
                    return [
                        $item->id => $item->unit?->name,
                    ];
                }));

            function updateUnit() {
                const unit = items[itemSelect.value];

                quantityUnit.textContent = unit || 'Satuan';
            }

            itemSelect.addEventListener('change', updateUnit);

            updateUnit();
        });
    </script>
    @endpush@extends('layouts.barista')

    @section('title', 'Ajukan Restock')

    @push('styles')
        @vite('resources/css/barista/restock-requests.css')
    @endpush

@section('content')
    <div class="page-container restock-page">
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-breadcrumb">
                    <a href="{{ route('barista.restock-requests.index') }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Pengajuan Restock</span>
                    </a>

                    <i class="bi bi-chevron-right"></i>

                    <span>Ajukan Restock</span>
                </div>

                <h1 class="page-title">Ajukan Restock</h1>

                <p class="page-subtitle">
                    Buat pengajuan penambahan stok barang kepada admin.
                </p>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('barista.restock-requests.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        @if (session('error'))
            <div class="restock-alert restock-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="restock-validation-alert">
                <div class="restock-validation-header">
                    <i class="bi bi-exclamation-circle-fill"></i>

                    <strong>
                        Terdapat kesalahan pada pengisian form.
                    </strong>
                </div>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="restock-form-card">
            <div class="restock-form-header">
                <div class="restock-form-icon">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>

                <div>
                    <h2>Form Pengajuan Restock</h2>

                    <p>
                        Isi data barang dan jumlah stok yang dibutuhkan.
                    </p>
                </div>
            </div>

            <form action="{{ route('barista.restock-requests.store') }}" method="POST">
                @csrf

                <div class="restock-form-body">
                    <div class="restock-form-grid">
                        <div class="restock-form-group">
                            <label for="item_id">
                                Barang
                                <span class="required">*</span>
                            </label>

                            <select name="item_id" id="item_id" class="@error('item_id') is-invalid @enderror" required>
                                <option value="">Pilih barang</option>

                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                                        {{ $item->name }}

                                        @if ($item->unit)
                                            - {{ $item->unit->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @error('item_id')
                                <div class="restock-field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="restock-field-help">
                                Hanya barang yang masih aktif yang dapat diajukan.
                            </div>
                        </div>

                        <div class="restock-form-group">
                            <label for="quantity">
                                Jumlah Restock
                                <span class="required">*</span>
                            </label>

                            <div class="restock-input-group">
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}"
                                    min="0.01" step="0.01" placeholder="Contoh: 10"
                                    class="@error('quantity') is-invalid @enderror" required>

                                <span id="quantity-unit">
                                    Satuan
                                </span>
                            </div>

                            @error('quantity')
                                <div class="restock-field-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="restock-form-group restock-form-group-full">
                            <label for="note">
                                Catatan
                            </label>

                            <textarea name="note" id="note" rows="4" maxlength="1000" class="@error('note') is-invalid @enderror"
                                placeholder="Tambahkan catatan atau alasan pengajuan jika diperlukan...">{{ old('note') }}</textarea>

                            @error('note')
                                <div class="restock-field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="restock-field-help">
                                Maksimal 1000 karakter.
                            </div>
                        </div>
                    </div>

                    <div class="restock-form-info">
                        <div class="restock-form-info-icon">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>

                        <div>
                            <strong>Informasi Pengajuan</strong>

                            <p>
                                Pengajuan ini belum menambah stok barang.
                                Stok hanya akan bertambah setelah barang benar-benar
                                diterima dan dicatat sebagai barang masuk.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="restock-form-actions">
                    <a href="{{ route('barista.restock-requests.index') }}" class="btn btn-light">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i>
                        <span>Ajukan Restock</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemSelect = document.getElementById('item_id');
            const quantityUnit = document.getElementById('quantity-unit');

            if (!itemSelect || !quantityUnit) {
                return;
            }

            const items = @json(
                $items->mapWithKeys(function ($item) {
                    return [
                        $item->id => $item->unit?->name,
                    ];
                }));

            function updateUnit() {
                const unit = items[itemSelect.value];

                quantityUnit.textContent = unit || 'Satuan';
            }

            itemSelect.addEventListener('change', updateUnit);

            updateUnit();
        });
    </script>
@endpush
