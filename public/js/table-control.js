document.addEventListener("DOMContentLoaded", function () {
    const url = new URL(window.location.href);

    // Search input handler
    document.querySelectorAll(".search-input").forEach(function (input) {
        input.addEventListener("keyup", function (e) {
            if (e.key === "Enter") {
                url.searchParams.set("search", input.value);
                url.searchParams.set("page", 1); // reset ke halaman 1
                window.location.href = url.toString();
            }
        });
    });

    // Entries selector handler
    document.querySelectorAll(".entries-selector").forEach(function (select) {
        select.addEventListener("change", function () {
            url.searchParams.set("per_page", this.value);
            url.searchParams.set("page", 1); // reset ke halaman 1
            window.location.href = url.toString();
        });
    });
});
