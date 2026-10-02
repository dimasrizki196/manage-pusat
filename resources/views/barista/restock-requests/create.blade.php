@extends('layouts.barista')

@section('title', 'Ajukan Restock')

@push('styles')
    @vite('resources/css/barista/restock-requests.css')
@endpush

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-breadcrumb">
                <a href="{{ route('barista.restock-requests.index') }}">
                    <i class="bi bi-clipboard-check"></i>
                    Pengajuan Restock
                </a>
                <i class="bi bi-chevron-right"></i>
                <span>Ajukan Restock</span>
            </div>
            <h1 class="page-title">Ajukan Restock</h1>
            <p class="page-subtitle">Buat pengajuan penambahan stok barang kepada admin.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('barista.restock-requests.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-circle-fill"></i>
                <strong>Terdapat kesalahan pada pengisian form.</strong>
            </div>
            <ul class="mb-0 ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="restock-form-header">
                <div class="restock-form-icon">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>
                <div>
                    <h5>Form Pengajuan Restock</h5>
                    <p>Isi data barang dan jumlah stok yang dibutuhkan.</p>
                </div>
            </div>

            <form action="{{ route('barista.restock-requests.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="item_id" class="form-label">
                            Barang <span class="text-danger">*</span>
                        </label>

                        <select name="item_id" id="item_id" class="form-select @error('item_id') is-invalid @enderror"
                            required>
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
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Hanya barang yang masih aktif yang dapat diajukan.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="quantity" class="form-label">
                            Jumlah Restock <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input type="number" name="quantity" id="quantity"
                                class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}"
                                min="0.01" step="0.01" placeholder="Contoh: 10" required>
                            <span class="input-group-text" id="quantity-unit">Satuan</span>
                        </div>

                        @error('quantity')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="note" class="form-label">
                            Catatan
                        </label>

                        <textarea name="note" id="note" rows="4" maxlength="1000"
                            class="form-control @error('note') is-invalid @enderror"
                            placeholder="Tambahkan catatan atau alasan pengajuan jika diperlukan...">{{ old('note') }}</textarea>

                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Maksimal 1000 karakter.
                        </div>
                    </div>
                </div>

                <div class="restock-form-info">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        <strong>Informasi Pengajuan</strong>
                        <p>
                            Pengajuan ini belum menambah stok barang.
                            Stok hanya akan bertambah setelah pengajuan disetujui dan diproses oleh admin.
                        </p>
                    </div>
                </div>

                <div class="restock-form-actions">
                    <a href="{{ route('barista.restock-requests.index') }}" class="btn btn-light">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i>
                        Ajukan Restock
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

            if (!itemSelect || !quantityUnit) return;

            const items = @json(
                $items->mapWithKeys(function ($item) {
                    return [$item->id => $item->unit?->name];
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
