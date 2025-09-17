@extends('layouts.app')



@section('content')
    <x-card title="Defect Category" icon="fa-solid fa-bug">
    <div class="d-flex justify-content-between align-items-center">

    <button class="btn btn-gradient btn-sm d-flex align-items-center gap-2"
            data-bs-toggle="modal"
            data-bs-target="#addModal"
            style="background: linear-gradient(90deg, #4CAF50, #2E7D32); color: white; border: none; transition: 0.3s;">
        <i class="bi bi-plus-circle-fill fs-6"></i> Add Data
    </button>
    </div>

   <div class="card-body">
    <x-search-page></x-search-page>

    {{-- Tabel Data --}}
    <table class="table table-hover table-custom align-middle data-table">
        <thead>
            <tr>
                <th style="width: 70px;">No</th>
                <th>Defect Category Name</th>
                <th>Jenis Defect</th>
                <th style="width: 180px;">Actions</th>

            </tr>
        </thead>
        <tbody>
            @forelse ($defcategories as $defect)
            <tr>
                <td class="fw-semibold text-center">
                    {{ $loop->iteration + ($defcategories->firstItem() - 1) }}
                </td>
                <td class="text-center">{{ $defect->defect_name }}</td>
                <td class="text-center">
                    @if (is_null($defect->jenis_defect))
                        <span class="badge bg-secondary">-</span>
                    @elseif ($defect->jenis_defect == 1)
                        <span class="badge bg-success">Painting</span>
                    @else
                        <span class="badge bg-danger">Not Painting</span>
                    @endif
                </td>

                <td class="text-center">
                <button class="badge bg-gradient-warning border-0 shadow-sm editBtn"
                        data-id="{{ $defect->id }}"
                        data-name="{{ $defect->defect_name }}"
                        data-jenis="{{ $defect->jenis_defect }}"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        title="Edit">
                    <i class="bi bi-pencil-square fs-6"></i>
                </button>

                    {{-- Tombol Delete --}}
                    <form action="{{ route('defect.destroy', $defect->id) }}"
                        method="POST"
                        class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="badge bg-gradient-danger shadow-sm border-0 deleteBtn" title="Delete">
                            <i class="bi bi-trash fs-6"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted fst-italic">Belum ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>


        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $defcategories->links('pagination::bootstrap-5') }}
        </div>
    </x-card>

    {{-- MODALS --}}
    @include('defect.modal')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/defect/script.js') }}"></script>
    @stack('scripts')
<script>
    window.sessionSuccess = @json(session('success'));
</script>

@endsection
