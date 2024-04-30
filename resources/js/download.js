import * as XLSX from "xlsx";
function getReadyOnGeneral(idTabel) {
    let headers = [];
    document
        .querySelectorAll(`#${idTabel} thead tr:nth-child(1) th:not(.deleted)`)
        .forEach((th) => {
            headers.push(th.textContent);
        });
    let contents = [];
    document.querySelectorAll(`#${idTabel} tbody tr`).forEach((row) => {
        let data = {};
        row.querySelectorAll("td:not(.deleted)").forEach((cell, index) => {
            let value = "";
            if (cell.children.length > 0) {
                cell.querySelectorAll("span").forEach((span) => {
                    value += span.textContent + ",";
                });
                value = value.slice(0, -1);
                value = value.replace(/\n|\s+/g, " ").trim();
            } else value = cell.textContent;
            data[headers[index]] = value;
        });
        contents.push(data);
    });
    contents.forEach(function (row, index) {
        // row.Komponen = row.Komponen.trim();
        for (let key in row) {
            let numericValue = parseFloat(row[key]);

            // Check if the parsed value is a number
            if (!isNaN(numericValue)) {
                // If it's a number, store the numeric value
                row[key] = numericValue;
            } else {
                // If it's not a number, store the original string value
                let value = row[key].trim();
                row[key] = value;
            }
        }
    });
    return contents;
}
function getReady() {
    let colspansElement = document.querySelector("#ColumnTabel thead tr th");
    let colspans;
    if (colspansElement) {
        colspans = Number(colspansElement.getAttribute("colspan"));
    }
    let headers = [];
    document
        .querySelectorAll(`#ColumnTabel thead tr:first-child th`)
        .forEach((th) => {
            headers.push(th.textContent);
        });
    headers = headers.map(function (header) {
        return header.trim();
    });
    let scndheaders = [];
    document
        .querySelectorAll(`#ColumnTabel thead tr:nth-child(2) th`)
        .forEach((th) => {
            scndheaders.push(th.textContent);
        });
    let mergedHeaders = [];
    let pembagi = scndheaders.length / headers.length;
    for (let i = 1; i <= headers.length; i++) {
        for (let j = 1; j <= pembagi; j++) {
            mergedHeaders.push(headers[i - 1] + "-" + scndheaders[j - 1]);
        }
    }
    let contents = [];
    document.querySelectorAll(`#ColumnTabel tbody tr`).forEach((row) => {
        let data = {};
        row.querySelectorAll("td").forEach((cell, index) => {
            let input = cell.querySelector('input')
            let value = input ? input.value : cell.textContent;
            let numericValue = parseFloat(value);
            if (!isNaN(numericValue)) {
                data[mergedHeaders[index]] = numericValue;
            } else {
                data[mergedHeaders[index]] = value;
            }
        });
        contents.push(data);
    });
    let komponens = [];
    let HeadersKomponen = document.querySelector(
        "#RowTabel th:nth-child(2)"
    ).textContent;
    document.querySelectorAll("#RowTabel tbody tr").forEach(function (row) {
        let data = {};
        row.querySelectorAll("td").forEach(function (cell, indeX) {
            let value = cell.textContent;
            data[HeadersKomponen] = value;
        });
        komponens.push(data);
    });
    if (komponens.length > 0) {
        komponens.forEach(function (row, index) {
            // row.Komponen = row.Komponen.trim();
            for (let key in row) {
                let numericValue = parseFloat(row[key]);

                // Check if the parsed value is a number
                if (!isNaN(numericValue)) {
                    // If it's a number, store the numeric value
                    row[key] = numericValue;
                } else {
                    // If it's not a number, store the original string value
                    let value = row[key].trim();
                    row[key] = value;
                }
            }
        });
        contents.forEach(function (row, index) {
            for (let key in row) {
                let numericValue = parseFloat(row[key]);

                // Check if the parsed value is a number
                if (!isNaN(numericValue)) {
                    // If it's a number, store the numeric value
                    row[key] = numericValue;
                } else {
                    // If it's not a number, store the original string value
                    let value = row[key].trim();
                    row[key] = value;
                }
            }
        });
        let merged = [];
        for (let i = 0; i < komponens.length; i++) {
            let mergeds = { ...komponens[i], ...contents[i] };
            merged.push(mergeds);
        }
        return merged;
    } else {
        contents.forEach(function (row, index) {
            // row.Komponen = row.Komponen.trim();
            for (let key in row) {
                let numericValue = parseFloat(row[key]);

                // Check if the parsed value is a number
                if (!isNaN(numericValue)) {
                    // If it's a number, store the numeric value
                    row[key] = numericValue;
                } else {
                    // If it's not a number, store the original string value
                    let value = row[key].trim();
                    row[key] = value;
                }
            }
        });
        return contents;
    }
}
function downloadExcel(data, titles) {
    var workbook = XLSX.utils.book_new();
    var worksheet = XLSX.utils.json_to_sheet(data);
    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet 1");

    // Convert the workbook to a binary Excel file
    var excelFile = XLSX.write(workbook, { type: "binary" });

    // Convert the binary Excel file to a Blob
    var blob = new Blob([s2ab(excelFile)], {
        type: "application/octet-stream",
    });

    // Create a download link
    var a = document.createElement("a");
    var url = URL.createObjectURL(blob);
    a.href = url;
    a.download = titles + ".xlsx";

    // Append the link to the document and trigger the download
    document.body.appendChild(a);
    a.click();

    // Clean up
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i = 0; i < s.length; i++) {
        view[i] = s.charCodeAt(i) & 0xff;
    }
    return buf;
}

export function GoDownload(idTabel, titles) {
    let datas = getReadyOnGeneral(idTabel);
    downloadExcel(datas, titles);
}
export function downloadTabel(titles) {
    let datas = getReady();
    downloadExcel(datas, titles);
}
