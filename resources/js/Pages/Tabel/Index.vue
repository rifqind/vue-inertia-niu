<script setup>
import { Link, Head, usePage, useForm } from '@inertiajs/vue3';
import { Teleport, onMounted, ref, watch } from 'vue';
import { clickSortProperties } from '@/sortAttribute';
import Pagination from '@/Components/Pagination.vue'
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import { GoDownload } from '@/download'
import { computed } from 'vue';

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
            } else if (obj.key == 'columns') {
                let check = item[obj.key].filter(X => {
                    let result = X.label.toLowerCase().includes(filterValue)
                    return result
                })
                if (check.length > 0) return true
            }
            else {
                return item[obj.key].toLowerCase().includes(filterValue)
            }
        })
    })
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
    return tables.value.slice(start, end)
})
watch(() => page.props.tables, (value) => {
    tables.value = [...value]
})
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
                    <th class="text-center align-middle">
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
                    <td class="align-middle"><span v-for="(col, index) in table.columns" :key="index"
                            class="badge mr-1 badge-info">{{
        col.label }}</span></td>
                    <td class="align-middle">{{ table.row_label }}</td>
                    <td class="align-middle">{{ table.tahun }}</td>
                    <td class="align-middle">{{ table.status }}</td>
                    <td class="text-center align-middle"><span class="badge badge-info">{{ table.who_updated
                            }}</span><br><span>{{ table.status_updated }}</span></td>
                    <td class="text-center align-middle deleted">
                        <Link :href="route('tabel.entri', { id: table.id_statustables })" class="edit-pen mr-5">
                        <font-awesome-icon icon="fa-solid fa-pencil" title="Cek/Edit" />
                        </Link>
                        <a v-if="currentRoute == 'tabel.index'" @click.prevent="() => {
        deleteModalStatus = true;
        form.id = table.id_statustables
    }" class="delete-trash">
                            <font-awesome-icon icon="fa-solid fa-trash-can" class="icon-trash-color" title="Hapus" />
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
        </Teleport>
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="tables.length" :current-page="currentPage" />
    </GeneralLayout>
</template>