<script setup>
import { Link, Head, usePage, useForm } from '@inertiajs/vue3';
import { Teleport, onMounted, onUpdated, ref, watch } from 'vue';
import { getPagination } from '@/pagination';
import { clickSortProperties } from '@/sortAttribute';
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const page = usePage()
var sObject = page.props.subjects
var subjects = ref(sObject)
const createModalStatus = ref(false)
const deleteModalStatus = ref(false)
const toggleFlash = ref(false)
const searchLabel = ref(null)
const triggerSpinner = ref(false)
const subjectFetched = ref(null)

//pagination
const tabelSubjek = ref(null)
const statusText = ref(false)
const maxRows = ref(null)
const currentPagination = ref(null)

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel }
]
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
    if (filters.length === 0) {
        subjects.value = sObject
        return
    }
    subjects.value = sObject.filter(item => {
        return filters.every(obj => {
            const filterValue = obj.valueFilter.value.toLowerCase()
            return item[obj.key].toLowerCase().includes(filterValue)
        })
    })
})
onMounted(() => {
    let currentStatusText = statusText.value
    var rowsTabel = tabelSubjek.value.querySelectorAll('tbody tr').length
    getPagination(tabelSubjek, currentPagination, 10, statusText,
        currentStatusText, rowsTabel)
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelSubjek, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
onUpdated(() => {
    sObject = page.props.subjects
    subjects = ref(sObject)
    let currentStatusText = statusText.value
    var rowsTabel = tabelSubjek.value.querySelectorAll('tbody tr').length
    currentStatusText.querySelector('#showTotal').textContent = rowsTabel
    if (maxRows.value.value > rowsTabel) {
        currentStatusText.querySelector('#showPage').textContent = rowsTabel
    } else {
        currentStatusText.querySelector('#showPage').textContent = maxRows.value.value
    }
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelSubjek, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
const form = useForm({
    id: null,
    label: null
})
const toggleUpdateModal = function (id) {
    if (id) {
        fetchSubject(id).then(function () {
            triggerSpinner.value = false
            createModalStatus.value = true
        })
    }
}
const toggleDeleteModal = function (id) {
    deleteModalStatus.value = true
    form.id = id
}
const fetchSubject = async function (id) {
    try {
        triggerSpinner.value = true
        const response = await axios.get('/subject/fetch/' + id)
        subjectFetched.value = response.data.data
        form.id = subjectFetched.value.id
        form.label = subjectFetched.value.label
    } catch (error) {
        console.error('Error Fetching Subject: ', error)
    }
}
const submit = function () {
    form.post(route('subject.store'), {
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
    form.post(route('subject.destroy'), {
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

    <Head title="Daftar Subjek" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Subjek
                </div>
                <a href="#" class="btn bg-success-fordone mr-2" title="Download" data-target="#downloadModal"
                    data-toggle="modal"><i class="fa-solid fa-circle-down"></i></a>
                <a @click="createModalStatus = true" class="btn bg-info-fordone"><i class="fa-solid fa-plus"></i>
                    Tambah Subjek Baru</a>
            </div>
        </div>
        <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
        <table class="table table-sorted table-hover table-bordered table-search" ref="tabelSubjek" id="tabel-subjek">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column tabel-width-10" @click="clickSortProperties(subjects, 'number')">No.</th>
                    <th class="text-center tabel-width-70" @click="clickSortProperties(subjects, 'label')">Nama Subjek
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
                <tr v-for="subject in subjects" :key="subject.id">
                    <td>{{ subject.number }}</td>
                    <td>{{ subject.label }}</td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleUpdateModal(subject.id)" class="edit-pen mx-1">
                            <i class="fa-solid fa-pencil" title="Edit Pengguna"></i>
                        </a>
                    </td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleDeleteModal(subject.id)" class="delete-trash">
                            <i class="fa-solid fa-trash-can icon-trash-color"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <Teleport to="body">
            <ModalBs :ModalStatus="createModalStatus" @close="createModalStatus = false" :title="'Tambah Subjek Baru'">
                <template #modalBody>
                    <form>
                        <div class="form-group">
                            <label for="label">Nama Subjek</label>
                            <input v-model="form.label" type="text" class="form-control" id="label"
                                placeholder="Isi Nama Subjek">
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
            <ModalBs :ModalStatus="deleteModalStatus" @close="deleteModalStatus = false" :title="'Hapus Subjek'">
                <template v-slot:modalBody>
                    <label>Apakah Anda yakin akan menghapus Subjek ini?</label>
                </template>
                <template v-slot:modalFunction>
                    <button type="button" class="btn btn-sm badge-status-empat" :disabled="form.processing"
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