<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import Multiselect from '@vueform/multiselect'
import Checkbox from '@/Components/Checkbox.vue'
import { ref, defineComponent, onMounted, watch, onUnmounted } from 'vue'
import { Head, usePage, useForm, Link } from "@inertiajs/vue3"

defineComponent({
    Multiselect
})
const page = usePage()
const subjects = page.props.subjects
const dinas = page.props.dinas
const rowListFetched = ref(null)
const tabelRowList = ref(null)
const kecs = ref([])
const desa = ref([])
const kabupatens = page.props.kabupatens
var kecLists = []
var desaLists = []
const provinsi = {
    'label': 'PROVINSI SULAWESI UTARA',
    'wilayah_fullcode': '7100000000'
}
kabupatens.push(provinsi)
kabupatens.map((item, key, array) => {
    item.tipe = (key === array.length - 1) ? 'PROVINSI' : 'KABUPATEN/KOTA'
})

const rowGroups = page.props.row_groups
const rowGroupsDrop = ref({
    value: null,
    options: rowGroups.slice(1)
})
const kabs = page.props.kabupatens.map(kab => ({
    label: kab.label,
    value: kab.wilayah_fullcode
}))
// onMounted(() => {
// })
// onUnmounted(()=>{
//    kabupatens = page.props.kabupatens 
// })

//try first
const rowsCheckBox = ref([])
rowsCheckBox.value = Array(kabupatens.length).fill(false)

const rowsSelected = ref([])
const sended = function () {
    rowsSelected.value = kabupatens.filter((_, index) => {
        return rowsCheckBox.value[index]
    })
}

// const rowsSelected = kabupatens.filter(x => )
const kabsDrop = ref({
    value: null,
    options: kabs
})
const kecsDrop = ref({
    value: null,
    options: kecs,
})
const subjectDrop = ref({
    value: null,
    options: subjects,
})
const dinasDrop = ref({
    value: null,
    options: dinas,
})
const rowTypeDrop = ref({
    value: null,
    options: [
        { value: '1', label: 'wilayah' },
        { value: '2', label: 'non-wilayah' }
    ]
})


const tingkatanDrop = ref({
    value: null,
    options: [
        { label: "Provinsi", value: 0 },
        { label: "Kabupaten/Kota", value: 1 },
        { label: "Kecamatan", value: 2 },
        { label: "Desa/Kelurahan", value: 3 },
    ]
})

const toggleCheck = function (index, object) {
    object[index] = !object[index]
}
const toggleAll = function (object) {
    object.forEach((_, index) => {
        object[index] = !object[index]
    })
}
const triggerTingkatan = function (value) {
    if (value < 2) {
        assignRowListWilayah(value, null)
    }
}
const assignRowListWilayah = function (options, parents) {
    switch (options) {
        case 0:
            rowListFetched.value = [
                { tipe: 'PROVINSI', label: 'PROVINSI SULAWESI UTARA', wilayah_fullcode: '7100000000' }
            ]
            break;
        case 1:
            rowListFetched.value = kabupatens
            break;
        case 2:
            kecLists = kecs.value
            kecLists.push(parents)
            kecLists.map((item, key, array) => {
                item.tipe = (key === array.length - 1) ? 'KABUPATEN/KOTA' : 'KECAMATAN'
            })
            rowListFetched.value = kecLists
            break;
        case 3:
            desaLists = desa.value
            desaLists.push(parents)
            desaLists.map((item, key, array) => {
                item.tipe = (key === array.length - 1) ? 'KECAMATAN' : 'DESA/KELURAHAN'
            })
            rowListFetched.value = desaLists
            break;

        default:
            break;
    }
}
const fetchRow = async function (value) {
    try {
        const response = await axios.get('/row/fetchCreate/' + value)
        rowListFetched.value = response.data.data
    } catch (error) {
        console.error('Error Fetching data:', error)
    }
}

const loadKecamatans = async (valueKabs) => {
    try {
        let kabs = valueKabs.substring(2, 4)
        const response = await axios.get('/master/wilayah/kecamatan/' + kabs);
        kecs.value = response.data.data

    } catch (error) {
        console.error('Error fetching kecamatan:', error);
    }
}

const loadDesa = async (valueKecs) => {
    try {
        let kabs = valueKecs.substring(2, 4)
        let kecs = valueKecs.substring(4, 7)
        const response = await axios.get('/master/wilayah/desa/' + kabs + '/' + kecs);
        desa.value = response.data.data

    } catch (error) {
        console.error('Error fetching desa:', error);
    }
}

onMounted(() => {
    // console.log(tabelRowList.value.querySelectorAll('.row-select'))
})
</script>
<template>

    <GeneralLayout>
        <div class="container">
            <div class="card">
                <div class="card-body bg-info-fordone text-center">
                    <h2>Buat Tabel Baru</h2>
                </div>
            </div>
            <div class="form-group">
                <div class="card mb-3">
                    <div class="card-header">
                        <label class="h5 mb-0">Deskripsi Umum</label>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="dinas">Produsen Data</label>
                            <Multiselect v-model="dinasDrop.value" :options="dinasDrop.options"
                                placeholder="-- Pilih Produsen Data --" :searchable="true" />
                            <div class="text-danger text-left" v-if="true" id="error-dinas"></div>
                        </div>
                        <div class="mb-3">
                            <label for="nomor">Nomor Tabel</label>
                            <input type="text" id="nomor" class="form-control" placeholder="1.1.1">
                            <div class="text-danger text-left" v-if="true" id="error-nomor"></div>
                        </div>
                        <div class="mb-3">
                            <label for="judul">Judul Tabel</label>
                            <input type="text" id="judul" class="form-control" placeholder="Isikan judul tabel">
                            <div class="text-danger text-left" v-if="true" id="error-judul"></div>
                        </div>
                        <div class="mb-3">
                            <label for="subjek">Subjek Tabel</label>
                            <Multiselect v-model="subjectDrop.value" :options="subjectDrop.options"
                                placeholder="-- Pilih Subjek --" :searchable="true" />
                            <div class="text-danger text-left" v-if="true" id="error-subjek"></div>
                        </div>
                        <div class="mb-3">
                            <label for="unit">Satuan/Unit Data</label>
                            <input type="text" id="unit" class="form-control" placeholder="Isikan satuan/unit data">
                            <div class="text-danger text-left" v-if="true" id="error-unit"></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <label class="h5 mb-0">Detail Baris</label>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="rowType">Tipe Baris</label>
                            <Multiselect v-model="rowTypeDrop.value" :options="rowTypeDrop.options"
                                placeholder="-- Pilih Tipe Baris --" :searchable="true" />
                            <div class="text-danger text-left" v-if="true" id="error-rowType"></div>
                        </div>
                        <div class="mb-3" v-if="rowTypeDrop.value == '1'">
                            <label for="tingkat-label">Tingkatan Wilayah</label>
                            <Multiselect v-model="tingkatanDrop.value" :options="tingkatanDrop.options" @change="triggerTingkatan"
                                :searchable="true" placeholder="-- Pilih Tingkatan --" />
                        </div>
                        <div v-if="tingkatanDrop.value > 1" class="mb-3" id="kabupaten-group">
                            <label for="kab-label">Kabupaten</label>
                            <Multiselect v-model="kabsDrop.value" :options="kabsDrop.options"
                                placeholder="-- Pilih Kabupaten/Kota --" :searchable="true" @change="loadKecamatans" />
                        </div>
                        <div v-if="tingkatanDrop.value > 2" class="mb-3" id="kecamatan-group">
                            <label for="kec-label">Kecamatan</label>
                            <Multiselect v-model="kecsDrop.value" :options="kecsDrop.options"
                                placeholder="-- Pilih Kecamatan --" :searchable="true" @change="loadDesa" />
                        </div>
                        <div class="mb-3" v-if="true">
                            <label for="judul">Daftar Baris</label>
                            <table class="table table-hover table-bordered" ref="tabelRowList" id="tabelRowList">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tipe</th>
                                        <th>Label</th>
                                        <th>
                                            <div class="btn btn-sm bg-success-fordone mr-1"
                                                @click="toggleAll(rowsCheckBox)"><i class="fa fa-check"></i></div>
                                            Pilih Semua
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(node, index) in rowListFetched" :key="index"
                                        @click="toggleCheck(index, rowsCheckBox)">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ node.tipe }}</td>
                                        <td>{{ node.label }}</td>
                                        <td class="text-center"><input class="row-select" type="checkbox"
                                                v-model="rowsCheckBox[index]" :value="node.wilayah_fullcode"
                                                ref="checkbox" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-3" v-if="rowTypeDrop.value == '2'">
                            <label for="row-groups">Kelompok Baris</label>
                            <Multiselect v-model="rowGroupsDrop.value" :options="rowGroupsDrop.options"
                                @change="fetchRow" placeholder="-- Pilih Kelompok Baris --" />
                        </div>
                        <div class="mb-3" v-if="rowGroupsDrop.value">
                            <label for="judul">Daftar Baris</label>
                            <table class="table table-hover table-bordered" ref="tabelRowList" id="tabelRowList">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tipe</th>
                                        <th>Label</th>
                                        <th>
                                            <div class="btn btn-sm bg-success-fordone mr-1"
                                                @click="toggleAll(rowsCheckBox)"><i class="fa fa-check"></i></div>
                                            Pilih Semua
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(node, index) in rowListFetched" :key="index"
                                        @click="toggleCheck(index, rowsCheckBox)">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ node.tipe }}</td>
                                        <td>{{ node.label }}</td>
                                        <td class="text-center"><input class="row-select" type="checkbox"
                                                v-model="rowsCheckBox[index]" :value="node.id" ref="checkbox" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-3">
                            <label for="subjek">Subjek Tabel</label>
                            <Multiselect v-model="subjectDrop.value" :options="subjectDrop.options"
                                placeholder="-- Pilih Subjek --" :searchable="true" />
                            <div class="text-danger text-left" v-if="true" id="error-subjek"></div>
                        </div>
                        <div class="mb-3">
                            <label for="unit">Satuan/Unit Data</label>
                            <input type="text" id="unit" class="form-control" placeholder="Isikan satuan/unit data">
                            <div class="text-danger text-left" v-if="true" id="error-unit"></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <label class="h5 mb-0">Detail Kolom</label>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="dinas">Produsen Data</label>
                            <Multiselect v-model="dinasDrop.value" :options="dinasDrop.options"
                                placeholder="-- Pilih Produsen Data --" :searchable="true" />
                            <div class="text-danger text-left" v-if="true" id="error-dinas"></div>
                        </div>
                        <div class="mb-3">
                            <label for="nomor">Nomor Tabel</label>
                            <input type="text" id="nomor" class="form-control" placeholder="1.1.1">
                            <div class="text-danger text-left" v-if="true" id="error-nomor"></div>
                        </div>
                        <div class="mb-3">
                            <label for="judul">Judul Tabel</label>
                            <input type="text" id="judul" class="form-control" placeholder="Isikan judul tabel">
                            <div class="text-danger text-left" v-if="true" id="error-judul"></div>
                        </div>
                        <div class="mb-3">
                            <label for="subjek">Subjek Tabel</label>
                            <Multiselect v-model="subjectDrop.value" :options="subjectDrop.options"
                                placeholder="-- Pilih Subjek --" :searchable="true" />
                            <div class="text-danger text-left" v-if="true" id="error-subjek"></div>
                        </div>
                        <div class="mb-3">
                            <label for="unit">Satuan/Unit Data</label>
                            <input type="text" id="unit" class="form-control" placeholder="Isikan satuan/unit data">
                            <div class="text-danger text-left" v-if="true" id="error-unit"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div><br></div>
    </GeneralLayout>
</template>
<style scoped>
.card-header {
    border-bottom-color: #3d3b8e;
    border-bottom-width: 3px;
}
</style>