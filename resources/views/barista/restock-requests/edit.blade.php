@extends('layouts.barista')

@section('title', 'Edit Ajuan Restock')

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
                    <a href="{{ route('barista.restock-requests.show', $restockRequest->id) }}">
                        <span>Detail Ajuan</span>
                    </a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Edit Ajuan Restock</span>
                </div>

                <h1 class="page-title">Edit Ajuan Restock</h1>
                <p class="page-subtitle">
                    Perbarui data pengajuan restock sebelum diproses admin.
                </p>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('barista.restock-requests.show', $restockRequest->id) }}" class="btn btn-light">
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
                    <strong>Terdapat kesalahan pada pengisian form.</strong>
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
                    <i class="bi bi-pencil-square"></i>
                </div>

                <div>
                    <h2>Edit Pengajuan Restock</h2>
                    <p>
                        Perbarui barang, jumlah restock, atau catatan pengajuan.
                    </p>
                </div>
            </div>

            <form action="{{ route('barista.restock-requests.update', $restockRequest->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                                    <option value="{{ $item->id }}" @selected(old('item_id', $restockRequest->item_id) == $item->id)>
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
                                Hanya barang yang masih aktif yang dapat dipilih.
                            </div>
                        </div>

                        <div class="restock-form-group">
                            <label for="quantity">
                                Jumlah Restock
                                <span class="required">*</span>
                            </label>

                            <div class="restock-input-group">
                                <input type="number" name="quantity" id="quantity"
                                    value="{{ old('quantity', $restockRequest->quantity) }}" min="0.01" step="0.01"
                                    placeholder="Contoh: 10" class="@error('quantity') is-invalid @enderror" required>

                                <span id="unit-label">
                                    {{ $restockRequest->item?->unit?->name ?? 'Satuan' }}
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

                            <textarea name="note" id="note" rows="4" maxlength="1000"
                                placeholder="Tambahkan catatan atau alasan pengajuan jika diperlukan..."
                                class="@error('note') is-invalid @enderror">{{ old('note', $restockRequest->note) }}</textarea>

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
                            <strong>Status Pengajuan</strong>

                            <p>
                                Perubahan hanya dapat dilakukan selama ajuan masih
                                berstatus <strong>Pending</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="restock-form-actions">
                    <a href="{{ route('barista.restock-requests.show', $restockRequest->id) }}" class="btn btn-light">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Perubahan</span>
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
            const unitLabel = document.getElementById('unit-label');

            if (!itemSelect || !unitLabel) {
                return;
            }

            const units = @json(
                $items->mapWithKeys(function ($item) {
                    return [
                        $item->id => $item->unit?->name,
                    ];
                }));

            function updateUnit() {
                unitLabel.textContent = units[itemSelect.value] || 'Satuan';
            }

            itemSelect.addEventListener('change', updateUnit);
            updateUnit();
        });
    </script>
@endpush
