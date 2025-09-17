{{-- Modal Tambah --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('targets.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5>Tambah Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Department</label>
                        <select name="department_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach($departments as $dw)
                                <option value="{{ $dw->id }}">{{ $dw->dept_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Target</label>
                        <input type="number" name="target_value" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Dari Bulan</label>
                            <select name="start_month" class="form-select" required>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Tahun</label>
                            <input type="number" name="start_year" value="{{ date('Y') }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Sampai Bulan</label>
                            <select name="end_month" class="form-select" required>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Tahun</label>
                            <input type="number" name="end_year" value="{{ date('Y') }}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit (Looping per target) --}}
@foreach($targets as $target)
<div class="modal fade" id="editModal{{ $target->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('targets.update', $target->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5>Edit Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Sub Workstation</label>
                        <select name="department_id" class="form-select" required>
                            @foreach($departments as $dw)
                                <option value="{{ $dw->id }}" {{ $dw->id == $target->department_id ? 'selected' : '' }}>
                                    {{ $dw->dept_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Target</label>
                        <input type="number" name="target_value" value="{{ $target->target_value }}" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Dari Bulan</label>
                            <select name="start_month" class="form-select" required>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" {{ $m == $target->start_month ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Tahun</label>
                            <input type="number" name="start_year" value="{{ $target->start_year }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Sampai Bulan</label>
                            <select name="end_month" class="form-select" required>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" {{ $m == $target->end_month ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Tahun</label>
                            <input type="number" name="end_year" value="{{ $target->end_year }}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
