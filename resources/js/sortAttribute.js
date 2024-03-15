var sortOrder = 1
const sortByProperty = function (x) {
    return function (a, b) {
        const aValue = isNaN(a[x]) ? a[x] : parseFloat(a[x])
        const bValue = isNaN(b[x]) ? b[x] : parseFloat(b[x])
        if (aValue < bValue) {
            return -1 * sortOrder
        }
        if (aValue > bValue) {
            return 1 * sortOrder
        }
        return 0
    }
}

export function clickSortProperties (object, attributes) {
    sortOrder *= -1
    object.sort(sortByProperty(attributes))
}