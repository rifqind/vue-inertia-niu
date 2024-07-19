<script setup>
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { Teleport, ref, watch } from 'vue';
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import { GoDownload } from '@/download'
import Pagination from '@/Components/Pagination.vue';
import { computed } from 'vue';
import * as XLSX from 'xlsx'

const page = usePage()
var cGObject = page.props.columnGroup.data
var columnGroup = ref(cGObject)
const createModalStatus = ref(false)
const deleteModalStatus = ref(false)
const toggleFlash = ref(false)
const searchLabel = ref(null)
const triggerSpinner = ref(false)
const columnGroupFetched = ref(null)
const modalTitle = ref('Tambah Kelompok Kolom Baru')
const downloadModalStatus = ref(false)
const downloadTitle = ref(null)
const uploadModal = ref(false)
const flashObject = ref(page.props.flash)
watch(() => page.props.flash, (value) => {
    flashObject.value = value
})
const flashHandle = () => {
    toggleFlash.value = false
    flashObject.value = {
        message: null,
        error: null,
    }
}

//pagination
const tabelColumnGroup = ref(null)

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel }
]
// const filteredColumns = computed(() => {
//     let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
//     if (filters.length === 0) {
//         return page.props.columnGroup
//     }
//     return page.props.columnGroup.filter(item => {
//         return filters.every(obj => {
//             const filterValue = obj.valueFilter.value.toLowerCase()
//             return item[obj.key].toLowerCase().includes(filterValue)
//         })
//     })
// })
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    // columnGroup.value = filteredColumns.value
    currentPage.value = 1
    delayedFetchData()
})
const delayedFetchData = debounce(() => {
    fetchData()
})
const form = useForm({
    id: null,
    label: null,
    fileUpload: null,
    _token: null,
})
const toggleUpdateModal = function (id) {
    if (id) {
        fetchColumnGroup(id).then(function () {
            modalTitle.value = 'Update Kelompok Kolom'
            triggerSpinner.value = false
            createModalStatus.value = true
        })
    }
}
const toggleDeleteModal = function (id) {
    deleteModalStatus.value = true
    form.id = id
}
const fetchColumnGroup = async function (id) {
    try {
        triggerSpinner.value = true
        const response = await axios.get('/column-group/fetch/' + id)
        columnGroupFetched.value = response.data.data
        form.id = columnGroupFetched.value.id
        form.label = columnGroupFetched.value.label
    } catch (error) {
        console.error('Error Fetching Subject: ', error)
    }
}
const submit = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
    form.post(route('column_group.store'), {
        onBefore: function () {
            triggerSpinner.value = true
            createModalStatus.value = false
            uploadModal.value = false
        },
        onFinish: function () { triggerSpinner.value = false },
        onSuccess: function () {
            if (flashObject) toggleFlash.value = true
            form.reset()
            fetchData()
        },
        onError: function () { createModalStatus.value = true }
    })
}
const deleteForm = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
    form.post(route('column_group.destroy'), {
        onBefore: function () {
            triggerSpinner.value = true
            deleteModalStatus.value = false
        },
        onFinish: function () { triggerSpinner.value = false },
        onSuccess: function () {
            if (flashObject) toggleFlash.value = true
            form.reset()
            fetchData()
        },
        onError: function () { deleteModalStatus.value = true }
    })
}
//new Pagination
// const showItemsValue = ref(10)
// const showItems = computed(() => {
//     const filteredLength = filteredColumns.value.length
//     let thisLength = null
//     if (filteredLength < 10) thisLength = filteredLength
//     else thisLength = showItemsValue.value
//     return thisLength
// })
const currentPage = ref(1)
const showItems = ref(10)

const updateShowItems = (value) => {
    // if (value > filteredColumns.value.length) showItemsValue.value = filteredColumns.value.length
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
    // const start = (currentPage.value - 1) * showItems.value
    // const end = start + showItems.value
    // return filteredColumns.value.slice(start, end)
    return columnGroup.value
})
watch(() => page.props.columnGroup.data, (value) => {
    columnGroup.value = value
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
        const response = await axios.get(route('column_group.index'), {
            params: {
                currentPage: currentPage.value, paginated: showItems.value,
                ArrayFilter: {
                    label: searchLabel.value
                },
                orderAttribute: orderAttribute.value
            }
        })
        columnGroup.value = response.data.columnGroup.data
        totalItems.value = response.data.countData
    } catch (error) {
        console.error('Error fetching data: ', error)
    }
}
const handleUpload = (e) => {
    let fileS = e.target.files ? e.target.files[0] : null
    if (fileS) {
        const reader = new FileReader()
        reader.onload = (e) => {
            /* Parse data */
            const bstr = e.target.result;
            const wb = XLSX.read(bstr, { type: 'binary' });
            /* Get first worksheet */
            const wsname = wb.SheetNames[0];
            const ws = wb.Sheets[wsname];
            /* Convert array of arrays */
            const data = XLSX.utils.sheet_to_json(ws, { header: 1 });
            // console.log(data)
            form.fileUpload = data
        }
        reader.readAsBinaryString(fileS)
        // console.log(result)
    }
}
const downloadTemplate = () => {
    window.location.href = '/download-template/column-group-template'
}
</script>
<template>

    <Head title="Daftar Kelompok Kolom" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Kelompok Kolom
                </div>
                <button class="btn bg-info mr-1" @click="uploadModal = !uploadModal"><font-awesome-icon
                        icon="fa-solid fa-file" /></button>
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" /></button>
                <a @click="createModalStatus = true" class="btn bg-info-fordone"><font-awesome-icon
                        icon="fa-solid fa-plus" />
                    Tambah Kelompok Kolom Baru</a>
            </div>
        </div>
        <FlashMessage :toggleFlash="toggleFlash" @close="flashHandle" :flashObject="flashObject" />
        <table class="table table-hover table-bordered table-search" ref="tabelColumnGroup" id="tabel-kelompok-kolom">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column tabel-width-10">No.</th>
                    <th class="text-center th-order tabel-width-70" @click="clickToOrder('label')">Nama
                        Kelompok Kolom
                    </th>
                    <th class="text-center deleted tabel-width-8">Edit</th>
                    <th class="text-center deleted">Hapus</th>
                </tr>
                <tr class="">
                    <td class="search-header"></td>
                    <td class="search-header"><input v-model.trim="searchLabel" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header deleted"></td>
                    <td class="search-header deleted"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="columnG in paginatedData" :key="columnG.id" v-if="columnGroup.length > 0">
                    <td>{{ columnG.number }}</td>
                    <td>{{ columnG.label }}</td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleUpdateModal(columnG.id)" class="edit-pen mx-1">
                            <font-awesome-icon icon="fa-solid fa-pencil" title="Edit Pengguna" />
                        </a>
                    </td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleDeleteModal(columnG.id)" class="delete-trash">
                            <font-awesome-icon icon="fa-solid fa-trash-can" class="icon-trash-color" />
                        </a>
                    </td>
                </tr>
                <tr v-else>
                    <td class="text-center" colspan="4">
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
                        @click.prevent="GoDownload('tabel-kelompok-kolom', downloadTitle)">Simpan</button>
                </template>
            </ModalBs>
            <ModalBs :ModalStatus="createModalStatus"
                @close="() => { createModalStatus = false; modalTitle = 'Tambah Kelompok Kolom Baru'; form.reset() }"
                :title="modalTitle">
                <template #modalBody>
                    <form>
                        <div class="form-group">
                            <label for="label">Nama Kelompok Kolom</label>
                            <input v-model="form.label" type="text" class="form-control" id="label"
                                placeholder="Isi Nama Kelompok Kolom">
                        </div>
                    </form>
                </template>
                <template #modalFunction>
                    <button id="" type="button" class="btn btn-sm bg-success-fordone" :disabled="form.processing"
                        @click.prevent="submit">Simpan</button>
                </template>
            </ModalBs>
        </Teleport>
        <Teleport to="body">
            <ModalBs :ModalStatus="deleteModalStatus" @close="function () {
        deleteModalStatus = false
        form.reset()
    }" :title="'Hapus Kelompok Kolom'">
                <template v-slot:modalBody>
                    <label>Apakah Anda yakin akan menghapus Kelompok Kolom ini? (Kolom yang di dalamnya maka akan ikut
                        terhapus)</label>
                </template>
                <template v-slot:modalFunction>
                    <button type="button" class="btn btn-sm badge-status-empat" :disabled="form.processing"
                        @click.prevent="deleteForm">Hapus</button>
                </template>
            </ModalBs>
            <ModalBs :ModalStatus="uploadModal" @close="uploadModal = false" :title="'Tambah dengan Template'">
                <template #modalBody>
                    <div class="mb-3 row">
                        <div class="col-6">
                            <label>Download Template</label>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-sm bg-success-fordone"
                                @click="downloadTemplate">Download</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="file" @change="handleUpload" class="form-control">
                    </div>
                </template>
                <template #modalFunction>
                    <button id="" type="button" class="btn btn-sm bg-success-fordone"
                        @click.prevent="submit">Simpan</button>
                </template>
            </ModalBs>
        </Teleport>
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="totalItems" :current-page="currentPage" :current-show-items="paginatedData.length" />
    </GeneralLayout>
</template>
<style scoped>
.th-order {
    cursor: pointer;
}
</style>