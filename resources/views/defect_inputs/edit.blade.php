@extends('layouts.app')

@section('content')
<x-card title="Edit Defect Input" icon="fa-solid fa-pen">
    <form action="{{ route('defect-inputs.update',$defectInput->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Tanggal</label>
                <input type="date" name="tgl" class="form-control" value="{{ $defectInput->tgl }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Shift</label>
                <input type="text" name="shift" class="form-control" value="{{ $defectInput->shift }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>NPK</label>
                <input type="text" name="npk" class="form-control" value="{{ $defectInput->npk }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label>Line</label>
                <select name="line" class="form-select" required>
                    @foreach($lines as $line)
                        <option value="{{ $line->subsect_name }}" {{ $defectInput->line == $line->subsect_name ? 'selected' : '' }}>
                            {{ $line->subsect_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Marking Number</label>
                <input type="text" name="marking_number" class="form-control" value="{{ $defectInput->marking_number }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>Lot</label>
                <input type="text" name="lot" class="form-control" value="{{ $defectInput->lot }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>Kayaba No</label>
                <input type="text" name="kayaba_no" class="form-control" value="{{ $defectInput->kayaba_no }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Total Check</label>
                <input type="number" name="total_check" class="form-control" value="{{ $defectInput->total_check }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Total NG</label>
                <input type="number" name="total_ng" class="form-control" value="{{ $defectInput->total_ng }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>OK</label>
                <input type="number" name="ok" class="form-control" id="ok" value="{{ $defectInput->ok }}" readonly>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    function calculateOK() {
                        const totalCheck = parseInt(document.querySelector('input[name="total_check"]').value) || 0;
                        const totalNG = parseInt(document.querySelector('input[name="total_ng"]').value) || 0;
                        const ok = totalCheck - totalNG;
                        document.getElementById('ok').value = ok >= 0 ? ok : 0;
                    }

                    document.querySelector('input[name="total_check"]').addEventListener('input', calculateOK);
                    document.querySelector('input[name="total_ng"]').addEventListener('input', calculateOK);

                    calculateOK();
                });
            </script>

            <div class="col-md-6 mb-3">
                <label>Reject</label>
                <input type="number" name="reject" class="form-control" value="{{ $defectInput->reject }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>Repair</label>
                <input type="number" name="repair" class="form-control" value="{{ $defectInput->repair }}">
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('defect-inputs.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-warning">Update</button>
        </div>
    </form>
</x-card>
@endsection
