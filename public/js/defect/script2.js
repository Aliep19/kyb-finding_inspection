document.addEventListener("DOMContentLoaded", function () {
    // Fill Edit Modal
    document.querySelectorAll(".editBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            let id = this.dataset.id;
            let ct = this.dataset.ct;
            let name = this.dataset.name;

            document.getElementById("edit_id").value = id;
            document.getElementById("edit_name").value = name;
            document.getElementById("edit_defect_category").value = ct;
            document.getElementById("editForm").action = "/defect_type/" + id;
        });
    });

    // Delete Confirmation
    document.querySelectorAll(".deleteBtn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            let form = this.closest("form");
            Swal.fire({
                title: "Hapus data?",
                text: "Data akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // SweetAlert success dari session
    if (window.sessionSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: window.sessionSuccess
        });
    }
});
