<script setup>
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import PieChart from '@/Components/PieChart.vue';
import Multiselect from '@vueform/multiselect';
import SpinnerBorder from '@/Components/SpinnerBorder.vue';
import { Head, usePage } from '@inertiajs/vue3';
import InfiniteLoading from 'v3-infinite-loading'
// import {  } from '@inertiajs/vue3';
import { ref, onMounted, defineComponent, watch } from 'vue'
import axios from 'axios';

defineComponent({
    Multiselect
})
const page = usePage()
const displayedData = ref(page.props.notifikasiList.data)
var pieChartData = page.props.pieValues
var newTabels = page.props.newTabels
var finalTabels = page.props.finalTabels
var entriTabels = page.props.entriTabels
var verifyTabels = page.props.verifyTabels
var repairTabels = page.props.repairTabels

const pieCharts = ref(null)
const percentProgress = ref(null)
const updatePieCharts = ref(true)
const triggerSpinner = ref(false)

var all = [{ label: 'Pilih Semua', value: 'all' }]
const yearDrop = ref({
    value: 'all',
    options: [...all, ...page.props.years]
})
const kabsDrop = ref({
    value: 'all',
    options: [...all, ...page.props.wilayah]
})
// console.log(page.props.notifikasiList)
// watch(() => page.props.notifikasiList.data, (value) => {
//     displayedData.value = value.slice(0, 20)
// })
const updateResult = ref(false)
const pageNumber = ref(2)
const loadMoreData = async (state) => {
    try {
        const response = await axios.get(route('home.dashboard'), {
            params: {
                currentPage: pageNumber.value, paginated: 20,
            }
        })
        // console.log(response.data.notifikasiList)
        let nextData = response.data.notifikasiList.data
        if (nextData.length) {
            displayedData.value = displayedData.value.concat(nextData)
            state.loaded()
        } else {
            updateResult.value = (nextData.length == 0) ? true : false
            state.complete()
        }
        pageNumber.value = pageNumber.value + 1
    } catch (error) {
        console.error('Error Fetching Data :', error)
    }
    // let nextData = page.props.notifikasiList.data.slice(displayedData.value.length, displayedData.value.length + 20)
}
const infiniteData = +new Date()
const defineBadges = function (status) {
    const statusMapping = {
        1: "badge-status-satu",
        2: "badge-status-dua",
        3: "badge-status-tiga",
        4: "badge-status-empat",
        5: "badge-status-lima"
    }
    return statusMapping[status]
}
onMounted(() => {
    // displayedData.value = page.props.notifikasiList.data
    percentProgress.value.style.height = `${pieCharts.value.offsetHeight}px`
})
const getDashboard = async function () {
    triggerSpinner.value = true
    if (!yearDrop.value.value) yearDrop.value.value = 'all'
    if (!kabsDrop.value.value) kabsDrop.value.value = 'all'
    try {
        const response = await axios.get(`/getDashboard/${yearDrop.value.value}/${kabsDrop.value.value}`)
        newTabels = response.data.newTabels
        finalTabels = response.data.finalTabels
        entriTabels = response.data.entriTabels
        verifyTabels = response.data.verifyTabels
        repairTabels = response.data.repairTabels
        updatePieCharts.value = false
        setTimeout(() => {
            pieChartData = response.data.pieValues
            updatePieCharts.value = true
        }, 100);
    } catch (error) {
        console.error('Error Fetching Data :', error);
    }
    triggerSpinner.value = false
}
</script>

<template>

    <Head title="Dashboard" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="d-flex mb-2">
            <div class="year mr-1">
                <Multiselect :options="yearDrop.options" v-model="yearDrop.value" placeholder="-- Pilih Tahun --" />
            </div>
            <div class="mr-1 wilayah">
                <Multiselect :options="kabsDrop.options" v-model="kabsDrop.value" placeholder="-- Pilih Wilayah --" />
            </div>
            <div class="">
                <button @click.prevent="getDashboard" type="submit" class="btn bg-info-fordone"><font-awesome-icon
                        icon="fa-solid fa-magnifying-glass" /></button>
            </div>
        </div>
        <div class="row d-flex justify-content-start">
            <div class="card col-xl-6 col-l-6 col-md-12 col-sm-12" id="box-satu">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-bold mb-2 text-satu">JUMLAH TABEL BARU</div>
                            <div class="h5 text-bold">{{ newTabels }} Tabel</div>
                        </div>
                        <div class="col-auto align-middle"><font-awesome-icon icon="fa-solid fa-bars-staggered"
                                size="2x" class="text-satu" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card col-xl-6 col-l-6 col-md-12 col-sm-12" id="box-lima">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-bold mb-2 text-lima">RILIS</div>
                            <div class="h5 text-bold">{{ finalTabels }} Tabel</div>
                        </div>
                        <div class="col-auto align-middle"><font-awesome-icon size="2x" icon="fa-solid fa-check-double"
                                class="text-lima" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-start">
            <div class="card col-xl-4 col-l-4 col-md-12 col-sm-12" id="box-dua">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-bold mb-2 text-dua">DALAM PROSES ENTRI</div>
                            <div class="h5 text-bold">{{ entriTabels }} Tabel</div>
                        </div>
                        <div class="col-auto align-middle"><font-awesome-icon size="2x" icon="fa-solid fa-pen-nib"
                                class="text-dua" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card col-xl-4 col-l-4 col-md-12 col-sm-12" id="box-tiga">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-bold mb-2 text-tiga">DIPERIKSA</div>
                            <div class="h5 text-bold">{{ verifyTabels }} Tabel</div>
                        </div>
                        <div class="col-auto align-middle"><font-awesome-icon size="2x"
                                icon="fa-solid fa-hourglass-half" class="text-tiga" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card col-xl-4 col-l-4 col-md-12 col-sm-12" id="box-empat">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-bold mb-2 text-empat">PERLU PERBAIKAN</div>
                            <div class="h5 text-bold">{{ repairTabels }} Tabel</div>
                        </div>
                        <div class="col-auto align-middle"><font-awesome-icon size="2x"
                                icon="fa-solid fa-screwdriver-wrench" class="text-empat" /></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="p-0" id="p-progress">
                <div class="card" id="pie-charts" ref="pieCharts">
                    <div class="card-header text-bold text-center">
                        PROGRES PENGERJAAN DATA
                    </div>
                    <div class="card-body">
                        <div class="pie-chart-container mb-3 text-center">
                            <PieChart v-if="updatePieCharts" :chartValue="pieChartData" />
                            <!-- <canvas id="pie-chart"></canvas> -->
                        </div>
                        <div class="row p-2">
                            <div class="col p-0">
                                <div class="small mr-2">
                                    <font-awesome-icon icon="fa-solid fa-circle" class="text-satu" />
                                    Baru
                                </div>
                                <div class="small mr-2">
                                    <font-awesome-icon icon="fa-solid fa-circle" class="text-lima" />
                                    Rilis
                                </div>
                            </div>
                            <div class="col p-0">
                                <div class="small mr-2">
                                    <font-awesome-icon icon="fa-solid fa-circle" class="text-dua" />
                                    Entri
                                </div>
                                <div class="small mr-2">
                                    <font-awesome-icon icon="fa-solid fa-circle" class="text-tiga" />
                                    Diperiksa
                                </div>
                            </div>
                            <div class="col p-0">
                                <div class="small">
                                    <font-awesome-icon icon="fa-solid fa-circle" class="text-empat" />
                                    Perbaikan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-0 ml-1 col-9">
                <div class="card mr-3" id="percentage-progress" ref="percentProgress">
                    <div class="card-header text-center text-bold">
                        PROGRES PENGERJAAN
                    </div>
                    <div class="card-body" id="card-notifikasi">
                        <div v-for="(node, index) in displayedData" :key="index" class="row">
                            <div class="col-2 mb-3">
                                <div class="">
                                    <span class="badge" :class="defineBadges(node.status)">
                                        <div>{{ node.timestamp }}</div>
                                    </span>
                                </div>
                            </div>
                            <div class="col-10 mb-3">
                                {{ node.komentar }}
                                <span class="text-bold">{{ node.judul_tabel }}</span>, Tahun {{ node.tahundata }}
                            </div>
                        </div>
                        <div class="text-center" v-if="!updateResult">
                            <InfiniteLoading @infinite="loadMoreData" :identifier="infiniteData" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>

        </div>
    </GeneralLayout>
</template>
<style scoped>
.year {
    width: 15%;
}

.wilayah {
    width: 25%;
}

#box-satu {
    border-left: 4px solid #7286a0;
}

#box-dua {
    width: 32%;
    border-left: 4px solid #03254e;
}

#box-tiga {
    width: 32%;
    border-left: 4px solid #f18f01;
}

#box-empat {
    width: 32%;
    border-left: 4px solid #8b1e3f;
}

#box-lima {
    border-left: 4px solid green;
}

.text-satu {
    color: #7286a0;
}

.text-dua {
    color: #03254e;
}

.text-tiga {
    color: #f18f01;
}

.text-empat {
    color: #8b1e3f;
}

.text-lima {
    color: green;
}

#percentage-progress {
    width: 100%;
}

#card-notifikasi {
    overflow-y: scroll;
}

#p-progress {
    width: 24.5%;
}
</style>