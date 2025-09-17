@extends('layouts.app')

@section('content')
<x-card title="Tambah Defect Input" icon="fa-solid fa-plus">
    <form action="{{ route('defect-inputs.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Tanggal --}}
            <div class="col-md-4 mb-3">
                <label>Tanggal</label>
                <input type="date" name="tgl" class="form-control" required>
            </div>

            {{-- Shift --}}
            <div class="col-md-4 mb-3">
                <label>Shift</label>
                <input type="text" name="shift" class="form-control" required>
            </div>

            {{-- NPK (hidden dari session) --}}
            <input type="hidden" name="npk" value="{{ session('user_npk') }}">

            {{-- Line --}}
            <div class="col-md-4 mb-3">
                <label>Line</label>
                <select name="line" class="form-select" required>
                    <option value="">-- Pilih Line --</option>
                    @foreach($lines as $line)
                        <option value="{{ $line->subsect_name }}">{{ $line->subsect_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Marking Number --}}
            <div class="col-md-6 mb-3">
                <label>Marking Number</label>
                <input type="text" name="marking_number" class="form-control">
            </div>

            {{-- Lot --}}
            <div class="col-md-6 mb-3">
                <label>Lot</label>
                <input type="text" name="lot" class="form-control">
            </div>

            {{-- Kayaba No --}}
            <div class="col-md-6 mb-3">
                <label>Kayaba No</label>
                <input type="text" name="kayaba_no" class="form-control">
            </div>

            {{-- Total Check --}}
            <div class="col-md-4 mb-3">
                <label>Total Check</label>
                <input type="number" name="total_check" class="form-control" required>
            </div>

            {{-- Total NG --}}
            <div class="col-md-4 mb-3">
                <label>Total NG</label>
                <input type="number" name="total_ng" class="form-control" required>
            </div>

            {{-- OK (readonly, otomatis) --}}
            <div class="col-md-4 mb-3">
                <label>OK</label>
                <input type="number" name="ok" class="form-control" id="ok" readonly>
            </div>

            {{-- Reject --}}
            <div class="col-md-6 mb-3">
                <label>Reject</label>
                <input type="number" name="reject" class="form-control">
            </div>

            {{-- Repair --}}
            <div class="col-md-6 mb-3">
                <label>Repair</label>
                <input type="number" name="repair" class="form-control">
            </div>
        </div>

        {{-- Button --}}
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('defect-inputs.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-success">Simpan</button>
        </div>
    </form>

    {{-- Script untuk hitung OK --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalCheck = document.querySelector('input[name="total_check"]');
            const totalNg = document.querySelector('input[name="total_ng"]');
            const ok = document.getElementById('ok');

            function calculateOK() {
                const check = parseInt(totalCheck.value) || 0;
                const ng = parseInt(totalNg.value) || 0;
                const result = check - ng;
                ok.value = result >= 0 ? result : 0;
            }

            totalCheck.addEventListener('input', calculateOK);
            totalNg.addEventListener('input', calculateOK);
        });
    </script>
</x-card>
@endsection
