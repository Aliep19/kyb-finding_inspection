    @extends('layouts.app')

    @section('content')
    <x-card title="Tambah Jenis Defect" icon="fa-solid fa-plus">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('defect-subs.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Defect Category</label>
                <select name="id_defect_category" class="form-control" required>
                    <option value="">-- Pilih Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ (old('id_defect_category', $selectedCategory ?? '') == $cat->id) ? 'selected' : '' }}>
                            {{ $cat->defect_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Defect</label>
                <input type="text" name="jenis_defect" class="form-control" value="{{ old('jenis_defect') }}" required>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('defect-subs.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </x-card>
    @endsection
