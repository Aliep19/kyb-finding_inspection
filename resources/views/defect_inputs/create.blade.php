@extends('layouts.app')

@section('content')
<x-card title="Tambah Defect Input & Details" icon="fa-solid fa-plus">
    <form action="{{ route('defect-inputs.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Tanggal (otomatis hari ini) --}}
            <div class="col-md-4 mb-3">
                <label>Tanggal</label>
                <input type="date" name="tgl" class="form-control" value="{{ old('tgl', date('Y-m-d')) }}" readonly>
            </div>

            {{-- Shift --}}
            <div class="col-md-4 mb-3">
                <label>Shift</label>
                <select name="shift" class="form-select" required>
                    <option value="">-- Pilih Shift --</option>
                    <option value="1" {{ old('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                    <option value="2" {{ old('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                    <option value="3" {{ old('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                </select>
            </div>

            {{-- NPK (hidden dari session) --}}
            <input type="hidden" name="npk" value="{{ session('user_npk') }}">

            {{-- Line --}}
            <div class="col-md-4 mb-3">
                <label>Line</label>
                <select name="line" class="form-select" required>
                    <option value="">-- Pilih Line --</option>
                    @foreach($lines as $line)
                        <option value="{{ $line->subsect_name }}" {{ old('line') == $line->subsect_name ? 'selected' : '' }}>{{ $line->subsect_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Marking Number --}}
            <div class="col-md-6 mb-3">
                <label>Marking Number</label>
                <input type="text" name="marking_number" class="form-control" value="{{ old('marking_number') }}">
            </div>

            {{-- Lot --}}
            <div class="col-md-6 mb-3">
                <label>Lot</label>
                <input type="text" name="lot" class="form-control" value="{{ old('lot') }}">
            </div>

            {{-- Kayaba No --}}
            <div class="col-md-6 mb-3">
                <label>Kayaba No</label>
                <input type="text" name="kayaba_no" class="form-control" value="{{ old('kayaba_no') }}">
            </div>

            {{-- Total Check --}}
            <div class="col-md-6 mb-3">
                <label>Total Check</label>
                <input type="number" name="total_check" class="form-control" value="{{ old('total_check') }}" required>
            </div>
        </div>

        {{-- Bagian Defect Details --}}
        <hr>
        <h5>Detail Defect</h5>
        <div id="detail-container">
            @php
                $defectCategories = old('defect_category_id', []);
                $defectSubs = old('defect_sub_id', []);
                $jumlahDefects = old('jumlah_defect', []);
                $rowCount = max(count($defectCategories), 1); // Minimal 1 baris jika tidak ada data lama
            @endphp

            @for ($i = 0; $i < $rowCount; $i++)
                <div class="row detail-row mb-3">
                    <div class="col-md-4">
                        <label>Kategori</label>
                        <select name="defect_category_id[]" class="form-select category-select" >
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('defect_category_id.' . $i) == $cat->id ? 'selected' : '' }}>{{ $cat->defect_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Jenis Defect</label>
                        <select name="defect_sub_id[]" class="form-select sub-select" >
                            <option value="">-- Pilih Jenis Defect --</option>
                            {{-- Opsi akan diisi oleh JS, tapi inisialisasi untuk data old --}}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah_defect[]" class="form-control jumlah-defect" value="{{ old('jumlah_defect.' . $i) }}" >
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

        {{-- Total NG (readonly, otomatis) --}}
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Total NG</label>
                <input type="number" name="total_ng" class="form-control" id="total_ng" value="{{ old('total_ng') }}" readonly>
            </div>

            {{-- OK (readonly, otomatis) --}}
            <div class="col-md-4 mb-3">
                <label>OK</label>
                <input type="number" name="ok" class="form-control" id="ok" value="{{ old('ok') }}" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label>Reject</label>
                <input type="number" name="reject" class="form-control reject" value="{{ old('reject', 0) }}" min="0">
                @error('reject')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- Repair --}}
            <div class="col-md-4 mb-3">
                <label>Repair</label>
                <input type="number" name="repair" class="form-control repair" value="{{ old('repair', 0) }}" min="0">
            </div>

            {{-- Keterangan (di paling akhir) --}}
            <div class="col-md-12 mb-3">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('defect-inputs.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-success">Simpan</button>
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

            // Fungsi untuk validasi Reject + Repair (dipanggil saat input, tapi tidak perlu alert di sini)
            function validateRejectRepair() {
                const ng = parseInt(totalNg.value) || 0;
                const rej = parseInt(reject.value) || 0;
                const rep = parseInt(repair.value) || 0;
                return rej + rep === ng;
            }

            // Event listener untuk Total Check
            totalCheck.addEventListener('input', calculateOK);

            // Event listener untuk input jumlah_defect
            detailContainer.addEventListener('input', function (e) {
                if (e.target.classList.contains('jumlah-defect')) {
                    calculateTotalNG();
                }
            });

            // Event listener untuk Reject dan Repair (hanya hitung ulang, alert di submit)
            reject.addEventListener('input', calculateOK); // Opsional, jika ingin update OK
            repair.addEventListener('input', calculateOK); // Opsional

            // Tambah baris defect
            document.getElementById('add-row').addEventListener('click', function () {
                let newRow = detailContainer.querySelector('.detail-row').cloneNode(true);
                newRow.querySelectorAll('input, select').forEach(el => el.value = '');
                newRow.querySelector('.sub-select').innerHTML = '<option value="">-- Pilih Jenis Defect --</option>';
                detailContainer.appendChild(newRow);
            });

            // Event listener untuk change kategori (isi sub defect)
            document.addEventListener('change', function(e) { // Ubah dari 'input' ke 'change' agar lebih akurat
                if (e.target.classList.contains('category-select')) {
                    let row = e.target.closest('.detail-row');
                    let catId = e.target.value;
                    let subSelect = row.querySelector('.sub-select');
                    subSelect.innerHTML = '<option value="">-- Pilih Jenis Defect --</option>';
                    if (subsByCategory[catId]) {
                        subsByCategory[catId].forEach(sub => {
                            let option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.jenis_defect;
                            subSelect.appendChild(option);
                        });
                    }
                }
            });

            // Hapus baris defect
            document.addEventListener('click', function (e) {
                if (e.target.closest('.remove-row')) {
                    let rows = document.querySelectorAll('.detail-row');
                    if (rows.length > 1) {
                        e.target.closest('.detail-row').remove();
                        calculateTotalNG();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Minimal satu baris defect harus ada!',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            });

            // Validasi saat submit form (ini yang utama untuk alert tanpa refresh)
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
                    return; // Stop submit
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
                    return; // Stop submit
                }

                // Jika lolos, form akan submit ke server
            });

            // Inisialisasi awal: Hitung Total NG dan OK dari data old()
            calculateTotalNG();

            // Inisialisasi ulang dropdown sub-defect untuk data old()
            document.querySelectorAll('.category-select').forEach(select => {
                if (select.value) {
                    let event = new Event('change');
                    select.dispatchEvent(event); // Trigger change untuk isi sub-select
                    let subSelect = select.closest('.detail-row').querySelector('.sub-select');
                    subSelect.value = '{{ old('defect_sub_id.' . $i) }}'; // Set nilai old untuk sub
                }
            });
        });
    </script>
</x-card>
@endsection
