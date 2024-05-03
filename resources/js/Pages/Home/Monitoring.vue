<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import Multiselect from '@vueform/multiselect'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { defineComponent, watch, ref, onMounted, onUpdated } from 'vue'
import { getPagination } from '@/pagination'
import axios from 'axios'
import { GoDownload } from '@/download'
import ModalBs from '@/Components/ModalBs.vue';

defineComponent({
    Multiselect
})
const page = usePage()
var mObject = page.props.this_monitoring
const monitoring = ref(mObject)
const searchLabel = ref(null)
const triggerSpinner = ref(false)
const downloadModalStatus = ref(false)
const downloadTitle = ref(null)

var valueChanged = 10
var currentStatusText = null
var rowsTabel = null

var all = [{ label: 'Pilih Semua', value: 'all' }]
const yearDrop = ref({
    value: 'all',
    options: [...all, ...page.props.years]
})

const tabelMonitoring = ref(null)
const statusText = ref(false)
const maxRows = ref(null)
const currentPagination = ref(null)

watch(searchLabel, () => {
    monitoring.value = mObject.filter(x => x.nama_dinas.toLowerCase().includes(searchLabel.value.toLowerCase()))
})
onMounted(() => {
    currentStatusText = statusText.value
    rowsTabel = tabelMonitoring.value.querySelectorAll('tbody tr').length
    getPagination(tabelMonitoring, currentPagination, 10, statusText,
        currentStatusText, rowsTabel)
    maxRows.value.addEventListener("change", function (e) {
        valueChanged = this.value
        valueChanged = getPagination(tabelMonitoring, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
const changeYearList = async function (value) {
    if (!value) {
        return
    }
    try {
        triggerSpinner.value = true
        const response = await axios.get(route('home.getMonitoring', { years: value }))
        mObject = response.data
        monitoring.value = mObject
        triggerSpinner.value = false
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}
onUpdated(() => {
    currentStatusText = statusText.value
    rowsTabel = tabelMonitoring.value.querySelectorAll('tbody tr').length
    getPagination(tabelMonitoring, currentPagination, valueChanged, statusText,
        currentStatusText, rowsTabel)
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
                <button class="btn bg-success-fordone mr-2" title="Download" @click="downloadModalStatus = true"><font-awesome-icon
                        icon="fa-solid fa-circle-down" /> Download</button>
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
                <tr v-for="(node, index) in monitoring" :key="index">
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