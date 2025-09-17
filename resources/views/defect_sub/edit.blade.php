@extends('layouts.app')

@section('content')
<x-card title="Edit Jenis Defect" icon="fa-solid fa-edit">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('defect-subs.update', $sub->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Defect Category</label>
            <select name="id_defect_category" class="form-control" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $sub->id_defect_category == $cat->id ? 'selected' : '' }}>
                        {{ $cat->defect_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Jenis Defect</label>
            <input type="text" name="jenis_defect" class="form-control" value="{{ old('jenis_defect', $sub->jenis_defect) }}" required>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('defect-subs.byCategory', $sub->id_defect_category) }}" class="btn btn-secondary">Batal</a>
    </form>
</x-card>
@endsection
