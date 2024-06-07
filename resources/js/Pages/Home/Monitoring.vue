<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import Multiselect from '@vueform/multiselect'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { defineComponent, watch, ref, computed } from 'vue'
import axios from 'axios'
import { GoDownload } from '@/download'
import ModalBs from '@/Components/ModalBs.vue';
import Pagination from '@/Components/Pagination.vue'

defineComponent({
    Multiselect
})
const page = usePage()
// var mObject = page.props.this_monitoring
const monitoring = ref(page.props.this_monitoring)
const searchLabel = ref(null)
const triggerSpinner = ref(false)
const downloadModalStatus = ref(false)
const downloadTitle = ref(null)

var all = [{ label: 'Pilih Semua', value: 'all' }]
const yearDrop = ref({
    value: 'all',
    options: [...all, ...page.props.years]
})

const tabelMonitoring = ref(null)

const ArrayBigObjects = [
    { key: 'nama_dinas', valueFilter: searchLabel },
]

const filteredColumns = computed(() => {
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
    if (filters.length === 0) {
        return monitoring.value
    }
    return monitoring.value.filter(item => {
        return filters.every(obj => {
            const filterValue = obj.valueFilter.value.toLowerCase()
            return item[obj.key].toLowerCase().includes(filterValue)
        })
    })
})
watch(ArrayBigObjects.filter(obj => obj.valueFilter.value), () => {
    monitoring.value = filteredColumns.value
})
const changeYearList = async function (value) {
    if (!value) {
        return
    }
    try {
        triggerSpinner.value = true
        const response = await axios.get(route('home.getMonitoring', { years: value }))
        monitoring.value = response.data
        triggerSpinner.value = false
    } catch (error) {
        console.error('Error fetching data:', error);
    }
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
</script>
<template>

    <Head title="Monitoring" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Monitoring Pengisian Tabel
                </div>
                <div class="mr-2 year">
                    <Multiselect @change="changeYearList" :options="yearDrop.options" v-model="yearDrop.value"
                        placeholder="-- Pilih Tahun --" />
                </div>
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" />
                    Download</button>
            </div>
        </div>
        <table class="table table-hover table-bordered" id="tabel-monitoring" ref="tabelMonitoring">
            <thead>
                <tr>
                    <th class="align-middle text-center tabel-width-5">#</th>
                    <th class="align-middle text-center tabel-width-35">Produsen Data</th>
                    <th class="align-middle text-center tabel-width-8">Status Tabel Baru</th>
                    <th class="align-middle text-center tabel-width-8">Status Proses Entri</th>
                    <th class="align-middle text-center tabel-width-8">Status Diperiksa</th>
                    <th class="align-middle text-center tabel-width-8">Status Perbaikan</th>
                    <th class="align-middle text-center tabel-width-8">Status Final</th>
                    <th class="align-middle text-center tabel-width-8">Tabel Dihapus</th>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text" class="form-control" v-model="searchLabel"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(node, index) in paginatedData" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>{{ node.nama_dinas }}</td>
                    <td>{{ node.jumlah_satu }}</td>
                    <td>{{ node.jumlah_dua }}</td>
                    <td>{{ node.jumlah_tiga }}</td>
                    <td>{{ node.jumlah_empat }}</td>
                    <td>{{ node.jumlah_lima }}</td>
                    <td>{{ node.jumlah_enam }}</td>
                </tr>
            </tbody>
        </table>
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="filteredColumns.length" :current-page="currentPage" />
        <Teleport to="body">
            <ModalBs :-modal-status="downloadModalStatus" @close="downloadModalStatus = false" :title="'Download Data'">
                <template #modalBody>
                    <label>Masukkan Judul File</label>
                    <input type="text" v-model="downloadTitle" class="form-control" />
                </template>
                <template #modalFunction>
                    <button type="button" class="btn btn-sm bg-success-fordone"
                        @click.prevent="GoDownload('tabel-monitoring', downloadTitle)">Simpan</button>
                </template>
            </ModalBs>
        </Teleport>
    </GeneralLayout>
</template>
<style scoped>
.year {
    width: 250px;
}
</style>