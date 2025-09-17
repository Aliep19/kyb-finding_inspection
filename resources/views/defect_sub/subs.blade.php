@extends('layouts.app')

@section('content')
<x-card :title="'Jenis Defect — ' . $category->defect_name" icon="fa-solid fa-bug">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('defect-subs.index') }}" class="btn btn-warning btn-sm">Kembali</a>
            <a href="{{ route('defect-subs.create', ['category_id' => $category->id]) }}" class="btn btn-success btn-sm">Tambah Data</a>
        </div>
    </div>

    {{-- Success (pakai PHP) --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-hover table-bordered align-middle table-striped">
        <thead class="bg-danger text-white text-center">
            <tr>
                <th style="width:70px">No</th>
                <th>Jenis Defect</th>
                <th style="width:180px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subs as $sub)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $sub->jenis_defect }}</td>
                    <td class="text-center">
                        <a href="{{ route('defect-subs.edit', $sub->id) }}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fa-solid fa-pen-to-square fs-6"></i>
                        </a>
                        <form action="{{ route('defect-subs.destroy', $sub->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm deleteBtn" title="Hapus">
                                <i class="fa-solid fa-trash fs-6"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted fst-italic">Belum ada jenis defect.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</x-card>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', function(e){
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Yakin hapus data ini?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

