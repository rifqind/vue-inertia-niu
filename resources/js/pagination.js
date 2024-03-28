export function getPagination(
    tabelDinas,
    currentPagination,
    maxRows,
    statusText,
    currentStatusText,
    rowsTabel
) {
    var trnum = 0;
    let listLiTmp = currentPagination.value.querySelectorAll("li");
    listLiTmp.forEach(function (e, index) {
        if (e.getAttribute("id") == null) {
            e.remove();
        }
    });
    let currentMaxRows = maxRows;
    if (currentMaxRows > rowsTabel) {
        currentStatusText.querySelector("#showPage").textContent = rowsTabel;
    } else {
        currentStatusText.querySelector("#showPage").textContent =
            currentMaxRows;
    }
    currentStatusText.querySelector("#showTotal").textContent = rowsTabel;
    if (rowsTabel > 0) {
        var totalPageNum = Math.ceil(rowsTabel / currentMaxRows);
        for (let i = 1; i <= totalPageNum; ) {
            let li = document.createElement("li");
            li.setAttribute("data-page", i);
            if (i == 1) li.classList.add("active");
            li.innerHTML =
                "<span>" +
                i++ +
                '<span class="sr-only">(current)</span></span>';
            currentPagination.value.insertBefore(
                li,
                document.getElementById("prev")
            );
        }
    }
    var lastPage = 0;
    let listLi = currentPagination.value.querySelectorAll("li");
    listLi.forEach(function (list) {
        list.addEventListener("click", function (e) {
            e.stopImmediatePropagation();
            e.preventDefault();
            let currentPageNum = this.getAttribute("data-page");
            let currentRows = parseInt(currentMaxRows);
            if (currentPageNum == "prev") {
                if (lastPage == 1) {
                    return;
                }
                currentPageNum = --lastPage;
            }
            if (currentPageNum == "next") {
                if (lastPage == currentPagination.value.children.length - 2) {
                    return;
                }
                currentPageNum = ++lastPage;
            }
            let trIndex = 0;
            lastPage = currentPageNum;
            listLi.forEach(function (e, index) {
                if (
                    index !== 0 &&
                    index !== currentPagination.value.children.length - 1
                ) {
                    e.classList.remove("active");
                }
            });
            currentPagination.value
                .querySelector("[data-page='" + lastPage + "']")
                .classList.add("active");
            tabelDinas.value.querySelectorAll("tbody tr").forEach(function (e) {
                trIndex++;
                if (
                    trIndex > currentRows * currentPageNum ||
                    trIndex <= currentRows * currentPageNum - currentRows
                ) {
                    e.style.display = "none";
                } else {
                    e.style.display = "";
                }
            });
        });
    });
    tabelDinas.value.querySelectorAll("tbody tr").forEach(function (e) {
        trnum++;
        if (trnum > currentMaxRows) {
            e.style.display = "none";
        }
        if (trnum <= currentMaxRows) {
            e.style.display = "";
        }
    });
    let activePage = parseInt(
        currentPagination.value
            .querySelector(".active")
            .getAttribute("data-page")
    );
    if (currentPagination.value.querySelectorAll("li").length > 7) {
        if (activePage <= 3) {
            currentPagination.value
                .querySelectorAll("li")
                .forEach(function (e, index) {
                    if (index > 5) {
                        e.style.display = "none";
                    } else {
                        e.style.display = "";
                    }
                });
            currentPagination.value.querySelector(
                '[data-page="next"]'
            ).style.display = "block";
        }
        if (activePage > 3) {
            currentPagination.value
                .querySelectorAll("li")
                .forEach(function (e, index) {
                    if (index > 0) {
                        e.style.display = "none";
                    } else {
                        e.style.display = "";
                    }
                });
            currentPagination.value.querySelector(
                '[data-page="next"]'
            ).style.display = "block";
            for (let i = activePage - 2; i <= activePage + 2; i++) {
                currentPagination.value.querySelector(
                    "[data-page='" + i + "']"
                ).style.display = "block";
            }
        }
    }
    return currentMaxRows
}
