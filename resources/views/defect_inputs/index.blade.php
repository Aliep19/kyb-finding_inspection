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

.pica-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.pica-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    background: #f8f9fa;
}

.pica-image {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 0.5rem;
}

.defect-sub-name {
    font-weight: bold;
    color: #495057;
    margin-bottom: 0.5rem;
}

.upload-form {
    margin-top: 0.5rem;
}

.upload-form input[type="file"] {
    display: block;
    width: 100%;
    margin-bottom: 0.5rem;
}

.locked-alert {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 0.75rem;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
}
</style>
@section('content')
<x-card title="Defect Inputs" icon="fa-solid fa-clipboard">
<div class="row align-items-center mb-3">
    <!-- Kolom kiri: Filter -->
    <div class="col-md-6">
        <form method="GET" action="{{ route('defect-inputs.index') }}" class="d-inline">
            <label for="filter" class="me-2 fw-semibold">Filter:</label>
            <select name="filter" id="filter" onchange="this.form.submit()"
                    class="form-select form-select-sm d-inline w-20">
                <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>All Days</option>
            </select>
        </form>
    </div>

    <!-- Kolom kanan: Tombol aksi -->
    <div class="col-md-6 d-flex justify-content-end gap-2">
        <a href="{{ route('defect-inputs.create') }}"
           class="btn btn-gradient btn-sm d-flex align-items-center gap-2"
           style="background: linear-gradient(90deg, #4CAF50, #2E7D32); color: white;">
           <i class="bi bi-plus-circle-fill fs-6"></i> Add Data
        </a>
        <a href="{{ route('defect-inputs.summary') }}"
           class="btn btn-secondary btn-sm d-flex align-items-center gap-2"
           style="height:38px;">
           Kembali
        </a>
    </div>
</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <x-search-page />
    <div class="table-responsive">
    <table class="sortable table table-hover table-custom align-middle" >
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>Line</th>
            <th>Total Check</th>
            <th>OK</th>
            <th>Total NG</th>
            <th>Defect Detail</th>
            <th>PICA</th>
            <th style="width:200px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($inputs as $input)
            <tr>
                <td>{{ $loop->iteration + ($inputs->firstItem()-1) }}</td>
                <td>{{ $input->tgl }}</td>
                <td>{{ $input->shift }}</td>
                <td>{{ $input->line }}</td>
                <td>{{ $input->total_check }}</td>
                <td>{{ $input->ok }}</td>
                <td>{{ $input->total_ng }}</td>
                <td>{{ $input->details->count() }}</td>

                <td>
                    @php $picaCount = $input->details->whereNotNull('pica')->count(); @endphp
                    <button type="button"
                            class="badge bg-gradient-success border-0 shadow-sm {{ $picaCount == 0 ? 'opacity-50' : '' }}"
                            data-bs-toggle="modal"
                            data-bs-target="#picaModal{{ $input->id }}"
                            title="Manage PICA ({{ $input->details->count() }} details, {{ $picaCount }} uploaded)">
                        <i class="fa fa-image fs-6"></i> PICA ({{ $picaCount }}/{{ $input->details->count() }})
                    </button>
                </td>

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

            <!-- Modal for each row (existing detail modal) -->
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
                    <i class="fa fa-calendar-alt me-2 text-primary"></i>
                    <span class="fw-semibold">Tanggal:</span> {{ $input->tgl }}
                </div>
                <div class="info-item">
                    <i class="fa fa-moon me-2 text-info"></i>
                    <span class="fw-semibold">Shift:</span> {{ $input->shift }}
                </div>
                <div class="info-item">
                    <i class="fa fa-id-card me-2 text-danger"></i>
                    <span class="fw-semibold">NPK:</span> {{ $input->npk }}
                </div>
                <div class="info-item">
                    <i class="fa fa-user me-2 text-success"></i>
                    <span class="fw-semibold">Nama:</span> {{ $input->user->full_name ?? '-' }}
                </div>
                <div class="info-item">
                    <i class="fa fa-stream me-2 text-warning"></i>
                    <span class="fw-semibold">Line:</span> {{ $input->line }}
                </div>
                <div class="info-item">
                    <i class="fa fa-hashtag me-2 text-secondary"></i>
                    <span class="fw-semibold">Marking Number:</span> {{ $input->marking_number ?? '-' }}
                </div>
                <div class="info-item">
                    <i class="fa fa-boxes me-2 text-primary"></i>
                    <span class="fw-semibold">Lot:</span> {{ $input->lot ?? '-' }}
                </div>
            </div>

            <!-- Kanan -->
            <div class="col-md-6">
                <div class="info-item">
                    <i class="fa fa-barcode me-2 text-info"></i>
                    <span class="fw-semibold">Kayaba No:</span> {{ $input->kayaba_no ?? '-' }}
                </div>
                <div class="info-item">
                    <i class="fa fa-tasks me-2 text-success"></i>
                    <span class="fw-semibold">Total Check:</span> {{ $input->total_check }}
                </div>
                <div class="info-item">
                    <i class="fa fa-check-circle me-2 text-success"></i>
                    <span class="fw-semibold">OK:</span> {{ $input->ok ?? '-' }}
                </div>
                <div class="info-item">
                    <i class="fa fa-times-circle me-2 text-danger"></i>
                    <span class="fw-semibold">Total NG:</span> {{ $input->total_ng }}
                </div>
                <div class="info-item">
                    <i class="fa fa-ban me-2 text-danger"></i>
                    <span class="fw-semibold">Reject:</span> {{ $input->reject ?? '-' }}
                </div>
                <div class="info-item">
                    <i class="fa fa-tools me-2 text-warning"></i>
                    <span class="fw-semibold">Repair:</span> {{ $input->repair ?? '-' }}
                </div>
                <div class="info-item">
                    <i class="fa fa-comment-dots me-2 text-secondary"></i>
                    <span class="fw-semibold">Keterangan:</span> {{ $input->keterangan ?? '-' }}
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

            <!-- Updated Modal for PICA (per row) - Now with upload forms, delete, and lock alerts -->
            <div class="modal fade" id="picaModal{{ $input->id }}" tabindex="-1"
                 aria-labelledby="picaModalLabel{{ $input->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content border-0 shadow-lg rounded-3">
                        <!-- Header -->
                        <div class="modal-header bg-success text-white rounded-top-3">
                            <h5 class="modal-title fw-bold" id="picaModalLabel{{ $input->id }}">
                                <i class="fa fa-image me-2"></i> Manage PICA untuk {{ $input->tgl }} - Shift {{ $input->shift }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body px-4 py-3">
                            <!-- Notifikasi awal terkait lock PICA -->
                            <div class="alert alert-danger text-white fw-bold" role="alert">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>Info PICA:</strong> Anda dapat mengupload, mengganti, atau menghapus PICA dalam 30 menit setelah upload awal. Setelah itu, PICA akan terkunci dan tidak dapat diubah lagi.
                            </div>

                            @php $allDetails = $input->details; @endphp
                            @if($allDetails->count() > 0)
                                <p class="text-muted mb-3">Total Defect Details: {{ $allDetails->count() }} | Uploaded PICA: {{ $allDetails->whereNotNull('pica')->count() }}</p>
                                <div class="pica-grid">
                                    @foreach($allDetails as $detail)
@php
    $canEditPica = !$detail->pica || ($detail->pica_uploaded_at && now()->diffInMinutes($detail->pica_uploaded_at, false) <= 30);
    $uploadTime = $detail->pica_uploaded_at ? $detail->pica_uploaded_at->format('d/m/Y H:i') : 'N/A';
@endphp

                                    <div class="pica-item">
                                        <div class="defect-sub-name">
                                            {{ $detail->sub->jenis_defect ?? 'Unknown Defect Sub' }}
                                            <span class="badge bg-light text-dark fs-6">Jumlah Defect : {{ $detail->jumlah_defect }}</span>
                                        </div>

                                        @if($detail->pica)
                                            <img src="{{ asset('storage/' . $detail->pica) }}" alt="PICA for {{ $detail->sub->jenis_defect ?? 'Detail' }} {{ $detail->id }}" class="pica-image" style="max-height: 200px;">

                                            @if(!$canEditPica)
                                                <div class="locked-alert">
                                                    <i class="fa fa-lock me-1"></i> PICA terkunci setelah 30 menit. Upload/Hapus tidak dapat dilakukan.
                                                </div>
                                                <!-- Button disabled -->
                                                <button type="button" class="btn btn-warning btn-sm w-100 opacity-50" disabled>Replace PICA</button>
                                                <button type="button" class="btn btn-danger btn-sm w-100 opacity-50 mt-1" disabled>Delete PICA</button>
                                            @else
                                                <!-- Form to replace PICA (button enabled) -->
                                                <form action="{{ route('defect-inputs.upload-pica', [$input, $detail]) }}" method="POST" enctype="multipart/form-data" class="upload-form">
                                                    @csrf
                                                    <input type="file" name="pica" accept="image/*">
                                                    <button type="submit" class="btn btn-warning btn-sm w-100">Replace PICA</button>
                                                </form>

                                                <!-- Form to delete PICA (button enabled) -->
                                                <form action="{{ route('defect-inputs.delete-pica', [$input, $detail]) }}" method="POST" class="upload-form mt-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Yakin hapus PICA ini?')">Delete PICA</button>
                                                </form>
                                            @endif
                                        @else
                                                <div class="text-center py-3">
                                                    <i class="fa fa-image fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted small">Belum ada PICA</p>
                                                </div>

                                                <!-- Upload Form -->
                                                <form action="{{ route('defect-inputs.upload-pica', [$input, $detail]) }}" method="POST" enctype="multipart/form-data" class="upload-form">
                                                    @csrf
                                                    <input type="file" name="pica" accept="image/*" required>
                                                    <br><br>
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">Upload PICA</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-5">
                                    <i class="fa fa-exclamation-triangle fa-3x mb-3 opacity-50"></i>
                                    <p>Belum ada Defect Details untuk data ini.</p>
                                    <small>Tambahkan details melalui halaman edit atau create.</small>
                                </div>
                            @endif
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
            <tr><td colspan="10" class="text-center text-muted fst-italic">Belum ada data</td></tr>
        @endforelse
    </tbody>
</table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $inputs->links('pagination::bootstrap-5') }}
    </div>
</x-card>
@endsection
