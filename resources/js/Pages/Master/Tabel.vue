<script setup>
import { Link, Head, usePage, useForm } from '@inertiajs/vue3';
import { Teleport, defineComponent, computed, ref, watch } from 'vue';
import { clickSortProperties } from '@/sortAttribute';
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Multiselect from "@vueform/multiselect";
import { GoDownload } from '@/download'
import Pagination from '@/Components/Pagination.vue';

defineComponent({
    Multiselect,
})

const page = usePage()
var tGroup = page.props.tables
var tables = ref(tGroup)
const form = useForm({
    id: null,
    tahun: null,
    _token: null,
})
const duplicateForm = useForm({
    id: null,
    judul: null,
    produsen: null,
    _token: null,
    rows: {
        tipe: 1,
        selected: [],
    }
})
const yearDrop = ref({
    value: null,
    options: [],
});
const currentYear = new Date().getFullYear();
const years = Array.from({ length: 11 }, (_, index) => currentYear - index);
yearDrop.value.options = years.map((year) => ({
    label: year.toString(),
    value: year.toString(),
}));

const triggerSpinner = ref(false)
const toggleFlash = ref(false)
const toggleFlashError = ref(false)
const deleteModalStatus = ref(false)
const addYearModalStatus = ref(false)
const downloadModalStatus = ref(false)
const duplicateModalStatus = ref(false)
const downloadTitle = ref(null)

const searchLabel = ref(null)
const searchLabelDinas = ref(null)
const searchColumnList = ref(null)
const searchTahun = ref(null)
const searchStatus = ref(null)
const searchUpdated = ref(null)
const searchRowLabel = ref(null)

const tabelTabels = ref(null)
const tingkatanDrop = ref({})
const kabsDrop = ref({
    value: null,
    options: [],
})
const kecsDrop = ref({
    value: null,
    options: [],
})
const desaDrop = ref({
    value: null,
    options: [],
})
const rowListFetched = ref([]);
const rowsCheckBox = ref([]);

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel },
    { key: 'nama_dinas', valueFilter: searchLabelDinas },
    { key: 'columns', valueFilter: searchColumnList },
    { key: 'row_label', valueFilter: searchRowLabel },
    { key: 'tahun', valueFilter: searchTahun },
    { key: 'status', valueFilter: searchStatus },
    { key: 'updated', valueFilter: searchUpdated },
]

// const debounce = (func, wait = 400) => {
//     let timeout;
//     return function (...args) {
//         clearTimeout(timeout);
//         timeout = setTimeout(() => {
//             func.apply(this, args);
//         }, wait);
//     };
// }

const delayedFetchData = debounce(() => {
    fetchData()
})
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
//             } else if (obj.key == 'tahuns') {
//                 let check = item[obj.key].filter(X => {
//                     let result = X.toLowerCase().includes(filterValue)
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
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    // tables.value = filteredColumns.value
    currentPage.value = 1
    delayedFetchData()
})
const ProdusenFetched = ref([])
const isWilayahFullcodes = ref(null)
const patchTabel = async (id_tabel) => {
    duplicateModalStatus.value = true
    const response = await axios.get(route('fetchMaster', { id: id_tabel }))
    // console.log(response.data)
    duplicateForm.id = id_tabel
    duplicateForm.produsen = response.data.tabel.id_dinas
    duplicateForm.judul = response.data.tabel.label
    ProdusenFetched.value = response.data.dinas
    isWilayahFullcodes.value = response.data.isWilayahFullcodes
    kabsDrop.value.options = (response.data.kab.length > 1) ? response.data.kab.slice(1) : response.data.kab
    tingkatanDrop.value = {
        value: null,
        options: [
            { label: "Kecamatan", value: 2 },
            { label: "Desa/Kelurahan", value: 3 },
        ],
    }
}
const loadKecamatans = async (valueKabs) => {
    if (valueKabs) {
        try {
            let kabs = valueKabs.substring(2, 4);
            const response = await axios.get("/master/wilayah/kecamatan/" + kabs);
            kecsDrop.value.options = response.data.data;
            let tempt = kabsDrop.value.options.filter((x) =>
                x.value.includes(valueKabs)
            );
            let parents = {
                label: tempt[0].label,
                value: tempt[0].value,
            };
            assignRowListWilayah(2, parents);
        } catch (error) {
            console.error("Error fetching kecamatan:", error);
        }
    }
};
const loadDesa = async (valueKecs) => {
    if (valueKecs) {
        try {
            let kabsFetch = valueKecs.substring(2, 4);
            let kecsFetch = valueKecs.substring(4, 7);
            const response = await axios.get(
                "/master/wilayah/desa/" + kabsFetch + "/" + kecsFetch
            );
            desaDrop.value.options = response.data.data;
            let tempt = kecsDrop.value.options.filter((x) => {
                return x.value.includes(valueKecs)
            })
            let parents = {
                label: tempt[0].label,
                value: tempt[0].value,
            }
            assignRowListWilayah(3, parents);
        } catch (error) {
            console.error("Error fetching desa:", error);
        }
    }
};
const toggleAll = function (object) {
    object.forEach((_, index) => {
        object[index] = !object[index];
    });
};
const assignRowListWilayah = function (options, parents) {
    rowListFetched.value = []
    switch (options) {
        case 2:
            let kecLists = kecsDrop.value.options;
            kecLists.push(parents);
            kecLists.map((item, key, array) => {
                item.tipe =
                    key === array.length - 1 ? "KABUPATEN/KOTA" : "KECAMATAN";
            });
            rowListFetched.value = kecLists;
            break;
        case 3:
            let desaLists = desaDrop.value.options;
            desaLists.push(parents);
            desaLists.map((item, key, array) => {
                item.tipe =
                    key === array.length - 1 ? "KECAMATAN" : "DESA/KELURAHAN";
            });
            rowListFetched.value = desaLists;
            break;

        default:
            break;
    }
    rowsCheckBox.value = Array(rowListFetched.value.length).fill(false);
};
const closeDuplicateModal = () => {
    duplicateModalStatus.value = false
    duplicateForm.reset()
    kabsDrop.value.options = []
    kecsDrop.value.options = []
    rowListFetched.value = []
    rowsCheckBox.value = []
}
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
const submit = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
    form.post(route('tabel.storeCopy'), {
        onBefore: function () {
            triggerSpinner.value = true
            addYearModalStatus.value = false
        },
        onFinish: function () { triggerSpinner.value = false },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            if (page.props.flash.error) toggleFlashError.value = true
            form.reset()
            fetchData()
        },
        onError: function () { addYearModalStatus.value = true }
    })
}
const duplicate = async () => {
    duplicateForm.rows.selected = rowListFetched.value.filter((_, index) => {
        return rowsCheckBox.value[index]
    })
    const response = await axios.get(route('token'))
    duplicateForm._token = response.data
    if (duplicateForm.processing) return
    duplicateForm.post(route('duplicateMaster'), {
        onBefore: function () {
            triggerSpinner.value = true
            duplicateModalStatus.value = false
        },
        onFinish: function () { triggerSpinner.value = false },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            if (page.props.flash.error) toggleFlashError.value = true
            duplicateForm.reset()
            fetchData()
        },
        onError: function () { duplicateModalStatus.value = true }
    })
}
//new Pagination
const showItems = ref(10)
// const showItemsValue = ref(10)
// const showItems = computed(() => {
// if (tables.value.length < 10) return tables.value.length
// return showItemsValue.value
// })
const currentPage = ref(1)

const updateShowItems = (value) => {
    // if (value > tables.value.length) showItemsValue.value = tables.value.length
    // else showItemsValue.value = value
    // currentPage.value = 1
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
    return tables.value
})
watch(() => page.props.tables, (value) => {
    tables.value = value
})
const orderAttribute = ref({
    before: null,
    label: null,
    value: 'asc',
})
const clickToOrder = (value) => {
    orderAttribute.value.label = value
    if (orderAttribute.value.before == null || orderAttribute.value.before == value) {
        if (orderAttribute.value.value == 'asc') orderAttribute.value.value = 'desc'
        else if (orderAttribute.value.value == 'desc') orderAttribute.value.value = null
        else orderAttribute.value.value = 'asc'
    } else orderAttribute.value.value = 'asc'
    orderAttribute.value.before = value
    fetchData()
}
const fetchData = async () => {
    try {
        const response = await axios.get(route('tabel.master'), {
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
                orderAttribute: orderAttribute.value,
                routeName: page.props.route
            }
        })
        tables.value = response.data.tables
        indexExpandedRow.value = Array(tables.value.length).fill(false)
        indexExpandedCol.value = Array(tables.value.length).fill(false)
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
const openColList = (index) => {
    if (index < 3) return true
    else return false
}

const indexExpandedRow = ref(Array(paginatedData.value.length).fill(false))
const openOtherRow = (index) => {
    indexExpandedRow.value[index] = !indexExpandedRow.value[index]
}
const indexExpandedCol = ref(Array(paginatedData.value.length).fill(false))
const openOtherCol = (index) => {
    indexExpandedCol.value[index] = !indexExpandedCol.value[index]
}
</script>
<template>

    <Head title="Master Tabel" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid p-0">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Master Tabel
                </div>
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" /></button>
                <Link :href="route('tabel.create')" class="btn bg-info-fordone"><font-awesome-icon
                    icon="fa-solid fa-plus" />
                Tambah Master Tabel Baru</Link>
            </div>
        </div>
        <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
        <FlashMessage :toggleFlash="toggleFlashError" @close="toggleFlashError = false" :flash="page.props.flash.error"
            :types="'alert-danger'" />
        <table class="table table-hover table-bordered table-search" ref="tabelTabels" id="tabel-master">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column text-center th-order align-middle"
                        @click="clickSortProperties(tables, 'number')">No.
                    </th>
                    <th class="text-center align-middle th-order tabel-width-20" @click="clickToOrder('tabels.label')">
                        Nama Tabel
                    </th>
                    <th class="text-center align-middle th-order tabel-width-20" @click="clickToOrder('dinas.nama')">
                        Produsen Data
                    </th>
                    <th class="text-center align-middle tabel-width-15">
                        Daftar Kolom
                    </th>
                    <th class="text-center align-middle">
                        Daftar Baris
                    </th>
                    <th class="text-center align-middle tabel-width-10">
                        Daftar Tahun
                    </th>
                    <th class="text-center align-middle th-order" @click="clickToOrder('tabels.updated_at')">
                        Terakhir di-update
                    </th>
                    <th class="text-center align-middle tabel-width-5">
                        Tambah Tahun
                    </th>
                </tr>
                <tr class="">
                    <td class="search-header"></td>
                    <td class="search-header"><input v-model.trim="searchLabel" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchLabelDinas" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"
                            v-model.trim="searchColumnList"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"
                            v-model.trim="searchRowLabel"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"
                            v-model.trim="searchTahun"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"
                            v-model.trim="searchUpdated"></td>
                    <td class="search-header deleted"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-if="tables.length > 0" v-for="(table, index) in paginatedData" :key="index">
                    <td class="align-middle">{{ table.number }}</td>
                    <td class="align-middle">{{ table.label }}</td>
                    <td class="align-middle">{{ table.nama_dinas }}</td>
                    <td class="align-middle">
                        <template v-for="(col, colIndex) in table.columns" :key="colIndex">
                            <template v-if="!table.columns.length > 5">
                                <span class="badge mr-1 badge-info">{{ col.label }}</span>
                            </template>
                            <template v-else>
                                <span v-if="indexExpandedCol[index] || openColList(colIndex)"
                                    class="badge mr-1 badge-info">
                                    {{ col.label }}
                                </span>
                            </template>
                        </template>
                        <span v-if="table.columns.length > 5" class="badge mr-1 badge-info"
                            @click="openOtherCol(index)">
                            <font-awesome-icon v-if="!indexExpandedCol[index]" icon="fa-solid fa-angle-down" />
                            <font-awesome-icon v-if="indexExpandedCol[index]" icon="fa-solid fa-angle-up" />
                        </span>
                        <!-- <span v-for="(col, colIndex) in table.columns" :key="colIndex" class="badge mr-1 badge-info">
                            {{ col.label }}
                        </span> -->
                    </td>
                    <!-- <td class="align-middle">{{ table.row_label }}</td> -->
                    <td class="align-middle">
                        <template v-for="(row, rowIndex) in table.rowInputs" :key="rowIndex">
                            <template v-if="row.wilayah_fullcode">
                                <span v-if="indexExpandedRow[index] || openRowList(rowIndex)"
                                    class="badge mr-1 badge-info">
                                    {{ row.label }}
                                </span>
                            </template>
                            <template v-else-if="!table.rowInputs.length > 5">
                                <span class="badge mr-1 badge-info">{{ row.label }}</span>
                            </template>
                            <template v-else>
                                <span v-if="indexExpandedRow[index] || openRowList(rowIndex)"
                                    class="badge mr-1 badge-info">{{ row.label }}</span>
                            </template>
                        </template>
                        <span v-if="table.rowInputs[0].wilayah_fullcode || table.rowInputs.length > 5"
                            class="badge mr-1 badge-info" @click="openOtherRow(index)">
                            <font-awesome-icon v-if="!indexExpandedRow[index]" icon="fa-solid fa-angle-down" />
                            <font-awesome-icon v-if="indexExpandedRow[index]" icon="fa-solid fa-angle-up" />
                        </span>
                    </td>
                    <td class="align-middle">
                        <span v-for="(node, index) in table.tahuns" :key="index" class="badge mr-1 badge-info">
                            {{ node }}
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        <span class="badge badge-info">
                            {{ table.who_updated }}
                        </span>
                        <br>
                        <span>{{ table.status_updated }}</span>
                    </td>
                    <td class="text-center align-middle deleted">
                        <a @click.prevent="() => { addYearModalStatus = true; form.id = table.id }"
                            class="edit-pen mr-2">
                            <font-awesome-icon icon="fas fa-plus-circle" title="Tambah Tahun" />
                        </a>
                        <Link :href="route('tabel.edit', { id: table.id })" class="edit-pen mx-1">
                        <font-awesome-icon icon="fa-solid fa-pencil" title="Edit Tabel" />
                        </Link>
                        <a class="edit-pen mx-1" @click.prevent="patchTabel(table.id, table.id_statustables)">
                            <font-awesome-icon icon="fa-solid fa-dice-d6" title="Duplikat Tabel" />
                        </a>
                        <a v-if="false" @click.prevent="() => {
        deleteModalStatus = true;
        form.id = table.id_statustables
    }" class="delete-trash">
                            <font-awesome-icon icon="fa-solid fa-trash-can icon-trash-color" title="Hapus" />
                        </a>
                    </td>
                </tr>
                <tr v-else>
                    <td colspan="8" class="text-center align-middle">
                        Tidak ada data
                    </td>
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
                        @click.prevent="GoDownload('tabel-master', downloadTitle)">Simpan</button>
                </template>
            </ModalBs>
            <ModalBs :ModalStatus="addYearModalStatus" @close="() => { addYearModalStatus = false; form.reset() }"
                :title="'Tambah Tahun'">
                <template #modalBody>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="tahun">Pilih Tahun</label>
                            <Multiselect v-model="form.tahun" :options="yearDrop.options" :searchable="true"
                                placeholder="-- Pilih Tahun --" mode="tags" />
                        </div>
                    </div>
                </template>
                <template #modalFunction>
                    <button id="" type="button" class="btn btn-sm bg-success-fordone" :disabled="form.processing"
                        @click.prevent="submit">Simpan</button>
                </template>
            </ModalBs>
            <ModalBs :-modal-status="duplicateModalStatus" @close="closeDuplicateModal" :title="'Duplikat Tabel'"
                :modalSize="'modal-xl modal-dialog-scrollable'">
                <template #modalBody>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="tahun">Judul Tabel</label>
                            <input class="form-control" type="text" v-model="duplicateForm.judul" />
                        </div>
                        <div class="mb-3">
                            <label for="tahun">Produsen</label>
                            <Multiselect v-model="duplicateForm.produsen" :value="duplicateForm.produsen"
                                :options="ProdusenFetched" placeholder="-- Pilih Produsen Data --" :searchable="true" />
                        </div>
                        <template v-if="isWilayahFullcodes">
                            <div class="mb-3">
                                <label for="tingkat-label">Tingkatan Wilayah</label>
                                <Multiselect v-model="tingkatanDrop.value" :options="tingkatanDrop.options"
                                    :searchable="true" placeholder="-- Pilih Tingkatan --" />
                            </div>
                            <div class="mb-3">
                                <label for="tingkat-label">Kabupaten/Kota</label>
                                <Multiselect v-model="kabsDrop.value" :options="kabsDrop.options"
                                    @change="loadKecamatans" :searchable="true"
                                    placeholder="-- Pilih Kabupaten/Kota --" />
                            </div>
                            <div class="mb-3" v-if="tingkatanDrop.value > 2">
                                <label for="tingkat-label">Kecamatan</label>
                                <Multiselect v-model="kecsDrop.value" :options="kecsDrop.options" :searchable="true"
                                    @change="loadDesa" placeholder="-- Pilih Kecamatan --" />
                            </div>
                            <div class="mb-3">
                                <label for="judul">Daftar Baris</label>
                                <table class="table table-hover table-bordered" ref="tabelRowList" id="tabelRowList">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Kelompok Baris</th>
                                            <th>Label</th>
                                            <th>
                                                <div class="btn btn-sm bg-success-fordone mr-1"
                                                    @click="toggleAll(rowsCheckBox)">
                                                    <font-awesome-icon icon="fa fa-check" />
                                                </div>
                                                Pilih Semua
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="tingkatanDrop.value">
                                        <tr v-if="rowListFetched.length > 0" v-for="(node, index) in rowListFetched"
                                            :key="index" @click="toggleCheck(index, rowsCheckBox)">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ node.tipe }}</td>
                                            <td>{{ node.label }}</td>
                                            <td class="text-center">
                                                <input class="row-select" type="checkbox" v-model="rowsCheckBox[index]"
                                                    :value="node.id" ref="checkbox" />
                                            </td>
                                        </tr>
                                        <tr v-else>
                                            <td colspan="4" class="text-center"> Belum ada daftar baris yang tersedia
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </div>
                </template>
                <template #modalFunction>
                    <button id="" type="button" class="btn btn-sm bg-success-fordone"
                        :disabled="duplicateForm.processing" @click.prevent="duplicate">Simpan</button>
                </template>
            </ModalBs>
        </Teleport>
        <Teleport to="body">
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
        </Teleport>
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="totalItems" :current-page="currentPage" :current-show-items="paginatedData.length" />
    </GeneralLayout>
</template>
<style scoped>
table {
    font-size: smaller;
}

.th-order {
    cursor: pointer;
}
</style>