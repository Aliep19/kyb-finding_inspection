{{-- MODAL ADD --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('defect.store') }}" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Defect Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label>Defect Category Name</label>
                <input type="text" name="defect_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Jenis Defect</label>
                <select name="jenis_defect" class="form-control" required>
                    <option value="">-- Pilih Category --</option>
                    <option value="1">Painting</option>
                    <option value="0">Not Painting</option>
                </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
    </form>
  </div>
</div>


{{-- MODAL EDIT --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Defect Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
                <label>Defect Category Name</label>
                <input type="text" name="defect_name" id="edit_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Jenis Defect</label>
                <select name="jenis_defect" id="edit_jenis" class="form-control" required>
                    <option value="1">Painting</option>
                    <option value="0">Not Painting</option>
                </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
    </form>
  </div>
</div>
