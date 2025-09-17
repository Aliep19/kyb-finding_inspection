@extends('layouts.app')

@section('content')
<div class="container mt-4">
    {{-- Notifikasi sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <x-card title="Data Target" icon="fa-solid fa-bullseye">
    <div class="d-flex justify-content-between align-items-center mb-3">

    <button class="btn btn-gradient btn-sm d-flex align-items-center gap-2"
            data-bs-toggle="modal"
            data-bs-target="#addModal"
            style="background: linear-gradient(90deg, #4CAF50, #2E7D32); color: white; border: none; transition: 0.3s;">
        <i class="bi bi-plus-circle-fill fs-6"></i> ADD Data
    </button>

    </div>
    <x-search-page></x-search-page>

    {{-- Tabel Data --}}

    <table class="table table-hover table-custom align-middle">
    <thead>
        <tr>
            <th>Sort</th>
            <th>Department</th>
            <th>Target</th>
            <th>Periode</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
        @forelse($targets as $i => $target)
            <tr>
                <td>{{ $targets->firstItem() + $i }}</td>
                <td>{{ $target->department->dept_name }}</td>
                <td>{{ $target->target_value }}</td>
                <td>{{ $target->period }}</td>
                <td>
                    <button class="badge bg-gradient-warning border-0 shadow-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $target->id }}"
                            title="Edit">
                        <i class="bi bi-pencil-square fs-6"></i>
                    </button>
                    <form action="{{ route('targets.destroy', $target->id) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Hapus target ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="badge bg-gradient-danger border-0 shadow-sm" title="Hapus">
                            <i class="bi bi-trash fs-6"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted fst-italic">Belum ada data target</td>
            </tr>
        @endforelse
    </tbody>
</table>


    {{-- Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $targets->links('pagination::bootstrap-5') }}
    </div>
</x-card>

</div>

{{-- Include modal add & edit --}}
@include('target.modal')
@endsection
