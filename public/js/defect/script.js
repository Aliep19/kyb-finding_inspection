document.addEventListener("DOMContentLoaded", function () {

// Fill Edit Modal
document.querySelectorAll(".editBtn").forEach(btn => {
    btn.addEventListener("click", function () {
        let id = this.dataset.id;
        let name = this.dataset.name;
        let jenis = this.dataset.jenis; // 👈 ambil dari data-jenis di tombol

        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_jenis").value = jenis; // 👈 set value select

        document.getElementById("editForm").action = "/defect/" + id;
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



