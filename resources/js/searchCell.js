export function searchCell(Object, maxRows){
    // console.log(Object);
    Object.querySelectorAll(".search-input").forEach((inputField) => {
        const tableRows = inputField
            .closest("table")
            .querySelectorAll("tbody > tr");
        const headerCell = inputField.closest("td.search-header");
        const otherHeaderCells = headerCell.closest("tr").children;
        const columnIndex = Array.from(otherHeaderCells).indexOf(headerCell);
        const searchableCells = Array.from(tableRows).map(
            (row) => row.querySelectorAll("td")[columnIndex]
        );

        inputField.addEventListener("input", () => {
            const searchQuery = inputField.value.toLowerCase();
            // console.log(searchQuery);
            Object.querySelectorAll(".table-search tbody tr").forEach((row) => {
                row.style.display = ""; // Show the row
            });
            if (inputField.value === '')  {
                Object.querySelectorAll(".table-search tbody tr").forEach((row, index) => {
                    if (index >= maxRows) {
                        row.style.display = "none"; // Show the row
                    } // Show the row
                });
            }
            for (const tableCell of searchableCells) {
                const row = tableCell.closest("tr");
                const value = tableCell.textContent
                    .toLowerCase()
                    .replace(",", "");

                row.style.visibility = null;

                if (value.search(searchQuery) === -1) {
                    row.style.visibility = "collapse";
                }
            }
        });
    });
}