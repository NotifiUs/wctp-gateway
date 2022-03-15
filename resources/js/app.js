require("./bootstrap");

window.addEventListener('DOMContentLoaded', (loaded) => {
    let tables = document.querySelectorAll('.table-responsive');

    tables.forEach( (table) => {
        table.addEventListener('show.bs.dropdown', (show) => {
            table.style.overflow = 'inherit';
        });
        table.addEventListener('hide.bs.dropdown', (show) => {
            table.style.overflow = 'auto';
        });
    });
});
