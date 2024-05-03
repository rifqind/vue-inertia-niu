<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import { Link, Head, usePage, useForm } from '@inertiajs/vue3'
import { clickSortProperties } from '@/sortAttribute'
import { searchCell } from '@/searchCell'
import { getPagination } from '@/pagination'
import { ref, watch, onMounted } from 'vue'
import { GoDownload } from '@/download'
import ModalBs from '@/Components/ModalBs.vue';

const page = usePage()
var mvGroup = page.props.tabels
var tabels = ref(mvGroup)

const searchLabel = ref(null)
const searchLabelDinas = ref(null)
const tabelMetavars = ref(null)
const statusText = ref(null)
const maxRows = ref(null)
const currentPagination = ref(null)
const downloadModalStatus = ref(false)
const downloadTitle = ref(null)

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel },
    { key: 'nama_dinas', valueFilter: searchLabelDinas },
]

watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
    if (filters.length === 0) {
        tabels.value = mvGroup
        return
    }
    tabels.value = mvGroup.filter(item => {
        return filters.every(obj => {
            const filterValue = obj.valueFilter.value.toLowerCase()
            return item[obj.key].toLowerCase().includes(filterValue)
        })
    })
})
onMounted(() => {
    searchCell(tabelMetavars.value, 10)
    let currentStatusText = statusText.value
    var rowsTabel = tabelMetavars.value.querySelectorAll('tbody tr').length
    getPagination(tabelMetavars, currentPagination, 10, statusText,
        currentStatusText, rowsTabel)
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelMetavars, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
</script>
<template>

    <Head title="Daftar Metadata Variabel" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Metadata
                </div>
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" />
                    Download</button>
            </div>
        </div>
        <table class="table table-sorted table-hover table-bordered table-search" ref="tabelMetavars"
            id="tabel-metavar">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column text-center align-middle"
                        @click="clickSortProperties(page.props.tabels, 'number')">No.
                    </th>
                    <th class="text-center align-middle tabel-width-20"
                        @click="clickSortProperties(page.props.tabels, 'label')">
                        Nama Tabel
                    </th>
                    <th class="text-center align-middle tabel-width-20"
                        @click="clickSortProperties(page.props.tabels, 'nama_dinas')">
                        Produsen Data
                    </th>
                    <th class="text-center align-middle" @click="clickSortProperties(page.props.tabels, 'status_desc')">
                        Status Metadata Variabel
                    </th>
                    <th class="text-center align-middle"
                        @click="clickSortProperties(page.props.tabels, 'when_updated')">
                        Terakhir di-update
                    </th>
                    <th class="text-center align-middle tabel-width-5">
                        Cek/Ubah Isian
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
                    <td class="search-header deleted"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-if="tabels.length > 0" v-for="(node, index) in tabels" :key="index">
                    <td class="align-middle">{{ node.number }}</td>
                    <td class="align-middle">{{ node.label }}</td>
                    <td class="align-middle">{{ node.nama_dinas }}</td>
                    <td v-if="node.status_metavar == 0" class="align-middle">Belum di Entri</td>
                    <td v-else class="align-middle">{{ node.status_desc }}</td>
                    <td class="text-center align-middle"><span class="badge badge-info">{{ node.who_updated
                            }}</span><br><span>{{ node.when_updated }}</span></td>
                    <td class="text-center align-middle deleted">
                        <Link :href="route('metavar.lists', { id: node.id })">
                        <font-awesome-icon icon="fa-solid fa-eye" title="Cek" />
                        </Link>
                    </td>
                </tr>
                <tr v-else>
                    <td class="align-middle" colspan="7">Data Tidak Ada</td>
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
                        @click.prevent="GoDownload('tabel-metavar', downloadTitle)">Simpan</button>
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