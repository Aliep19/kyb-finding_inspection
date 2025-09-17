@extends('layouts.app')

@section('content')
<x-card title="Edit Detail Defect - {{ $defectInput->id_defect }}" icon="fa-solid fa-pen">
    <form action="{{ route('defect-input-details.update',[$defectInput->id,$detail->id]) }}" method="POST">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Kategori</label>
                <select name="defect_category_id" class="form-select" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $detail->defect_category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->defect_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Jenis Defect</label>
                <select name="defect_sub_id" class="form-select" required>
                    @foreach($subs as $sub)
                        <option value="{{ $sub->id }}" {{ $detail->defect_sub_id == $sub->id ? 'selected' : '' }}>
                            {{ $sub->jenis_defect }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Jumlah Defect</label>
                <input type="number" name="jumlah_defect" class="form-control" value="{{ $detail->jumlah_defect }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control" value="{{ $detail->keterangan }}">
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('defect-input-details.index',$defectInput->id) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-warning">Update</button>
        </div>
    </form>
</x-card>
@endsection
