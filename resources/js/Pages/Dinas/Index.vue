<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { getPagination } from '@/pagination';
import { ref, onMounted, onUpdated, defineComponent } from 'vue'
import ModalBs from '@/Components/ModalBs.vue';
import Multiselect from '@vueform/multiselect';

const page = usePage()
const tabelDinas = ref(null)
const currentPagination = ref(null)
const maxRows = ref(null)
const statusText = ref(null)
const updateModalStatus = ref(false)
const DOMLoaded = ref(false)
const toggleFlash = ref(false)
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
    updateModalStatus.value = true
    if (id) {
        fetchProdusen(id)
    }
}

const fetchProdusen = async function (id) {
    try {
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

const hideFlashMessage = function () {
    setInterval(() => {
        toggleFlash.value = false
    }, 2000);
}

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
defineProps({
    dinas: Object,
})
defineComponent({
    Multiselect
})
const submit = function () {
    form.post(route('dinas.update'), {
        onSuccess: function () {
            updateModalStatus.value = false
            if (page.props.flash.message) toggleFlash.value = true
            hideFlashMessage()
        }
    })
}
</script>

<template>
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
            <div class="alert alert-success" v-if="toggleFlash" role="alert">
                {{ page.props.flash.message }}
            </div>
            <table class="table table-sorted table-hover table-bordered table-search" ref="tabelDinas" id="tabel-dinas">
                <thead>
                    <tr class="bg-info-fordone">
                        <th class="first-column">No.</th>
                        <th class="text-center tabel-width-50">Nama Produsen Data</th>
                        <th class="text-center">Wilayah Kerja</th>
                        <th class="text-center deleted">Edit</th>
                        <th class="text-center deleted">Hapus</th>
                    </tr>
                    <tr class="">
                        <td class="search-header"></td>
                        <td class="search-header"><input type="text" class="search-input form-control"></td>
                        <td class="search-header"><input type="text" class="search-input form-control"></td>
                        <td class="search-header"></td>
                        <td class="search-header"></td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="din in dinas" :key="din.id">
                        <td>{{ din.number }}</td>
                        <td>{{ din.nama }}</td>
                        <td class="">{{ din.wilayah.label }}</td>
                        <td class="text-center deleted">
                            <a @click.prevent="toggleUpdateModal(din.id)" class="update-pen">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                        </td>
                        <td class="text-center deleted">
                            <a href="" class="delete-trash" data-dinas="{{ json_encode([
                                    'id' => din.id,
                                ]) }}" data-toggle="modal" data-target="#deleteModal">
                                <i class="fa-solid fa-trash-can icon-trash-color"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Teleport to="body">
            <ModalBs v-if="DOMLoaded" :updateModalStatus="updateModalStatus" @close="updateModalStatus = false"
                :title="'Update Produsen Data'">
                <template v-slot:modalBody>
                    <form id="DinasForm" class="form-horizontal mb-3">
                        <div class="form-group">
                            <label for="nama">Nama Produsen Data</label>
                            <input class="form-control" name="nama" id="nama" v-model="form.nama"
                                placeholder="Isi Nama Produsen Data">
                            <div id="error-nama" class="text-danger mb-3"></div>
                            <div name="wilayah_fullcode" class="d-none">x</div>
                            <div class="mb-3">
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
                    <button id="" type="button" class="btn btn-sm bg-success-fordone"
                        @click.prevent="submit">Simpan</button>
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