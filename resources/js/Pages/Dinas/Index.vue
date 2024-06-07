<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import ModalBs from '@/Components/ModalBs.vue';
import Multiselect from '@vueform/multiselect';
import SpinnerBorder from '@/Components/SpinnerBorder.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Pagination from '@/Components/Pagination.vue'
import { Link, useForm, usePage, Head } from '@inertiajs/vue3'
import { ref, defineComponent, watch } from 'vue'
import { clickSortProperties } from '@/sortAttribute';
import { GoDownload } from '@/download'
import { computed } from 'vue';

const page = usePage()
const tabelDinas = ref(null)
const updateModalStatus = ref(false)
const deleteModalStatus = ref(false)
const toggleFlash = ref(false)
const searchNama = ref(null)
const searchWilayah = ref(null)
const triggerSpinner = ref(false)
const downloadModalStatus = ref(false)
const downloadTitle = ref(null)
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
    _token: null,
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
const filteredColumns = computed(() => {
    if (searchNama.value && !searchWilayah.value) {
        return page.props.dinas.filter(x =>
            x.nama.toLowerCase().includes(searchNama.value.toLowerCase()))
    } else if (searchWilayah.value && !searchNama.value) {
        return page.props.dinas.filter(x =>
            x.wilayah_label.toLowerCase().includes(searchWilayah.value.toLowerCase())
        )
    } else if (searchNama.value && searchWilayah.value) {
        return page.props.dinas.filter(x =>
            x.nama.toLowerCase().includes(searchNama.value.toLowerCase()) &&
            x.wilayah_label.toLowerCase().includes(searchWilayah.value.toLowerCase())
        )
    } else {
        return page.props.dinas
    }
})
watch([searchNama, searchWilayah], () => {
    // filterData()
    d.value = filteredColumns.value
})

defineComponent({
    Multiselect
})
const submit = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
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
const deleteForm = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
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
// pagination vue
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
watch(() => page.props.dinas, (value) => {
    d.value = value
})
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
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" /></button>
                <Link :href="route('dinas.create')" class="btn bg-info-fordone"><font-awesome-icon
                    icon="fa-solid fa-plus" />
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
                    <tr v-if="d.length > 0" v-for="din in paginatedData" :key="din.id">
                        <td>{{ din.number }}</td>
                        <td>{{ din.nama }}</td>
                        <td class="">{{ din.wilayah_label }}</td>
                        <td class="text-center deleted">
                            <a @click.prevent="toggleUpdateModal(din.id)" class="update-pen">
                                <font-awesome-icon icon="fa-solid fa-pen" />
                            </a>
                        </td>
                        <td class="text-center deleted">
                            <a @click.prevent="toggleDeleteModal(din.id)" class="delete-trash">
                                <font-awesome-icon icon="fa-solid fa-trash-can" class="icon-trash-color" />
                            </a>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="5" class="text-center">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="d.length" :current-page="currentPage" />
        <Teleport to="body">
            <ModalBs :-modal-status="downloadModalStatus" @close="downloadModalStatus = false" :title="'Download Data'">
                <template #modalBody>
                    <label>Masukkan Judul File</label>
                    <input type="text" v-model="downloadTitle" class="form-control" />
                </template>
                <template #modalFunction>
                    <button type="button" class="btn btn-sm bg-success-fordone"
                        @click.prevent="GoDownload('tabel-dinas', downloadTitle)">Simpan</button>
                </template>
            </ModalBs>
            <ModalBs :ModalStatus="updateModalStatus" @close="updateModalStatus = false"
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
            <ModalBs :ModalStatus="deleteModalStatus" @close="deleteModalStatus = false" :title="'Hapus Produsen Data'">
                <template v-slot:modalBody>
                    <label>Apakah Anda yakin akan menghapus Produsen Data ini?</label>
                </template>
                <template v-slot:modalFunction>
                    <button type="button" class="btn btn-sm badge-status-empat" :disabled="form.processing"
                        @click.prevent="deleteForm">Hapus</button>
                </template>
            </ModalBs>

        </Teleport>
    </GeneralLayout>
</template>