    @extends('layouts.app')

    @section('content')
    <x-card title="Defect Categories" icon="fa-solid fa-bug">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('defect-subs.create') }}"
            class="btn btn-gradient btn-sm d-flex align-items-center gap-2"
            style="background: linear-gradient(90deg, #4CAF50, #2E7D32); color: white; border: none; transition: 0.3s;">
                <i class="bi bi-plus-circle-fill fs-6"></i> Add Data
            </a>
        </div>

        {{-- Success message --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search + Entries (reusable) --}}
        <x-search-page />

        {{-- Table --}}
        <table class="table table-hover table-custom align-middle">
            <thead>
                <tr>
                    <th style="width:70px">No</th>
                    <th>Defect Category Name</th>
                    <th style="width:200px">Jenis Defect</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td class="text-center">
                            {{ $loop->iteration + ($categories->firstItem() - 1) }}
                        </td>
                        <td class="fw-semibold">{{ $cat->defect_name }}</td>
                        <td class="text-center">
                            <a href="{{ route('defect-subs.byCategory', $cat->id) }}" class="badge bg-gradient-info border-0 shadow-sm">
                                Lihat Jenis ({{ $cat->subs_count ?? 0 }})
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted fst-italic">Belum ada data kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    </x-card>
    @endsection
