{{-- resources/views/defect_inputs/summary.blade.php --}}
@extends('layouts.app')

@section('content')
<x-card title="Defect Summary" icon="fa-solid fa-clipboard">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-hover table-custom align-middle">
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
