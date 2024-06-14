<script setup>
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { Teleport, defineComponent, computed, ref, watch } from 'vue';
import { clickSortProperties } from '@/sortAttribute';
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Multiselect from '@vueform/multiselect';
import { GoDownload } from '@/download'
import Pagination from '@/Components/Pagination.vue';

defineComponent({
    Multiselect
})
const page = usePage()
var rGroup = page.props.rows
var rows = ref(rGroup)
const row_groups = page.props.row_groups
const createModalStatus = ref(false)
const deleteModalStatus = ref(false)
const toggleFlash = ref(false)
const toggleFlashError = ref(false)
const searchLabel = ref(null)
const searchRowGroup = ref(null)
const triggerSpinner = ref(false)
const rowsFetched = ref(null)
const modalTitle = ref('Tambah Baris Baru')
const downloadModalStatus = ref(false)
const downloadTitle = ref(null)
const rowGroupsDrop = ref({
    value: null,
    options: row_groups
})

//pagination
const tabelRows = ref(null)

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel },
    { key: 'rowGroupsLabel', valueFilter: searchRowGroup },
]
const filteredColumns = computed(() => {
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
    if (filters.length === 0) {
        return page.props.rows
    }
    return page.props.rows.filter(item => {
        return filters.every(obj => {
            const filterValue = obj.valueFilter.value.toLowerCase()
            return item[obj.key].toLowerCase().includes(filterValue)
        })
    })
})
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    rows.value = filteredColumns.value
})

const form = useForm({
    id: null,
    label: null,
    id_row_groups: null,
    _token: null,
})
const isUpdate = ref(false)
const toggleUpdateModal = function (id) {
    if (id) {
        fetchRows(id).then(function () {
            isUpdate.value = true
            modalTitle.value = 'Update Baris'
            triggerSpinner.value = false
            createModalStatus.value = true
        })
    }
}
const toggleDeleteModal = function (id) {
    deleteModalStatus.value = true
    form.id = id
}
const fetchRows = async function (id) {
    try {
        triggerSpinner.value = true
        const response = await axios.get('/row/fetch/' + id)
        rowsFetched.value = response.data.data
        form.id = rowsFetched.value.id
        form.label = rowsFetched.value.label
        form.id_row_groups = rowsFetched.value.id_row_groups
    } catch (error) {
        console.error('Error Fetching Row: ', error)
    }
}
const submit = async function () {
    const response = await axios.get(route('token'))
    if (!isUpdate.value) form.label = listInput.value.value
    else form.label = [form.label]
    form._token = response.data
    if (form.processing) return
    form.post(route('rows.store'), {
        onBefore: function () {
            isUpdate.value = false
            triggerSpinner.value = true
            createModalStatus.value = false
        },
        onFinish: function () { triggerSpinner.value = false },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            form.reset()
        },
        onError: function () { createModalStatus.value = true }
    })
}
const deleteForm = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
    form.post(route('rows.destroy'), {
        onBefore: function () {
            triggerSpinner.value = true
            deleteModalStatus.value = false
        },
        onFinish: function () { triggerSpinner.value = false },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            if (page.props.flash.error) toggleFlashError.value = true
            form.reset()
        },
        onError: function () { deleteModalStatus.value = true }
    })
}
//new Pagination
const showItemsValue = ref(10)
const showItems = computed(() => {
    if (filteredColumns.value.length < 10) return filteredColumns.value.length
    return showItemsValue.value
})
const currentPage = ref(1)

const updateShowItems = (value) => {
    if (value > filteredColumns.value.length) showItemsValue.value = filteredColumns.value.length
    else showItemsValue.value = value
    currentPage.value = 1
}
const updateCurrentPage = (value) => {
    currentPage.value = value
}
const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * showItems.value
    const end = start + showItems.value
    return filteredColumns.value.slice(start, end)
})
watch(() => page.props.rows, (value) => {
    rows.value = value
})
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
</script>
<template>

    <Head title="Daftar Baris" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Baris
                </div>
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" /></button>
                <a @click="createModalStatus = true" class="btn bg-info-fordone"><font-awesome-icon
                        icon="fa-solid fa-plus" />
                    Tambah Baris Baru</a>
            </div>
        </div>
        <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
        <FlashMessage :toggleFlash="toggleFlashError" @close="toggleFlashError = false" :flash="page.props.flash.error"
            :types="'alert-danger'" />
        <table class="table table-sorted table-hover table-bordered table-search" ref="tabelRows" id="tabel-Baris">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column tabel-width-10" @click="clickSortProperties(rows, 'number')">No.</th>
                    <th class="text-center tabel-width-30" @click="clickSortProperties(rows, 'label')">Nama Baris
                    </th>
                    <th class="text-center tabel-width-30" @click="clickSortProperties(rows, 'rowGroupsLabel')">
                        Nama Kelompok Baris
                    </th>
                    <th class="text-center deleted tabel-width-8">Edit</th>
                    <th class="text-center deleted">Hapus</th>
                </tr>
                <tr class="">
                    <td class="search-header"></td>
                    <td class="search-header"><input v-model.trim="searchLabel" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchRowGroup" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header deleted"></td>
                    <td class="search-header deleted"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in paginatedData" :key="row.id" v-if="rows.length > 0">
                    <td>{{ row.number }}</td>
                    <td>{{ row.label }}</td>
                    <td>{{ row.rowGroupsLabel }}</td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleUpdateModal(row.id)" class="edit-pen mx-1">
                            <font-awesome-icon icon="fa-solid fa-pencil" title="Edit Pengguna" />
                        </a>
                    </td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleDeleteModal(row.id)" class="delete-trash">
                            <font-awesome-icon icon="fa-solid fa-trash-can" class="icon-trash-color" />
                        </a>
                    </td>
                </tr>
                <tr v-else>
                    <td colspan="5" class="text-center">Tidak Ada Data</td>
                </tr>
            </tbody>
        </table>
        <Teleport to="body">
            <ModalBs :ModalStatus="createModalStatus" @close="closeCreateModalStatus" :title="modalTitle">
                <template #modalBody>
                    <form>
                        <div class="form-group">
                            <template v-if="!isUpdate">
                                <div class="mb-3">
                                    <label for="label">Daftar Baris</label>
                                    <Multiselect v-model="listInput.value" mode="tags" :options="listInput.options"
                                        :placeholder="'-- Daftar Baris --'" />
                                    <div v-if="form.errors.label" class="text-danger">{{ form.errors.label }}</div>
                                </div>
                                <div class="mb-3">
                                    <label for="label">Nama Baris</label>
                                    <div class="row">
                                        <input v-model="currentInput" type="text" class="ml-2 form-control col mr-1"
                                            id="label" placeholder="Isi Nama Baris">
                                        <button type="button" class="btn btn-sm bg-success-fordone col-2 mr-2"
                                            @click.prevent="addListInput">Tambah</button>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="form-group">
                                <label for="label">Nama Baris</label>
                                <input v-model="form.label" type="text" class="form-control" id="label"
                                    placeholder="Isi Nama Baris">
                            </div>
                            <div class="mb-3">
                                <label for="id_row_groups">Nama Kelompok Baris</label>
                                <Multiselect v-model="form.id_row_groups" :options="rowGroupsDrop.options"
                                    placeholder="-- Pilih Kelompok Baris --" :searchable="true" />
                                <div v-if="form.errors.id_row_groups" class="text-danger">
                                    {{ form.errors.id_row_groups }}
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
            <ModalBs :-modal-status="downloadModalStatus" @close="downloadModalStatus = false" :title="'Download Data'">
                <template #modalBody>
                    <label>Masukkan Judul File</label>
                    <input type="text" v-model="downloadTitle" class="form-control" />
                </template>
                <template #modalFunction>
                    <button type="button" class="btn btn-sm bg-success-fordone"
                        @click.prevent="GoDownload('tabel-Baris', downloadTitle)">Simpan</button>
                </template>
            </ModalBs>
            <ModalBs :ModalStatus="deleteModalStatus" @close="function () {
        deleteModalStatus = false
        form.reset()
    }" :title="'Hapus Baris'">
                <template v-slot:modalBody>
                    <label>Apakah Anda yakin akan menghapus Baris ini?</label>
                </template>
                <template v-slot:modalFunction>
                    <button type="button" class="btn btn-sm badge-status-empat" :disabled="deleteForm.processing"
                        @click.prevent="deleteForm">Hapus</button>
                </template>
            </ModalBs>
        </Teleport>
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="filteredColumns.length" :current-page="currentPage" :current-show-items="paginatedData.length"/>
    </GeneralLayout>
</template>