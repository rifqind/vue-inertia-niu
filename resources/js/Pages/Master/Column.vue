<script setup>
import { Link, Head, usePage, useForm } from '@inertiajs/vue3';
import { Teleport, defineComponent, onMounted, onUpdated, ref, watch } from 'vue';
import { getPagination } from '@/pagination';
import { clickSortProperties } from '@/sortAttribute';
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Multiselect from '@vueform/multiselect';

defineComponent({
    Multiselect
})
const page = usePage()
var cGroup = page.props.columns
var columns = ref(cGroup)
const columnGroups = page.props.column_groups
const createModalStatus = ref(false)
const deleteModalStatus = ref(false)
const toggleFlash = ref(false)
const searchLabel = ref(null)
const searchColumnGroup = ref(null)
const triggerSpinner = ref(false)
const columnsFetched = ref(null)
const colGroupsDrop = ref({
    value: null,
    options: columnGroups
})

//pagination
const tabelColumns = ref(null)
const statusText = ref(false)
const maxRows = ref(null)
const currentPagination = ref(null)

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel },
    { key: 'columnGroupsLabel', valueFilter: searchColumnGroup },
]
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
    if (filters.length === 0) {
        columns.value = cGroup
        return
    }
    columns.value = cGroup.filter(item => {
        return filters.every(obj => {
            const filterValue = obj.valueFilter.value.toLowerCase()
            return item[obj.key].toLowerCase().includes(filterValue)
        })
    })
})

onMounted(() => {
    let currentStatusText = statusText.value
    var rowsTabel = tabelColumns.value.querySelectorAll('tbody tr').length
    getPagination(tabelColumns, currentPagination, 10, statusText,
        currentStatusText, rowsTabel)
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelColumns, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
onUpdated(() => {
    cGroup = page.props.columns
    columns = ref(cGroup)
    let currentStatusText = statusText.value
    var rowsTabel = tabelColumns.value.querySelectorAll('tbody tr').length
    currentStatusText.querySelector('#showTotal').textContent = rowsTabel
    if (maxRows.value.value > rowsTabel) {
        currentStatusText.querySelector('#showPage').textContent = rowsTabel
    } else {
        currentStatusText.querySelector('#showPage').textContent = maxRows.value.value
    }
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelColumns, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
const form = useForm({
    id: null,
    label: null,
    id_column_groups: null,
})
const toggleUpdateModal = function (id) {
    if (id) {
        fetchColumns(id).then(function () {
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
const submit = function () {
    form.post(route('columns.store'), {
        onBefore: function () {
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
const deleteForm = function () {
    form.post(route('columns.destroy'), {
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
                <a href="#" class="btn bg-success-fordone mr-2" title="Download" data-target="#downloadModal"
                    data-toggle="modal"><i class="fa-solid fa-circle-down"></i></a>
                <a @click="createModalStatus = true" class="btn bg-info-fordone"><i class="fa-solid fa-plus"></i>
                    Tambah Kolom Baru</a>
            </div>
        </div>
        <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
        <table class="table table-sorted table-hover table-bordered table-search" ref="tabelColumns" id="tabel-kolom">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column tabel-width-10" @click="clickSortProperties(columns, 'number')">No.</th>
                    <th class="text-center tabel-width-30" @click="clickSortProperties(columns, 'label')">Nama Kolom
                    </th>
                    <th class="text-center tabel-width-30" @click="clickSortProperties(columns, 'columnGroupsLabel')">
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
                <tr v-for="column in columns" :key="column.id">
                    <td>{{ column.number }}</td>
                    <td>{{ column.label }}</td>
                    <td>{{ column.columnGroupsLabel }}</td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleUpdateModal(column.id)" class="edit-pen mx-1">
                            <i class="fa-solid fa-pencil" title="Edit Pengguna"></i>
                        </a>
                    </td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleDeleteModal(column.id)" class="delete-trash">
                            <i class="fa-solid fa-trash-can icon-trash-color"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <Teleport to="body">
            <ModalBs :ModalStatus="createModalStatus" @close="function () {
        createModalStatus = false
        form.reset()
    }" :title="'Tambah Kolom Baru'">
                <template #modalBody>
                    <form>
                        <div class="form-group">
                            <div class="mb-3">
                                <label for="label">Nama Kolom</label>
                                <input v-model="form.label" type="text" class="form-control" id="label"
                                    placeholder="Isi Nama Kolom">
                            </div>
                            <div class="mb-3">
                                <label for="id_column_groups">Nama Kelompok Kolom</label>
                                <Multiselect v-model="form.id_column_groups" :options="colGroupsDrop.options"
                                    placeholder="-- Pilih Kelompok Kolom --" :searchable="true" />
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
        <div class="d-flex justify-content-end align-items-center">
            <div id="statusText" ref="statusText" class="mb-3 mx-3 ml-auto">Menampilkan <span id="showPage"></span> dari
                <span id="showTotal"></span>
            </div>
            <div class="form-group"> <!--		Show Numbers Of Rows 		-->
                <select class="form-control" ref="maxRows" name="state" id="maxRows">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="pagination-container">
                <nav>
                    <ul class="pagination" id="currentPagination" ref="currentPagination">
                        <li data-page="prev" id="next">
                            <span>
                                < <span class="sr-only">(current)
                            </span></span>
                        </li>
                        <!--	Here the JS Function Will Add the Rows -->
                        <li data-page="next" id="prev">
                            <span> > <span class="sr-only">(current)</span></span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </GeneralLayout>
</template>