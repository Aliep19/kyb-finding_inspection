@extends('layouts.app')

@section('content')
<x-card title="Detail Defect - {{ $defectInput->id_defect }}" icon="fa-solid fa-list">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('defect-input-details.create',$defectInput->id) }}"
           class="btn btn-gradient btn-sm d-flex align-items-center gap-2"
           style="background: linear-gradient(90deg, #2196F3, #1565C0); color: white;">
           <i class="bi bi-plus-circle-fill fs-6"></i> Add Detail
        </a>
        <a href="{{ route('defect-inputs.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <x-search-page />

    <table class="table table-hover table-custom align-middle">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Jenis Defect</th>
                <th>Jumlah Defect</th>
                <th>Keterangan</th>
                <th style="width:200px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details as $detail)
                <tr>
                    <td>{{ $loop->iteration + ($details->firstItem()-1) }}</td>
                    <td>{{ $detail->category->defect_name ?? '-' }}</td>
                    <td>{{ $detail->sub->jenis_defect ?? '-' }}</td>
                    <td>{{ $detail->jumlah_defect }}</td>
                    <td>{{ $detail->keterangan }}</td>
                    <td class="text-center">
                        <a href="{{ route('defect-input-details.edit',[$defectInput->id,$detail->id]) }}"
                        class="badge bg-gradient-warning border-0 shadow-sm" title="Edit">
                            <i class="fa-solid fa-pen-to-square fs-6"></i>
                        </a>

                        <form action="{{ route('defect-input-details.destroy',[$defectInput->id,$detail->id]) }}"
                            method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="badge bg-gradient-danger border-0 shadow-sm" onclick="return confirm('Hapus detail?')" title="Delete">
                                <i class="fa-solid fa-trash fs-6"></i>
                            </button>
                        </form>
                    </td>

                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted fst-italic">Belum ada detail</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-end mt-3">
        {{ $details->links('pagination::bootstrap-5') }}
    </div>
</x-card>
@endsection
