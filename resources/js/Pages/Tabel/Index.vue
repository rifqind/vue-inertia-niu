<script setup>
import { Link, Head, usePage, useForm } from '@inertiajs/vue3';
import { Teleport, defineComponent, onMounted, onUpdated, ref, watch } from 'vue';
import { getPagination } from '@/pagination';
import { clickSortProperties } from '@/sortAttribute';
import { searchCell } from '@/searchCell'
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Multiselect from '@vueform/multiselect';
import { formToJSON } from 'axios';
import axios from 'axios';

const page = usePage()
const tGroup = page.props.tables
const tables = ref(tGroup)

const triggerSpinner = ref(false)
const toggleFlash = ref(false)

const searchLabel = ref(null)
const searchLabelDinas = ref(null)
const searchColumnList = ref(null)
const searchTahun = ref(null)
const searchStatus = ref(null)
const searchUpdated = ref(null)

const tabelTabels = ref(null)
const statusText = ref(false)
const maxRows = ref(null)
const currentPagination = ref(null)

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel },
    { key: 'nama_dinas', valueFilter: searchLabelDinas },
    { key: 'columns', valueFilter: searchColumnList },
    { key: 'tahun', valueFilter: searchTahun },
    { key: 'status', valueFilter: searchStatus },
    { key: 'updated', valueFilter: searchUpdated },
]
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
    if (filters.length === 0) {
        tables.value = tGroup
        return
    }
    tables.value = tGroup.filter(item => {
        return filters.every(obj => {
            const filterValue = obj.valueFilter.value.toLowerCase()
            if (obj.key == 'updated') {
                return `${item['who_updated']} ${item['status_updated']}`.toLowerCase().includes(filterValue);
            } else {
                return item[obj.key].toLowerCase().includes(filterValue)
            }
        })
    })
})
onMounted(() => {
    if (page.props.flash.message) {
        toggleFlash.value = true
    }
    searchCell(tabelTabels.value, 10)
    let currentStatusText = statusText.value
    var rowsTabel = tabelTabels.value.querySelectorAll('tbody tr').length
    getPagination(tabelTabels, currentPagination, 5, statusText,
        currentStatusText, rowsTabel)
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelTabels, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
const entriTabel = function (id) {
    axios.get(route('tabel.entri', { id: id }))
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
                <a href="#" class="btn bg-success-fordone mr-2" title="Download" data-target="#downloadModal"
                    data-toggle="modal"><i class="fa-solid fa-circle-down"></i> Download</a>
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
                    <th class="text-center align-middle">
                        Daftar Kolom
                    </th>
                    <th class="text-center align-middle">
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
                    <td class="search-header"><input type="text" class="search-input form-control"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"></td>
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
                <tr v-for="(table, index) in tables" :key="index">
                    <td class="align-middle">{{ table.number }}</td>
                    <td class="align-middle">{{ table.label }}</td>
                    <td class="align-middle">{{ table.nama_dinas }}</td>
                    <td class="align-middle"><span v-for="(col, index) in table.columns" :key="index"
                            class="badge mr-1 badge-info">{{
        col.label }}</span></td>
                    <td class="align-middle">{{ table.row_label }}</td>
                    <td class="align-middle">{{ table.tahun }}</td>
                    <td class="align-middle">{{ table.status }}</td>
                    <td class="text-center align-middle">({{ table.who_updated }})<br>{{ table.status_updated }}</td>
                    <td class="text-center align-middle deleted">
                        <Link :href="route('tabel.entri', { id: table.id_statustables })" class="edit-pen mr-5">
                        <i class="fa-solid fa-pencil" title="Cek/Edit"></i>
                        </Link>
                        <a @click.prevent="toggleDeleteModal(table.id)" class="delete-trash">
                            <i class="fa-solid fa-trash-can icon-trash-color" title="Hapus"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-end align-items-center">
            <div id="statusText" ref="statusText" class="mb-3 mx-3 ml-auto">Menampilkan <span id="showPage"></span> dari
                <span id="showTotal"></span>
            </div>
            <div class="form-group"> <!--		Show Numbers Of Rows 		-->
                <select class="form-control" ref="maxRows" name="state" id="maxRows">
                    <option value="5">5</option>
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