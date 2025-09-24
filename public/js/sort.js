function makeSortable(table) {
    const headers = table.querySelectorAll("th");
    headers.forEach((th, colIndex) => {
        let asc = true;
        th.style.cursor = "pointer";
        th.addEventListener("click", () => {
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.rows);

            rows.sort((a, b) => {
                let A = a.cells[colIndex].innerText.trim();
                let B = b.cells[colIndex].innerText.trim();

                // deteksi angka
                if (!isNaN(A) && !isNaN(B)) {
                    A = Number(A);
                    B = Number(B);
                }

                if (A < B) return asc ? -1 : 1;
                if (A > B) return asc ? 1 : -1;
                return 0;
            });

            rows.forEach(row => tbody.appendChild(row));
            asc = !asc; // toggle ASC/DESC
        });
    });
}

// buat semua tabel dengan class sortable jadi bisa di-sort
document.querySelectorAll("table.sortable").forEach(makeSortable);