<script setup>
import { Link, Head, usePage, useForm } from '@inertiajs/vue3';
import { Teleport, onMounted, ref, watch, defineComponent } from 'vue';
import Multiselect from "@vueform/multiselect";
import draggable from 'vuedraggable'
import { clickSortProperties } from '@/sortAttribute';
import Pagination from '@/Components/Pagination.vue'
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import { GoDownload } from '@/download'
import { computed } from 'vue';
import axios from 'axios';

defineComponent({
    Multiselect, draggable
})
const page = usePage()
var tGroup = page.props.tables
var tables = ref(tGroup)
const form = useForm({
    id: null,
    _token: null,
})
const currentRoute = page.props.route

const triggerSpinner = ref(false)
const toggleFlash = ref(false)
const deleteModalStatus = ref(false)
const downloadModalStatus = ref(false)
const orderModalStatus = ref(false)
const downloadTitle = ref(null)

const searchLabel = ref(null)
const searchLabelDinas = ref(null)
const searchColumnList = ref(null)
const searchRowLabel = ref(null)
const searchTahun = ref(null)
const searchStatus = ref(null)
const searchUpdated = ref(null)

const tabelTabels = ref(null)

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel },
    { key: 'nama_dinas', valueFilter: searchLabelDinas },
    { key: 'columns', valueFilter: searchColumnList },
    { key: 'row_label', valueFilter: searchRowLabel },
    { key: 'tahun', valueFilter: searchTahun },
    { key: 'status', valueFilter: searchStatus },
    { key: 'updated', valueFilter: searchUpdated },
]
const debounce = (func, wait = 400) => {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(this, args);
        }, wait);
    };
}
// const filteredColumns = computed(() => {
//     let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
//     if (filters.length === 0) {
//         return page.props.tables
//     }
//     return page.props.tables.filter(item => {
//         return filters.every(obj => {
//             const filterValue = obj.valueFilter.value.toLowerCase()
//             if (obj.key == 'updated') {
//                 return `${item['who_updated']} ${item['status_updated']}`.toLowerCase().includes(filterValue);
//             } else if (obj.key == 'columns') {
//                 let check = item[obj.key].filter(X => {
//                     let result = X.label.toLowerCase().includes(filterValue)
//                     return result
//                 })
//                 if (check.length > 0) return true
//             }
//             else {
//                 return item[obj.key].toLowerCase().includes(filterValue)
//             }
//         })
//     })
// })
const delayedFetchData = debounce(() => {
    fetchData()
})
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    currentPage.value = 1
    delayedFetchData()
})
onMounted(() => {
    if (page.props.flash.message) {
        toggleFlash.value = true
    }
    // searchCell(tabelTabels.value, 10)
})
const deleteForm = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
    form.post(route('tabel.statusDestroy'), {
        onBefore: function () {
            triggerSpinner.value = true
            deleteModalStatus.value = false
        },
        onFinish: function () { triggerSpinner.value = false },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            form.reset()
        },
        onError: function () { deleteModalStatus.value = true }
    })
}
const order = useForm({
    id: null,
    _token: null,
    orders: {
        rowOrder: null,
        columnOrder: null,
    }
})
const changeOrder = async () => {
    const response = await axios.get(route('token'))
    order._token = response.data
    order.orders.columnOrder = currentColumnOrder.value
    order.orders.rowOrder = currentRowOrder.value
    if (order.processing) return
    order.post(route('order.changeOrder'), {
        onBefore: function () {
            triggerSpinner.value = true
            orderModalStatus.value = false
        },
        onFinish: function () {
            triggerSpinner.value = false; orderDropColumn.value = 2
        },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            order.reset()
        },
        onError: function () { orderModalStatus.value = true }
    })
}
const currentColumnOrder = ref(null)
const currentRowOrder = ref(null)
const toggleModalOrders = async (id) => {
    orderModalStatus.value = true
    order.id = id
    try {
        const response = await axios.get('/order/fetch/' + id)
        // console.log(response.data)
        currentColumnOrder.value = response.data.columnList
        if (response.data.rowList) currentRowOrder.value = response.data.rowList
    } catch (error) {
        console.error('Error fetching data: ', error)
    }
}
const orderDropColumn = ref(2)
const setupOrderColumn = (value) => {
    if (value == 1) {
        orderDropColumn.value = 1
    } else {
        orderDropColumn.value = 2
    }
}
const orderDropRow = ref(2)
const setupOrderRow = (value) => {
    if (value == 1) {
        orderDropRow.value = 1
    } else {
        orderDropRow.value = 2
    }
}
//new Pagination
const showItems = ref(10)
const currentPage = ref(1)

// const updateShowItems = (value) => {
//     showItems.value = value
// }
// const updateCurrentPage = (value) => {
//     currentPage.value = value
// }
// const paginatedData = computed(() => {
//     const start = (currentPage.value - 1) * showItems.value
//     const end = start + showItems.value
//     return tables.value.slice(start, end)
// })
// watch(() => page.props.tables, (value) => {
//     tables.value = [...value]
// })
const updateShowItems = (value) => {
    showItems.value = value
    fetchData()
}
const updateCurrentPage = (value) => {
    currentPage.value = value
    fetchData()
}
const totalItems = ref(page.props.countData)
watch(() => page.props.countData, (value) => {
    totalItems.value = value
})
const paginatedData = computed(() => {
    // const start = (currentPage.value - 1) * showItems.value
    // const end = start + showItems.value
    // return filteredColumns.value.slice(start, end)
    return tables.value
})
watch(() => page.props.tables, (value) => {
    tables.value = value
})
const fetchData = async () => {
    try {
        const response = await axios.get(route('tabel.index'), {
            params: {
                currentPage: currentPage.value, paginated: showItems.value,
                ArrayFilter: {
                    label: searchLabel.value,
                    nama_dinas: searchLabelDinas.value,
                    columns: searchColumnList.value,
                    row_label: searchRowLabel.value,
                    tahun: searchTahun.value,
                    status: searchStatus.value,
                    updated: searchUpdated.value,
                },
                routeName: page.props.route
            }
        })
        tables.value = response.data.tables
        indexExpanded.value = Array(tables.value.length).fill(false)
        totalItems.value = response.data.countData
        // currentPage.value = response.data
    } catch (error) {
        console.error('Error fetching data: ', error)
    }
}
const openRowList = (index) => {
    if (index < 3) return true
    else return false
}
    
const indexExpanded = ref(Array(paginatedData.value.length).fill(false))
const openOtherRow = (index) => {
    indexExpanded.value[index] = !indexExpanded.value[index]
}
</script>
<template>

    <Head title="Daftar Tabel" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Tabel
                </div>
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" />
                    Download</button>
            </div>
        </div>
        <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
        <table class="table table-sorted table-hover table-bordered table-search" ref="tabelTabels" id="tabel-kolom">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column text-center align-middle" @click="clickSortProperties(tables, 'number')">No.
                    </th>
                    <th class="text-center align-middle tabel-width-20" @click="clickSortProperties(tables, 'label')">
                        Nama Tabel
                    </th>
                    <th class="text-center align-middle tabel-width-20"
                        @click="clickSortProperties(tables, 'nama_dinas')">
                        Produsen Data
                    </th>
                    <th class="text-center align-middle tabel-width-20">
                        Daftar Kolom
                    </th>
                    <th class="text-center align-middle" @click="clickSortProperties(tables, 'row_label')">
                        Daftar Baris
                    </th>
                    <th class="text-center align-middle" @click="clickSortProperties(tables, 'tahun')">
                        Tahun
                    </th>
                    <th class="text-center align-middle" @click="clickSortProperties(tables, 'status')">
                        Status Pengisian
                    </th>
                    <th class="text-center align-middle" @click="clickSortProperties(tables, 'status_updated')">
                        Terakhir di-update
                    </th>
                    <th class="text-center align-middle tabel-width-5">
                        Cek/Hapus
                    </th>
                </tr>
                <tr class="">
                    <td class="search-header"></td>
                    <td class="search-header"><input v-model.trim="searchLabel" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchLabelDinas" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input type="text" v-model.trim="searchColumnList"
                            class="search-input form-control"></td>
                    <td class="search-header"><input type="text" v-model.trim="searchRowLabel"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchTahun" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchStatus" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchUpdated" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header deleted"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-if="tables.length > 0" v-for="(table, index) in paginatedData" :key="index">
                    <td class="align-middle">{{ table.number }}</td>
                    <td class="align-middle">{{ table.label }}</td>
                    <td class="align-middle">{{ table.nama_dinas }}</td>
                    <td class="align-middle"><span v-for="(col, colIndex) in table.columns" :key="colIndex"
                            class="badge mr-1 badge-info">{{
        col.label }}</span></td>
                    <!-- <td class="align-middle">{{ table.row_label }}</td> -->
                    <td class="align-middle">
                        <template v-for="(row, rowIndex) in table.rowInputs" :key="rowIndex">
                            <template v-if="row.wilayah_fullcode">
                                <span v-if="indexExpanded[index] || openRowList(rowIndex)"
                                    class="badge mr-1 badge-info">{{
        row.label }}</span>
                            </template>
                            <template v-else>
                                <span class="badge mr-1 badge-info">{{ row.label }}</span>
                            </template>
                        </template>
                        <span v-if="table.rowInputs[0].wilayah_fullcode" class="badge mr-1 badge-info"
                            @click="openOtherRow(index)">
                            <font-awesome-icon v-if="!indexExpanded[index]" icon="fa-solid fa-angle-down" />
                            <font-awesome-icon v-if="indexExpanded[index]" icon="fa-solid fa-angle-up" />
                        </span>
                    </td>

                    <td class="align-middle">{{ table.tahun }}</td>
                    <td class="align-middle">{{ table.status }}</td>
                    <td class="text-center align-middle"><span class="badge badge-info">{{ table.who_updated
                            }}</span><br><span>{{ table.status_updated }}</span></td>
                    <td class="text-center align-middle deleted">
                        <Link :href="route('tabel.entri', { id: table.id_statustables })" class="edit-pen mx-1">
                        <font-awesome-icon icon="fa-solid fa-pencil" title="Cek/Edit" />
                        </Link>
                        <a @click.prevent="toggleModalOrders(table.id_statustables)"><font-awesome-icon
                                icon="fa-solid fa-sort" class="edit-pen mx-1" title="Edit Urutan Kolom/Baris" /></a>
                        <a v-if="(currentRoute == 'tabel.index' && (page.props.auth.user.role == 'admin' || page.props.auth.user.role == 'kominfo'))"
                            @click.prevent="() => {
        deleteModalStatus = true;
        form.id = table.id_statustables
    }" class="delete-trash">
                            <font-awesome-icon icon="fa-solid fa-trash-can" class="icon-trash-color mx-1"
                                title="Hapus" />
                        </a>
                    </td>
                </tr>
                <tr v-else>
                    <td colspan="9" class="text-center">Tidak Ada Data</td>
                </tr>
            </tbody>
        </table>
        <Teleport to="body">
            <ModalBs :-modal-status="downloadModalStatus" @close="downloadModalStatus = false" :title="'Download Data'">
                <template #modalBody>
                    <label>Masukkan Judul File</label>
                    <input type="text" v-model="downloadTitle" class="form-control" />
                </template>
                <template #modalFunction>
                    <button type="button" class="btn btn-sm bg-success-fordone"
                        @click.prevent="GoDownload('tabel-kolom', downloadTitle)">Simpan</button>
                </template>
            </ModalBs>
            <ModalBs :ModalStatus="deleteModalStatus" @close="function () {
        deleteModalStatus = false
        form.reset()
    }" :title="'Hapus Tabel'">
                <template v-slot:modalBody>
                    <label>Apakah Anda yakin akan menghapus Tabel tahun ini?</label>
                </template>
                <template v-slot:modalFunction>
                    <button type="button" class="btn btn-sm badge-status-empat" :disabled="deleteForm.processing"
                        @click.prevent="deleteForm">Hapus</button>
                </template>
            </ModalBs>
            <ModalBs :-modal-status="orderModalStatus"
                @close="orderModalStatus = false; currentColumnOrder = null; currentRowOrder = null; order.reset(); orderDropColumn = 2; orderDropRow = 2"
                :title="'Ganti Urutan Kolom atau Baris'">
                <template #modalBody>
                    <form>
                        <div class="form-group">
                            <div class="mb-3">
                                <label>Urutan Kolom</label>
                                <Multiselect class="mb-3" placeholder="Apakah ada perubahan urutan?" :value="2"
                                    @change="setupOrderColumn"
                                    :options="[{ label: 'Ada perubahan', value: '1' }, { label: 'Sudah sesuai', value: '2' }]" />
                                <table v-if="orderDropColumn == 1" class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Daftar Kolom</th>
                                        </tr>
                                    </thead>
                                    <draggable v-model="currentColumnOrder" tag="tbody" item-key="label">
                                        <template #item="{ element }">
                                            <tr>
                                                <td>{{ element.label }}</td>
                                            </tr>
                                        </template>
                                    </draggable>
                                </table>
                            </div>
                            <div class="mb-3">
                                <label>Urutan Baris</label>
                                <template v-if="currentRowOrder">
                                    <Multiselect class="mb-3" placeholder="Apakah ada perubahan urutan?" :value="2"
                                        @change="setupOrderRow"
                                        :options="[{ label: 'Ada perubahan', value: '1' }, { label: 'Sudah sesuai', value: '2' }]" />
                                    <table v-if="orderDropRow == 1" class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Daftar Baris</th>
                                            </tr>
                                        </thead>
                                        <draggable v-model="currentRowOrder" tag="tbody" item-key="label">
                                            <template #item="{ element }">
                                                <tr>
                                                    <td>{{ element.label }}</td>
                                                </tr>
                                            </template>
                                        </draggable>
                                    </table>
                                </template>
                                <template v-else>
                                    <div>Tidak bisa mengubah urutan baris untuk data ini</div>
                                </template>
                            </div>
                        </div>
                    </form>
                </template>
                <template #modalFunction>
                    <button id="" type="button" class="btn btn-sm bg-success-fordone" :disabled="order.processing"
                        @click.prevent="changeOrder">Simpan</button>
                </template>
            </ModalBs>
        </Teleport>
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="totalItems" :current-page="currentPage" />
    </GeneralLayout>
</template>
<style scoped>
table {
    font-size: smaller;
}
</style>