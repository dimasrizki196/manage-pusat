@extends('layouts.barista')

@section('title', 'Edit Ajuan Restock')
@push('styles')
    @vite('resources/css/barista/restock-requests.css')
@endpush

@section('content')
    <div class="restock-form-page">
        <div class="form-header">
            <div>
                <a href="{{ route('barista.restock-requests.show', $restockRequest->id) }}" class="back-link">
                    ← Kembali
                </a>

                <h1>Edit Ajuan Restock</h1>
                <p>Perbarui data pengajuan restock sebelum diproses admin.</p>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('barista.restock-requests.update', $restockRequest->id) }}" method="POST" class="restock-form">
            @csrf
            @method('PUT')

            <div class="form-card">
                <div class="form-group">
                    <label for="item_id">Barang</label>

                    <select name="item_id" id="item_id" class="@error('item_id') is-invalid @enderror" required>
                        <option value="">Pilih barang</option>

                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @selected(old('item_id', $restockRequest->item_id) == $item->id)>
                                {{ $item->name }}
                                @if ($item->category)
                                    — {{ $item->category->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @error('item_id')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="quantity">Jumlah Restock</label>

                    <div class="quantity-input">
                        <input type="number" name="quantity" id="quantity"
                            value="{{ old('quantity', $restockRequest->quantity) }}" min="0.01" step="0.01"
                            class="@error('quantity') is-invalid @enderror" required>

                        <span id="unit-label">
                            {{ $restockRequest->item?->unit?->name ?? '' }}
                        </span>
                    </div>

                    @error('quantity')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="note">Catatan</label>

                    <textarea name="note" id="note" rows="5" maxlength="1000"
                        placeholder="Tambahkan catatan jika diperlukan..." class="@error('note') is-invalid @enderror">{{ old('note', $restockRequest->note) }}</textarea>

                    @error('note')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-info">
                    <strong>Catatan:</strong>
                    Perubahan hanya dapat dilakukan selama ajuan masih berstatus
                    <strong>Pending</strong>.
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('barista.restock-requests.show', $restockRequest->id) }}" class="btn-secondary">
                    Batal
                </a>

                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemSelect = document.getElementById('item_id');
            const unitLabel = document.getElementById('unit-label');

            const units = @json(
                $items->mapWithKeys(fn($item) => [
                        $item->id => $item->unit?->name,
                    ]));

            itemSelect.addEventListener('change', function() {
                unitLabel.textContent = units[this.value] ?? '';
            });
        });
    </script>
@endsection
