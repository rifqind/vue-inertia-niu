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
            let input = cell.querySelector("input");
            let value = input ? input.value : cell.textContent;
            let numericValue = parseFloat(value);
            if (!isNaN(numericValue)) {
                // let val = value.replace(/[^0-9]/g, '');
                let val = value.replace(/\./g, "").replace(/,/g, ".");
                data[mergedHeaders[index]] = Number(val);
            } else {
                data[mergedHeaders[index]] = String(value);
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
                // console.log(row[key])
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
function tableToJson(idTabel) {
    const table = document.getElementById(idTabel);
    const headers = [];
    const rows = [];
    const groupHeaders = [];
    const subGroupHeaders = [];

    // Get the header rows
    const headerRows = table.querySelectorAll('thead tr');

    // First row: Group headers
    const groupRow = headerRows[0].querySelectorAll('th');
    groupRow.forEach(th => {
        const groupHeader = {
            text: th.innerText,
            colspan: parseInt(th.getAttribute('colspan') || 1),
            rowspan: parseInt(th.getAttribute('rowspan') || 1)
        };
        groupHeaders.push(groupHeader);
    });

    // Second row: Sub Group headers
    const subGroupRow = headerRows[1].querySelectorAll('th');
    subGroupRow.forEach(th => {
        const subGroupHeader = th.innerText;
        subGroupHeaders.push(subGroupHeader);
    });

    headers.push(groupHeaders, subGroupHeaders);
    const rowLabel = groupHeaders[0].text;
    // Get the body rows
    const bodyRows = table.querySelectorAll('tbody tr');
    bodyRows.forEach((row) => {
        const rowData = {};
        const cells = row.querySelectorAll('td');

        rowData[rowLabel] = cells[0].innerText; // first column data
        let groupIndex = 1; // Start after "first column"

        // Dynamically map data under the correct groups
        groupHeaders.slice(1).forEach(group => {
            const groupData = {};
            for (let i = 0; i < group.colspan; i++) {
                let input = cells[groupIndex].querySelector("input");
                let value = input ? input.value : cells[groupIndex].textContent;
                let numericValue = parseFloat(value);
                if (!isNaN(numericValue)) {
                    // let val = value.replace(/[^0-9]/g, '');
                    let val = value.replace(/\./g, "").replace(/,/g, ".");
                    groupData[subGroupHeaders[groupIndex - 1]] = Number(val);
                } else {
                    groupData[subGroupHeaders[groupIndex - 1]] = String(value);
                }
                // groupData[subGroupHeaders[groupIndex - 1]] = cells[groupIndex].innerText;
                groupIndex++;
            }
            rowData[group.text] = groupData;
        });

        rows.push(rowData);
    });

    return {
        headers: headers,
        rows: rows
    };
}
export function newDownload(idTabel, titles) {

    const jsonData = tableToJson(idTabel)
    var workbook = XLSX.utils.book_new();
    let test = []
    // Build the header rows dynamically
    var bigColspan
    jsonData.headers.forEach((headerRow, index) => {
        const row = [];
        if (index == 1) row.push('')
        headerRow.forEach((header) => {
            if (header.colspan) bigColspan = header.colspan
            if (header.text) row.push(header.text)
            else row.push(header)
            // row.push(header.text);  // Push header text
            for (let i = 1; i < (header.colspan || 1); i++) {
                row.push('');  // Push empty cells for colspan
            }
        });
        test.push(row);  // Add the constructed header row
    });
    let rowLabel = test[0][0]
    jsonData.rows.forEach((dataRow, index) => {
        const row = [];
        row.push(dataRow[rowLabel])
        // Loop through each header group in the jsonData
        for (let group of jsonData.headers[0].slice(1)) {
            // For each sub-group in the dataRow, fetch the data
            for (let subGroup of Object.keys(dataRow[group.text])) {
                row.push(dataRow[group.text][subGroup]);
            }
        }
        test.push(row); // Add the constructed data row
    });

    const worksheet = XLSX.utils.aoa_to_sheet(test);
    const merges = [];
    jsonData.headers.forEach((row, rowIndex) => {
        if (rowIndex == 0) {
            row.forEach((header, colIndex) => {
                if (header.rowspan > 1) {
                    merges.push({
                        s: { r: rowIndex, c: colIndex },
                        e: { r: rowIndex + 1, c: colIndex }
                    });
                }
                if (header.colspan > 1) {
                    let number = 1 + (bigColspan * (colIndex - 1))
                    if (number < 0) number = 0
                    merges.push({
                        s: { r: rowIndex, c: number },
                        e: { r: rowIndex, c: number + bigColspan - 1 }
                    });
                }
            });
        }
    });
    worksheet['!merges'] = merges;
    // const table = document.getElementById(idTabel)
    // var ws = XLSX.utils.table_to_sheet(table)
    // var wb = XLSX.utils.table_to_book(table)
    // XLSX.utils.book_append_sheet(workbook, ws, "Sheet 1");
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
function aoaToJson(aoa) {
    const headers = aoa[0]; // First row as headers
    const data = aoa.slice(1); // Remaining rows as data
  
    const jsonData = data.map(row => {
      let obj = {};
      row.forEach((cell, index) => {
        obj[headers[index]] = cell;
      });
      return obj;
    });
  
    return jsonData;
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
    try {
        let datas = getReadyOnGeneral(idTabel);
        downloadExcel(datas, titles);
    } catch (error) {
        alert("Error : Data tidak sesuai format");
    }
}
export function downloadTabel(titles) {
    try {
        let datas = getReady();
        downloadExcel(datas, titles);
    } catch (error) {
        alert("Error : Data tidak sesuai format");
    }
}
