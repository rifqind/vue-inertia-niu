<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import ModalBs from '@/Components/ModalBs.vue';
import Multiselect from '@vueform/multiselect';
import SpinnerBorder from '@/Components/SpinnerBorder.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import { Link, useForm, usePage, Head } from '@inertiajs/vue3'
import { getPagination } from '@/pagination';
import { ref, onMounted, onUpdated, defineComponent, watch } from 'vue'
import { clickSortProperties } from '@/sortAttribute';

const page = usePage()
const tabelDinas = ref(null)
const currentPagination = ref(null)
const maxRows = ref(null)
const statusText = ref(null)
const updateModalStatus = ref(false)
const deleteModalStatus = ref(false)
const DOMLoaded = ref(false)
const toggleFlash = ref(false)
const searchNama = ref(null)
const searchWilayah = ref(null)
const triggerSpinner = ref(false)
const produsenFetched = ref({
    data: [],
    kabs: [],
})
const kecs = ref([])
const desa = ref([])
const tingkatan = ref({
    value: null,
    options: [
        { label: "Provinsi", value: 0 },
        { label: "Kabupaten/Kota", value: 1 },
        { label: "Kecamatan", value: 2 },
        { label: "Desa/Kelurahan", value: 3 },
    ]
})
var dObject = page.props.dinas
var d = ref(dObject)
const kabsDrop = ref({
    value: null,
    options: null,
})
const form = useForm({
    id: null,
    nama: null,
    tingkat: null,
    kab: null,
    kec: null,
    desa: null,
})
const kecsDrop = ref({
    value: null,
    options: kecs,
})
const desaDrop = ref({
    value: null,
    options: desa,
})

const toggleUpdateModal = function (id) {
    if (id) {
        fetchProdusen(id).then(function () {
            triggerSpinner.value = false
            updateModalStatus.value = true
        })
    }
}
const toggleDeleteModal = function (id) {
    deleteModalStatus.value = true
    form.id = id
}

const fetchProdusen = async function (id) {
    try {
        triggerSpinner.value = true

        const response = await axios.get('/dinas/fetch/' + id)
        produsenFetched.value = response.data
        form.nama = produsenFetched.value.data.nama
        form.tingkat = produsenFetched.value.data.tingkat
        form.id = produsenFetched.value.data.id
        let kabupaten = produsenFetched.value.data.wilayah_fullcode.substring(2, 4)
        let kecamatan = produsenFetched.value.data.wilayah_fullcode.substring(4, 7)
        if (form.tingkat > 0) {
            if (form.tingkat > 1) {
                if (form.tingkat > 2) {
                    form.kab = '71' + kabupaten + '000000'
                    form.kec = '71' + kabupaten + kecamatan + '000'
                    form.desa = produsenFetched.value.data.wilayah_fullcode
                    loadKecamatans(form.desa)
                    loadDesa(form.desa)
                } else {
                    form.kab = '71' + kabupaten + '000000'
                    form.kec = produsenFetched.value.data.wilayah_fullcode
                    loadKecamatans(form.kec)
                }
            } else {
                form.kab = produsenFetched.value.data.wilayah_fullcode
            }
        }
        kabsDrop.value.options = produsenFetched.value.kabs.slice(1)
    } catch (error) {
        console.error('Error fetching produsen: ', error)
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
};

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

watch([searchNama, searchWilayah], function () {
    if (searchNama.value && !searchWilayah.value) {
        d.value = dObject.filter(x =>
            x.nama.toLowerCase().includes(searchNama.value.toLowerCase()))
    } else if (searchWilayah.value && !searchNama.value) {
        d.value = dObject.filter(x =>
            x.wilayah_label.toLowerCase().includes(searchWilayah.value.toLowerCase())
        )
    } else if (searchNama.value && searchWilayah.value) {
        d.value = dObject.filter(x =>
            x.nama.toLowerCase().includes(searchNama.value.toLowerCase()) &&
            x.wilayah_label.toLowerCase().includes(searchWilayah.value.toLowerCase())
        )
    } else {
        d.value = dObject
    }
})

onMounted(() => {
    let currentStatusText = statusText.value
    var rowsTabel = tabelDinas.value.querySelectorAll('tbody tr').length
    getPagination(tabelDinas, currentPagination, 10, statusText,
        currentStatusText, rowsTabel)
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelDinas, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
    DOMLoaded.value = true
})

onUpdated(() => {
    let currentStatusText = statusText.value
    var rowsTabel = tabelDinas.value.querySelectorAll('tbody tr').length
    currentStatusText.querySelector('#showTotal').textContent = rowsTabel
    dObject = page.props.dinas
    d = ref(dObject)
})
defineComponent({
    Multiselect
})
const submit = function () {
    form.post(route('dinas.update'), {
        onBefore: function () {
            triggerSpinner.value = true
            updateModalStatus.value = false
        },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            form.reset()
        },
        onFinish: function () {
            triggerSpinner.value = false
        },
        onError: function () { updateModalStatus.value = true }
    })
}
const deleteForm = function () {
    form.post(route('dinas.delete'), {
        onBefore: function () {
            triggerSpinner.value = true
            deleteModalStatus.value = false
        },
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            form.reset()
        },
        onFinish: function () {
            triggerSpinner.value = false
        },
        onError: function () { deleteModalStatus.value = true }
    })
}
</script>

<template>

    <Head title="Daftar Produsen Data" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Produsen Data
                </div>
                <a href="#" class="btn bg-success-fordone mr-2" title="Download" data-target="#downloadModal"
                    data-toggle="modal"><i class="fa-solid fa-circle-down"></i></a>
                <Link :href="route('dinas.create')" class="btn bg-info-fordone"><i class="fa-solid fa-plus"></i>
                Tambah Produsen Data Baru</Link>
            </div>
            <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
            <table class="table table-sorted table-hover table-bordered" ref="tabelDinas" id="tabel-dinas">
                <thead>
                    <tr>
                        <th class="first-column" @click="clickSortProperties(d, 'number')">No.</th>
                        <th class="text-center tabel-width-50" @click="clickSortProperties(d, 'nama')">Nama Produsen
                            Data
                        </th>
                        <th class="text-center" @click="clickSortProperties(d, 'wilayah_label')">Wilayah Kerja</th>
                        <th class="text-center deleted">Edit</th>
                        <th class="text-center deleted">Hapus</th>
                    </tr>
                    <tr class="">
                        <td class="search-header"></td>
                        <td class="search-header"><input v-model.trim="searchNama" type="text"
                                class="search-input form-control"></td>
                        <td class="search-header"><input v-model.trim="searchWilayah" type="text"
                                class="search-input form-control"></td>
                        <td class="search-header"></td>
                        <td class="search-header"></td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="din in d" :key="din.id">
                        <td>{{ din.number }}</td>
                        <td>{{ din.nama }}</td>
                        <td class="">{{ din.wilayah_label }}</td>
                        <td class="text-center deleted">
                            <a @click.prevent="toggleUpdateModal(din.id)" class="update-pen">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                        </td>
                        <td class="text-center deleted">
                            <a @click.prevent="toggleDeleteModal(din.id)" class="delete-trash">
                                <i class="fa-solid fa-trash-can icon-trash-color"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Teleport to="body">
            <ModalBs v-if="DOMLoaded" :ModalStatus="updateModalStatus" @close="updateModalStatus = false"
                :title="'Update Produsen Data'">
                <template v-slot:modalBody>
                    <form id="DinasForm" class="form-horizontal mb-3">
                        <div class="form-group">
                            <label for="nama">Nama Produsen Data</label>
                            <input class="form-control" name="nama" id="nama" v-model="form.nama"
                                placeholder="Isi Nama Produsen Data">
                            <div id="error-nama" v-if="form.errors.nama" class="text-danger">{{ form.errors.nama }}
                            </div>
                            <div name="wilayah_fullcode" class="d-none">x</div>
                            <div class="mb-3 mt-3">
                                <label for="tingkat-label">Tingkatan Wilayah</label>
                                <Multiselect v-model="form.tingkat" :options="tingkatan.options"
                                    placeholder="-- Pilih Tingkatan --" />
                            </div>
                            <div v-if="form.tingkat > 0" class="mb-3" id="kabupaten-group">
                                <label for="kab-label">Kabupaten</label>
                                <Multiselect v-model="form.kab" :options="kabsDrop.options"
                                    placeholder="-- Pilih Kabupaten/Kota --" :searchable="true"
                                    @change="loadKecamatans" />
                            </div>
                            <div v-if="form.tingkat > 1" class="mb-3" id="kecamatan-group">
                                <label for="kec-label">Kecamatan</label>
                                <Multiselect v-model="form.kec" :options="kecsDrop.options"
                                    placeholder="-- Pilih Kecamatan --" :searchable="true" @change="loadDesa" />
                            </div>
                            <div v-if="form.tingkat > 2" class="mb-3" id="desa-group">
                                <label for="desa-label">Desa</label>
                                <Multiselect v-model="form.desa" :options="desaDrop.options"
                                    placeholder="-- Pilih Desa/Kelurahan --" :searchable="true" />
                            </div>
                        </div>
                    </form>
                </template>
                <template v-slot:modalFunction>
                    <button id="" type="button" class="btn btn-sm bg-success-fordone" :disabled="form.processing"
                        @click.prevent="submit">Simpan</button>
                </template>
            </ModalBs>
            <ModalBs v-if="DOMLoaded" :ModalStatus="deleteModalStatus" @close="deleteModalStatus = false"
                :title="'Hapus Produsen Data'">
                <template v-slot:modalBody>
                    <label>Apakah Anda yakin akan menghapus Produsen Data ini?</label>
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