<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import { Link, Head, usePage, } from '@inertiajs/vue3'
import { clickSortProperties } from '@/sortAttribute'
import { ref, watch, computed } from 'vue'
import { GoDownload } from '@/download'
import ModalBs from '@/Components/ModalBs.vue';
import Pagination from '@/Components/Pagination.vue'

const page = usePage()
var mvGroup = page.props.tabels
var tabels = ref(mvGroup)

const searchLabel = ref(null)
const searchLabelDinas = ref(null)
const tabelMetavars = ref(null)
const downloadModalStatus = ref(false)
const downloadTitle = ref(null)

const ArrayBigObjects = [
    { key: 'label', valueFilter: searchLabel },
    { key: 'nama_dinas', valueFilter: searchLabelDinas },
]
const filteredColumns = computed(() => {
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
    if (filters.length === 0) {
        return page.props.tabels
    }
    return page.props.tabels.filter(item => {
        return filters.every(obj => {
            const filterValue = obj.valueFilter.value.toLowerCase()
            return item[obj.key].toLowerCase().includes(filterValue)
        })
    })
})
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    tabels.value = filteredColumns.value
})
//new Pagination
const showItems = ref(10)
const currentPage = ref(1)

const updateShowItems = (value) => {
    showItems.value = value
}
const updateCurrentPage = (value) => {
    currentPage.value = value
}
const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * showItems.value
    const end = start + showItems.value
    return filteredColumns.value.slice(start, end)
})
watch(() => page.props.tabels, (value) => {
    tabels.value = value
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
                <tr v-if="tabels.length > 0" v-for="(node, index) in paginatedData" :key="index">
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
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="tabels.length" :current-page="currentPage" />
    </GeneralLayout>
</template>