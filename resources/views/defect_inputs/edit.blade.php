@extends('layouts.app')

@section('content')
<x-card title="Edit Defect Input & Details" icon="fa-solid fa-pen">
    <form action="{{ route('defect-inputs.update', $defectInput->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- Tanggal --}}
            <div class="col-md-4 mb-3">
                <label>Tanggal</label>
                <input type="date" name="tgl" class="form-control" value="{{ old('tgl', $defectInput->tgl) }}" required>
            </div>

            {{-- Shift --}}
            <div class="col-md-4 mb-3">
                <label>Shift</label>
                <input type="text" name="shift" class="form-control" value="{{ old('shift', $defectInput->shift) }}" required>
            </div>

            {{-- NPK (readonly) --}}
            <div class="col-md-4 mb-3">
                <label>NPK</label>
                <input type="text" name="npk" class="form-control" value="{{ old('npk', $defectInput->npk) }}" readonly>
            </div>

            {{-- Line --}}
            <div class="col-md-6 mb-3">
                <label>Line</label>
                <select name="line" class="form-select" required>
                    <option value="">-- Pilih Line --</option>
                    @foreach($lines as $line)
                        <option value="{{ $line->subsect_name }}" {{ old('line', $defectInput->line) == $line->subsect_name ? 'selected' : '' }}>
                            {{ $line->subsect_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Marking Number --}}
            <div class="col-md-6 mb-3">
                <label>Marking Number</label>
                <input type="text" name="marking_number" class="form-control" value="{{ old('marking_number', $defectInput->marking_number) }}">
            </div>

            {{-- Lot --}}
            <div class="col-md-6 mb-3">
                <label>Lot</label>
                <input type="text" name="lot" class="form-control" value="{{ old('lot', $defectInput->lot) }}">
            </div>

            {{-- Kayaba No --}}
            <div class="col-md-6 mb-3">
                <label>Kayaba No</label>
                <input type="text" name="kayaba_no" class="form-control" value="{{ old('kayaba_no', $defectInput->kayaba_no) }}">
            </div>

            {{-- Total Check --}}
            <div class="col-md-4 mb-3">
                <label>Total Check</label>
                <input type="number" name="total_check" class="form-control" value="{{ old('total_check', $defectInput->total_check) }}" required>
            </div>

            {{-- Total NG (readonly, calculated) --}}
            <div class="col-md-4 mb-3">
                <label>Total NG</label>
                <input type="number" name="total_ng" class="form-control" id="total_ng" value="{{ old('total_ng', $defectInput->total_ng) }}" readonly>
            </div>

            {{-- OK (readonly, calculated) --}}
            <div class="col-md-4 mb-3">
                <label>OK</label>
                <input type="number" name="ok" class="form-control" id="ok" value="{{ old('ok', $defectInput->ok) }}" readonly>
            </div>

            {{-- Reject --}}
            <div class="col-md-6 mb-3">
                <label>Reject</label>
                <input type="number" name="reject" class="form-control reject" value="{{ old('reject', $defectInput->reject ?? 0) }}" min="0">
                @error('reject')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- Repair --}}
            <div class="col-md-6 mb-3">
                <label>Repair</label>
                <input type="number" name="repair" class="form-control repair" value="{{ old('repair', $defectInput->repair ?? 0) }}" min="0">
            </div>

            {{-- Keterangan --}}
            <div class="col-md-12 mb-3">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $defectInput->keterangan) }}">
            </div>
        </div>

        {{-- Bagian Defect Details --}}
        <hr>
        <h5>Detail Defect</h5>
        <div id="detail-container">
            @php
                $defectDetails = $defectInput->details ?? [];
                $rowCount = max(count($defectDetails), 1); // Minimal 1 baris
            @endphp

            @for ($i = 0; $i < $rowCount; $i++)
                <div class="row detail-row mb-3">
                    <div class="col-md-4">
                        <label>Kategori</label>
                        <select name="defect_category_id[]" class="form-select category-select">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('defect_category_id.' . $i, isset($defectDetails[$i]) ? $defectDetails[$i]->defect_category_id : '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->defect_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Jenis Defect</label>
                        <select name="defect_sub_id[]" class="form-select sub-select">
                            <option value="">-- Pilih Jenis Defect --</option>
                            {{-- Opsi akan diisi oleh JS --}}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah_defect[]" class="form-control jumlah-defect" value="{{ old('jumlah_defect.' . $i, isset($defectDetails[$i]) ? $defectDetails[$i]->jumlah_defect : '') }}" min="0">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="badge bg-gradient-danger border-0 shadow-sm fs-5 remove-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            @endfor
        </div>

        <div class="mb-3">
            <button type="button" id="add-row" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Tambah Defect
            </button>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('defect-inputs.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-warning">Update</button>
        </div>
    </form>

    {{-- Script untuk hitung Total NG, OK, dan validasi Reject + Repair --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalCheck = document.querySelector('input[name="total_check"]');
        const totalNg = document.getElementById('total_ng');
        const ok = document.getElementById('ok');
        const reject = document.querySelector('input[name="reject"]');
        const repair = document.querySelector('input[name="repair"]');
        const detailContainer = document.getElementById('detail-container');
        const subsByCategory = @json($subsByCategory);
        // Kirim data defect_sub_id dari PHP ke JavaScript
        const defectSubIds = @json($defectDetails->pluck('defect_sub_id')->toArray());

        // Fungsi untuk menghitung Total NG
        function calculateTotalNG() {
            const jumlahDefects = document.querySelectorAll('.jumlah-defect');
            let total = 0;
            jumlahDefects.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            totalNg.value = total;
            calculateOK();
        }

        // Fungsi untuk menghitung OK
        function calculateOK() {
            const check = parseInt(totalCheck.value) || 0;
            const ng = parseInt(totalNg.value) || 0;
            const result = check - ng;
            ok.value = result >= 0 ? result : 0;
        }

        // Fungsi untuk mengisi sub-select berdasarkan kategori
        function populateSubSelect(categorySelect, subSelect, selectedSubId = '') {
            const catId = categorySelect.value;
            subSelect.innerHTML = '<option value="">-- Pilih Jenis Defect --</option>';
            if (subsByCategory[catId]) {
                subsByCategory[catId].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.jenis_defect;
                    if (sub.id == selectedSubId) {
                        option.selected = true;
                    }
                    subSelect.appendChild(option);
                });
            }
        }

        // Event listener untuk Total Check
        totalCheck.addEventListener('input', calculateOK);

        // Event listener untuk input jumlah_defect
        detailContainer.addEventListener('input', function (e) {
            if (e.target.classList.contains('jumlah-defect')) {
                calculateTotalNG();
            }
        });

        // Event listener untuk Reject dan Repair
        reject.addEventListener('input', calculateOK);
        repair.addEventListener('input', calculateOK);

        // Tambah baris defect
        document.getElementById('add-row').addEventListener('click', function () {
            let newRow = detailContainer.querySelector('.detail-row').cloneNode(true);
            newRow.querySelectorAll('input, select').forEach(el => el.value = '');
            newRow.querySelector('.sub-select').innerHTML = '<option value="">-- Pilih Jenis Defect --</option>';
            detailContainer.appendChild(newRow);
        });

        // Event listener untuk change kategori
        detailContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('category-select')) {
                const row = e.target.closest('.detail-row');
                const subSelect = row.querySelector('.sub-select');
                populateSubSelect(e.target, subSelect);
            }
        });

        // Hapus baris defect
        detailContainer.addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                let row = e.target.closest('.detail-row');
                let rows = document.querySelectorAll('.detail-row');
                if (rows.length > 1) {
                    row.remove();
                    calculateTotalNG();
                } else {
                    row.querySelectorAll('input, select').forEach(el => el.value = '');
                    row.querySelector('.sub-select').innerHTML = '<option value="">-- Pilih Jenis Defect --</option>';
                    calculateTotalNG();
                }
            }
        });

        // Validasi saat submit form
        document.querySelector('form').addEventListener('submit', function (e) {
            const ng = parseInt(totalNg.value) || 0;
            const check = parseInt(totalCheck.value) || 0;
            const rej = parseInt(reject.value) || 0;
            const rep = parseInt(repair.value) || 0;

            if (ng > check) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Total NG tidak boleh melebihi Total Check!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            if (rej + rep !== ng) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Total Reject + Repair harus sama dengan Total NG (${ng})!`,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
                return;
            }
        });

        // Inisialisasi awal: Hitung Total NG dan OK
        calculateTotalNG();

        // Inisialisasi ulang dropdown sub-defect untuk data yang sudah ada
        document.querySelectorAll('.detail-row').forEach((row, index) => {
            const categorySelect = row.querySelector('.category-select');
            const subSelect = row.querySelector('.sub-select');
            // Gunakan defectSubIds dari array yang dihasilkan oleh PHP
            const selectedSubId = defectSubIds[index] || '';

            if (categorySelect.value) {
                populateSubSelect(categorySelect, subSelect, selectedSubId);
            }
        });
    });
</script>
</x-card>
@endsection
