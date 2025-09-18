@extends('layouts.app')
<style>
    .bg-gradient-primary {
    background: linear-gradient(135deg, #0062E6 0%, #33AEFF 100%);
}

.info-item {
    padding: 6px 0;
    border-bottom: 1px dashed #e0e0e0;
    transition: background 0.2s;
}

.info-item:hover {
    background: #f8f9fa;
    border-radius: 6px;
    padding-left: 6px;
}

</style>
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
                    <a href="{{ route('defect-input-details.index', $input->id) }}"
                       class="badge bg-gradient-primary border-0 shadow-sm"
                       title="Details">
                        <i class="fa fa-eye fs-6"></i>
                    </a>
                    <button type="button"
                            class="badge bg-gradient-info border-0 shadow-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#detailModal{{ $input->id }}"
                            title="Show Details">
                        <i class="fa fa-info-circle fs-6"></i>
                    </button>
                    <a href="{{ route('defect-inputs.edit', $input->id) }}"
                       class="badge bg-gradient-warning border-0 shadow-sm"
                       title="Edit">
                        <i class="fa fa-edit fs-6"></i>
                    </a>
                    <form action="{{ route('defect-inputs.destroy', $input->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="badge bg-gradient-danger border-0 shadow-sm"
                                title="Delete"
                                onclick="return confirm('Hapus data?')">
                            <i class="fa fa-trash fs-6"></i>
                        </button>
                    </form>
                </td>
            </tr>

            <!-- Modal for each row -->
            <div class="modal fade" id="detailModal{{ $input->id }}" tabindex="-1"
                 aria-labelledby="detailModalLabel{{ $input->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-3">
    <!-- Header -->
    <div class="modal-header bg-gradient-danger text-white rounded-top-3">
        <h5 class="modal-title fw-bold" id="detailModalLabel{{ $input->id }}">
            <i class="bi bi-info-circle me-2"></i> Detail Data
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <!-- Body -->
    <div class="modal-body px-4 py-3">
        <div class="row g-3">
            <!-- Kiri -->
            <div class="col-md-6">
                <div class="info-item">
                    <span class="fw-semibold">Tanggal:</span> {{ $input->tgl }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">Shift:</span> {{ $input->shift }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">NPK:</span> {{ $input->npk }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">Nama:</span> {{ $input->user->full_name ?? '-' }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">Line:</span> {{ $input->line }}
                </div>
            </div>

            <!-- Kanan -->
            <div class="col-md-6">
                <div class="info-item">
                    <span class="fw-semibold">Marking Number:</span> {{ $input->marking_number ?? '-' }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">Lot:</span> {{ $input->lot ?? '-' }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">Kayaba No:</span> {{ $input->kayaba_no ?? '-' }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">Total Check:</span> {{ $input->total_check }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">OK:</span> {{ $input->ok ?? '-' }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">Total NG:</span> {{ $input->total_ng }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">Reject:</span> {{ $input->reject ?? '-' }}
                </div>
                <div class="info-item">
                    <span class="fw-semibold">Repair:</span> {{ $input->repair ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i> Close
        </button>
    </div>
</div>

                </div>
            </div>
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
