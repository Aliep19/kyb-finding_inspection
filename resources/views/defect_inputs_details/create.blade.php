@extends('layouts.app')

@section('content')
<x-card title="Tambah Detail Defect - {{ $defectInput->id_defect }}" icon="fa-solid fa-plus">
    <form action="{{ route('defect-input-details.store', $defectInput->id) }}" method="POST">
        @csrf

        {{-- Wrapper baris defect --}}
        <div id="detail-container">
            <div class="row detail-row mb-3">
                <div class="col-md-4">
                    <label>Kategori</label>
                    <select name="defect_category_id[]" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->defect_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Jenis Defect</label>
                    <select name="defect_sub_id[]" class="form-select" required>
                        <option value="">-- Pilih Jenis Defect --</option>
                        @foreach($subs as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->jenis_defect }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah_defect[]" class="form-control" required>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="badge bg-gradient-danger border-0 shadow-sm fs-5 remove-row">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="button" id="add-row" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Tambah Defect
            </button>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control">
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('defect-input-details.index', $defectInput->id) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-success">Simpan</button>
        </div>
    </form>
</x-card>

{{-- Script JS sederhana untuk tambah/hapus baris --}}
@push('scripts')
<script>
document.getElementById('add-row').addEventListener('click', function () {
    let container = document.getElementById('detail-container');
    let newRow = container.querySelector('.detail-row').cloneNode(true);

    // Kosongkan input baru
    newRow.querySelectorAll('input, select').forEach(el => el.value = '');

    container.appendChild(newRow);
});

// Hapus baris
document.addEventListener('click', function (e) {
    if (e.target.closest('.remove-row')) {
        let rows = document.querySelectorAll('.detail-row');
if (rows.length > 1) {
    e.target.closest('.detail-row').remove();
} else {
    Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: 'Minimal satu baris defect harus ada!',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33'
    });
}

    }
});
</script>
@endpush
@endsection
