@extends('layouts.app')

@section('content')
<x-card title="Defect Summary" icon="fa-solid fa-clipboard">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('defect-inputs.create') }}"
           class="btn btn-gradient btn-sm d-flex align-items-center gap-2"
           style="background: linear-gradient(90deg, #4CAF50, #2E7D32); color: white;">
           <i class="bi bi-plus-circle-fill fs-6"></i> Add Data
        </a>

        <!-- 🔹 Filter Tahun -->
        <form action="{{ route('defect-inputs.summary') }}" method="GET" class="d-flex align-items-center gap-2">
            <label for="year" class="fw-bold mb-0">Tahun:</label>
            <select name="year" id="year" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 100px;">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="sortable table table-hover table-custom align-middle">
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Dept</th>
                <th>Total NG</th>
                <th style="width:200px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groups as $group)
                <tr>
                    <td>{{ $group->id }}</td>
                    <td>{{ $group->bulan }}</td>
                    <td>{{ $group->dept }}</td>
                    <td>{{ $group->total_ng }}</td>
                    <td class="text-center">
                        <a href="{{ route('defect-inputs.index') }}?month={{ $group->month }}&dept={{ $group->dept }}" class="badge bg-gradient-primary border-0 shadow-sm" title="View Details">
                            <i class="fa fa-eye fs-6"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted fst-italic">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</x-card>
@endsection
