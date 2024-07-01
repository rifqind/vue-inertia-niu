<script setup>
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { Teleport, defineComponent, ref, watch } from 'vue';
import { clickSortProperties } from '@/sortAttribute';
import Pagination from '@/Components/Pagination.vue'
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Multiselect from '@vueform/multiselect';
import { GoDownload } from '@/download'
import { computed } from 'vue';

defineComponent({
    Multiselect
})
const page = usePage()
var cGroup = page.props.columns.data
var columns = ref(cGroup)
const columnGroups = page.props.column_groups
const createModalStatus = ref(false)
const deleteModalStatus = ref(false)
const toggleFlash = ref(false)
const toggleFlashError = ref(false)
const searchLabel = ref(null)
const searchColumnGroup = ref(null)
const triggerSpinner = ref(false)
const columnsFetched = ref(null)
const modalTitle = ref('Tambah Kolom Baru')
const downloadModalStatus = ref(false)
const downloadTitle = ref(null)
const colGroupsDrop = ref({
    value: null,
    options: columnGroups
})

//pagination
const tabelColumns = ref(null)

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel },
    { key: 'columnGroupsLabel', valueFilter: searchColumnGroup },
]
// const filteredColumns = computed(() => {
//     let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
//     if (filters.length === 0) {
//         return page.props.columns
//     }
//     return page.props.columns.filter(item => {
//         return filters.every(obj => {
//             const filterValue = obj.valueFilter.value.toLowerCase()
//             return item[obj.key].toLowerCase().includes(filterValue)
//         })
//     })
// })
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    // columns.value = filteredColumns.value
    currentPage.value = 1
    delayedFetchData()
})
const delayedFetchData = debounce(() => {
    fetchData()
})

const form = useForm({
    id: null,
    label: null,
    id_column_groups: null,
    _token: null,
})
const isUpdate = ref(false)
const toggleUpdateModal = function (id) {
    if (id) {
        fetchColumns(id).then(function () {
            isUpdate.value = true
            modalTitle.value = 'Update Kolom'
            triggerSpinner.value = false
            createModalStatus.value = true
        })
    }
}
const toggleDeleteModal = function (id) {
    deleteModalStatus.value = true
    form.id = id
}
const fetchColumns = async function (id) {
    try {
        triggerSpinner.value = true
        const response = await axios.get('/column/fetch/' + id)
        columnsFetched.value = response.data.data
        form.id = columnsFetched.value.id
        form.label = columnsFetched.value.label
        form.id_column_groups = columnsFetched.value.id_column_groups
    } catch (error) {
        console.error('Error Fetching Column: ', error)
    }
}
const submit = async function () {
    const response = await axios.get(route('token'))
    // console.log(isUpdate.value)
    if (!isUpdate.value) form.label = listInput.value.value
    else form.label = [form.label]
    form._token = response.data
    if (form.processing) return
    form.post(route('columns.store'), {
        onBefore: function () {
            isUpdate.value = false
            triggerSpinner.value = true
            createModalStatus.value = false
        },
        onFinish: function () { triggerSpinner.value = false },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            form.reset()
            fetchData()
        },
        onError: function () { createModalStatus.value = true }
    })
    // columns.value = filteredColumns.value
}
const deleteForm = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
    form.post(route('columns.destroy'), {
        onBefore: function () {
            triggerSpinner.value = true
            deleteModalStatus.value = false
        },
        onFinish: function () { triggerSpinner.value = false },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            if (page.props.flash.error) toggleFlashError.value = true
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
    return columns.value
})
watch(() => page.props.columns.data, (value) => {
    columns.value = value
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
const listInput = ref({
    value: [],
    options: []
})
const currentInput = ref(null)
const addListInput = () => {
    if (currentInput.value) {
        listInput.value.options.push(currentInput.value)
        listInput.value.value.push(currentInput.value)
        currentInput.value = null
    }
}
const closeCreateModalStatus = () => {
    createModalStatus.value = false
    form.reset()
    modalTitle.value = 'Tambah Kolom Baru'
    listInput.value.value = []
    listInput.value.options = []
    isUpdate.value = false
}
const fetchData = async () => {
    try {
        const response = await axios.get(route('columns.index'), {
            params: {
                currentPage: currentPage.value, paginated: showItems.value,
                ArrayFilter: {
                    label: searchLabel.value,
                    columnGroupsLabel: searchColumnGroup.value
                },
                orderAttribute: orderAttribute.value
            }
        })
        columns.value = response.data.columns.data
        totalItems.value = response.data.countData
    } catch (error) {
        console.error('Error fetching data: ', error)
    }
}
</script>
<template>

    <Head title="Daftar Kolom" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Kolom
                </div>
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" /></button>
                <a @click="createModalStatus = true" class="btn bg-info-fordone"><font-awesome-icon
                        icon="fa-solid fa-plus" />
                    Tambah Kolom Baru</a>
            </div>
        </div>
        <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
        <FlashMessage :toggleFlash="toggleFlashError" @close="toggleFlashError = false" :flash="page.props.flash.error"
            :types="'alert-danger'" />
        <table class="table table-hover table-bordered table-search" ref="tabelColumns" id="tabel-kolom">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column th-order tabel-width-10">No.</th>
                    <th class="text-center th-order tabel-width-30" @click="clickToOrder('label')">Nama Kolom
                    </th>
                    <th class="text-center th-order tabel-width-30" @click="clickToOrder('cg.label')">
                        Nama Kelompok Kolom
                    </th>
                    <th class="text-center deleted tabel-width-8">Edit</th>
                    <th class="text-center deleted">Hapus</th>
                </tr>
                <tr class="">
                    <td class="search-header"></td>
                    <td class="search-header"><input v-model.trim="searchLabel" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchColumnGroup" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header deleted"></td>
                    <td class="search-header deleted"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="column in paginatedData" :key="column.id" v-if="columns.length > 0">
                    <td>{{ column.number }}</td>
                    <td>{{ column.label }}</td>
                    <td>{{ column.columnGroupsLabel }}</td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleUpdateModal(column.id)" class="edit-pen mx-1">
                            <font-awesome-icon icon="fa-solid fa-pencil" title="Edit Pengguna" />
                        </a>
                    </td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleDeleteModal(column.id)" class="delete-trash">
                            <font-awesome-icon icon="fa-solid fa-trash-can" class="icon-trash-color" />
                        </a>
                    </td>
                </tr>
                <tr v-else>
                    <td colspan="5" class="text-center">Data Tidak Ada</td>
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
            <ModalBs :ModalStatus="createModalStatus" @close="closeCreateModalStatus" :title="modalTitle">
                <template #modalBody>
                    <form>
                        <div class="form-group">
                            <template v-if="!isUpdate">
                                <div class="mb-3">
                                    <label for="label">Daftar Kolom</label>
                                    <Multiselect v-model="listInput.value" mode="tags" :options="listInput.options"
                                        :placeholder="'-- Daftar Kolom --'" />
                                    <div v-if="form.errors.label" class="text-danger">{{ form.errors.label }}</div>
                                </div>
                                <div class="mb-3">
                                    <label for="label">Nama Kolom</label>
                                    <div class="row">
                                        <input v-model="currentInput" type="text" class="ml-2 form-control col mr-1"
                                            id="label" placeholder="Isi Nama Kolom">
                                        <button type="button" class="btn btn-sm bg-success-fordone col-2 mr-2"
                                            @click.prevent="addListInput">Tambah</button>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="form-group">
                                <label for="label">Nama Kolom</label>
                                <input v-model="form.label" type="text" class="form-control" id="label"
                                    placeholder="Isi Nama Kolom">
                            </div>
                            <div class="mb-3">
                                <label for="id_column_groups">Nama Kelompok Kolom</label>
                                <Multiselect v-model="form.id_column_groups" :options="colGroupsDrop.options"
                                    placeholder="-- Pilih Kelompok Kolom --" :searchable="true" />
                                <div v-if="form.errors.id_column_groups" class="text-danger">
                                    {{ form.errors.id_column_groups }}
                                </div>
                            </div>
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
    }" :title="'Hapus Kolom'">
                <template v-slot:modalBody>
                    <label>Apakah Anda yakin akan menghapus Kolom ini?</label>
                </template>
                <template v-slot:modalFunction>
                    <button type="button" class="btn btn-sm badge-status-empat" :disabled="deleteForm.processing"
                        @click.prevent="deleteForm">Hapus</button>
                </template>
            </ModalBs>
        </Teleport>
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="totalItems" :current-page="currentPage"
            :current-show-items="paginatedData.length" />
    </GeneralLayout>
</template>
<style scoped>
.th-order{
    cursor: pointer;
}
</style>