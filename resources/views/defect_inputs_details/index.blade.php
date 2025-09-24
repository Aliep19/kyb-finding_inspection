@extends('layouts.app')

@section('content')
<x-card title="Detail Defect - {{ $defectInput->id_defect }}" icon="fa-solid fa-list">
    <div class="d-flex justify-content-between align-items-center mb-3">
        
        <a href="{{ route('defect-inputs.index') }}"            class="btn btn-secondary btn-sm d-flex align-items-center gap-2"
           style="height:38px;">
           Kembali</a>
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
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($details as $detail)
            <tr>
                <td>{{ $loop->iteration + ($details->firstItem()-1) }}</td>
                <td>{{ $detail->category->defect_name ?? '-' }}</td>
                <td>{{ $detail->sub->jenis_defect ?? '-' }}</td>
                <td>{{ $detail->jumlah_defect }}</td>
                <td>
                    <form action="{{ route('defect-input-details.update', [$defectInput->id, $detail->id]) }}" method="POST" class="keterangan-form">
                        @csrf
                        @method('PUT')
                        <select name="keterangan" class="form-select form-select-sm keterangan-select">
                            <option value="" {{ $detail->keterangan == '' ? 'selected' : '' }}>- Kosong -</option>
                            <option value="repair" {{ $detail->keterangan == 'repair' ? 'selected' : '' }}>Repair</option>
                            <option value="reject" {{ $detail->keterangan == 'reject' ? 'selected' : '' }}>Reject</option>
                        </select>
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
@push('scripts')
<script>
    document.querySelectorAll('.keterangan-select').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
@endpush

