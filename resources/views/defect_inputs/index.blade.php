@extends('layouts.app')

@section('content')
<x-card title="Defect Inputs" icon="fa-solid fa-clipboard">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('defect-inputs.create') }}"
           class="btn btn-gradient btn-sm d-flex align-items-center gap-2"
           style="background: linear-gradient(90deg, #4CAF50, #2E7D32); color: white;">
           <i class="bi bi-plus-circle-fill fs-6"></i> Add Data
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-search-page />
<table class="table table-hover table-custom align-middle">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>NPK</th>
            <th>Line</th>
            <th>Total Check</th>
            <th>OK</th>
            <th>Total NG</th>
            <th style="width:200px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($inputs as $input)
            <tr>
                <td>{{ $loop->iteration + ($inputs->firstItem()-1) }}</td>
                <td>{{ $input->tgl }}</td>
                <td>{{ $input->shift }}</td>
                <td>
                    <div class="d-flex flex-column align-items-start">
                        <span class="fw-bold text-danger fs-6">{{ $input->npk }}</span>
                        <span class="badge bg-gradient-primary text-white mt-1 px-2 py-1"
                              data-bs-toggle="tooltip"
                              title="{{ $input->user->full_name ?? '-' }}">
                            <i class="fa fa-user me-1"></i>
                            {{ explode(' ', $input->user->full_name ?? '-')[0] }}
                        </span>
                    </div>
                </td>
                <td>{{ $input->line }}</td>
                <td>{{ $input->total_check }}</td>
                <td>{{ $input->ok }}</td>
                <td>{{ $input->total_ng }}</td>
                <td class="text-center">
                    <a href="{{ route('defect-input-details.index', $input->id) }}" class="badge bg-gradient-primary border-0 shadow-sm" title="Details">
                        <i class="fa fa-eye fs-6"></i>
                    </a>
                    <a href="{{ route('defect-inputs.edit', $input->id) }}" class="badge bg-gradient-warning border-0 shadow-sm" title="Edit">
                        <i class="fa fa-edit fs-6"></i>
                    </a>
                    <form action="{{ route('defect-inputs.destroy', $input->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="badge bg-gradient-danger border-0 shadow-sm" title="Delete" onclick="return confirm('Hapus data?')">
                            <i class="fa fa-trash fs-6"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="text-center text-muted fst-italic">Belum ada data</td></tr>
        @endforelse
    </tbody>
</table>


    <div class="d-flex justify-content-end mt-3">
        {{ $inputs->links('pagination::bootstrap-5') }}
    </div>
</x-card>
@endsection
