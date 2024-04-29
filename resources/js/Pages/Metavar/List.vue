<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import { Link, Head, usePage, useForm } from '@inertiajs/vue3'
import { clickSortProperties } from '@/sortAttribute'
import SpinnerBorder from '@/Components/SpinnerBorder.vue'
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import { searchCell } from '@/searchCell'
import { getPagination } from '@/pagination'
import Multiselect from '@vueform/multiselect'
import { ref, watch, onMounted, defineComponent } from 'vue'
import axios from 'axios'

const page = usePage()
const master = page.props.master_metavar.map((obj) => ({
    value: obj.id,
    label: obj.r101,
}))
const masterDrop = ref({
    value: null,
    options: [...[{ label: 'Baru/Tidak Menggunakan Master', value: 0 }], ...master]
})
defineComponent({
    Multiselect
})
const triggerSpinner = ref(false)
const createModalStatus = ref(false)
const masterModalStatus = ref(false)
const searchR101 = ref(null)
const searchR102 = ref(null)
const searchR103 = ref(null)
const searchR104 = ref(null)
const searchSatuan = ref(null)

const form = useForm({
    id: '',
    id_tabel: '',
    r101: '',
    r102: '',
    r103: '',
    r104: '',
    r105: '',
    r106: '',
    r107: '',
    r108: '',
    r109: '',
    r110: '',
    r111: '',
    r112: '',
})

const fetchMastertoApply = async function () {
    try {
        masterModalStatus.value = false
        triggerSpinner.value = true
        const response = await axios.get(route('metavar.fetchMaster', { id: masterDrop.value.value }))
        console.log(response.data)
        form.r101 = response.data.r101
        form.r102 = response.data.r102
        form.r103 = response.data.r103
        form.r104 = response.data.r104
        form.r105 = response.data.r105
        form.r106 = response.data.r106
        form.r107 = response.data.r107
        form.r108 = response.data.r108
        form.r109 = response.data.r109
        form.r110 = response.data.r110
        form.r111 = response.data.r111
        form.r112 = response.data.r112
    } catch (error) {
        console.error("Error fetching master:", error)
    }
    triggerSpinner.value = false
    createModalStatus.value = true
}
const submit = () => {
    form.id_tabel = page.props.id_tabel
    form.post(route('metavar.store'), {
        onBefore: () => {
            triggerSpinner.value = true
            createModalStatus.value = false
        },
        onFinish: () => {
            triggerSpinner.value = false
        },
        onSuccess: () => {
            form.reset()
        },
        onError: () => {
            createModalStatus.value = true
        }
    })
}
const toggleUpdateModal = async function (id) {
    try {
        const response = await axios.get(route('metavar.fetchData', { id: id }))
        createModalStatus.value = true
        form.id = id
        form.id_tabel = page.props.id_tabel
        form.r101 = response.data.r101
        form.r102 = response.data.r102
        form.r103 = response.data.r103
        form.r104 = response.data.r104
        form.r105 = response.data.r105
        form.r106 = response.data.r106
        form.r107 = response.data.r107
        form.r108 = response.data.r108
        form.r109 = response.data.r109
        form.r110 = response.data.r110
        form.r111 = response.data.r111
        form.r112 = response.data.r112
    } catch (error) {
        console.error("Error fetching data:", error)
    }
}
</script>

<template>

    <Head title="Isi Metadata Variabel" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Tabel : {{ page.props.judul }} :
                    <span v-if="page.props.status_desc">{{ page.props.status_desc }}</span>
                    <span v-else>Kosong</span>
                </div>
                <a href="#" class="btn bg-success-fordone mr-2" title="Download" data-target="#downloadModal"
                    data-toggle="modal"><i class="fa-solid fa-circle-down"></i></a>
                <a @click="masterModalStatus = true" class="btn bg-info-fordone"><i class="fa-solid fa-plus"></i>
                    Tambah Metadata Variabel Baru</a>
            </div>
        </div>
        <table class="table table-sorted table-hover table-bordered table-search" ref="tabelListMetavar"
            id="tabel-user">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column" @click="clickSortProperties(page.props.metavars, 'number')">No.</th>
                    <th class="text-center" @click="clickSortProperties(page.props.metavars, 'r101')">Nama Variabel</th>
                    <th class="text-center tabel-width-15" @click="clickSortProperties(page.props.metavars, 'r102')">
                        Konsep</th>
                    <th class="text-center tabel-width-20" @click="clickSortProperties(page.props.metavars, 'r103')">
                        Definisi</th>
                    <th class="text-center tabel-width-20" @click="clickSortProperties(page.props.metavars, 'r104')">
                        Referensi Pemilihan
                    </th>
                    <th class="text-center" @click="clickSortProperties(page.props.metavars, 'satuan')">Satuan</th>
                    <th class="text-center deleted">Lihat Detail/Edit</th>
                    <th class="text-center deleted">Hapus</th>
                </tr>
                <tr class="">
                    <td class="search-header"></td>
                    <td class="search-header"><input v-model.trim="searchR101" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchR102" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchR103" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchR104" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchSatuan" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header deleted"></td>
                    <td class="search-header deleted"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(node, index) in page.props.metavars" :key="node.id">
                    <td>{{ node.number }}</td>
                    <td>{{ node.r101 }}</td>
                    <td>{{ node.r102 }}</td>
                    <td>{{ node.r103 }}</td>
                    <td>{{ node.r104 }}</td>
                    <td>{{ node.satuan }}</td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleUpdateModal(node.id)" class="edit-pen"><i class="fa-solid fa-pencil"
                                title="Edit Data"></i></a>
                    </td>
                    <td class="text-center deleted">

                    </td>
                </tr>
            </tbody>
        </table>
        <Teleport to="body">
            <ModalBs :ModalStatus="createModalStatus" @close="createModalStatus = false"
                :modalSize="'modal-xl modal-dialog-scrollable'">
                <template #modalBody>
                    <div class="form-group">
                        <input id="id_tabel" name="id_tabel" hidden>
                        <label for="r101">Nama Variabel</label>
                        <input class="form-control mb-3" id="r101" v-model="form.r101" placeholder="Isi nama variabel">
                        <label for="r102">Konsep</label>
                        <input class="form-control mb-3" id="r102" v-model="form.r102"
                            placeholder="Isi konsep yang sesuai">
                        <label for="r103">Definisi</label>
                        <textarea class="form-control mb-3" id="r103" v-model="form.r103"
                            placeholder="Isi definisi yang sesuai"></textarea>
                        <label for="r104">Referensi Pemilihan</label>
                        <textarea class="form-control mb-3" id="r104" v-model="form.r104"
                            placeholder="Isi referensi pemilihan dari variabel"></textarea>
                        <label for="r105">Referensi Waktu</label>
                        <input class="form-control mb-3" id="r105" v-model="form.r105"
                            placeholder="Isi referensi waktu dari variabel">
                        <label for="satuan">Satuan</label>
                        <input class="form-control mb-3" id="satuan" v-model="form.satuan" placeholder="">
                        <label for="r106">Alias</label>
                        <input class="form-control mb-3" id="r106" v-model="form.r106"
                            placeholder="Isi alias dari variabel">
                        <label for="r107">Ukuran</label>
                        <input class="form-control mb-3" id="r107" v-model="form.r107"
                            placeholder="Isi ukuran dari variabel">
                        <label for="r108">Tipe Data</label>
                        <input class="form-control mb-3" id="r108" v-model="form.r108"
                            placeholder="Isi tipe data dari variabel">
                        <label for="r109">Klasifikasi Isian</label>
                        <input class="form-control mb-3" id="r109" v-model="form.r109"
                            placeholder="Apakah variabel memiliki klasifikasi isian?">
                        <label for="r110">Aturan Validasi</label>
                        <input class="form-control mb-3" id="r110" v-model="form.r110"
                            placeholder="Bagaimana aturan validasi untuk variabel?">
                        <label for="r111">Kalimat Pertanyaan</label>
                        <input class="form-control mb-3" id="r111" v-model="form.r111"
                            placeholder="Bagaimana bentuk kalimat pertanyaan untuk variabel?">
                        <label for="r112">Apakah variabel dapat diakses?</label>
                        <Multiselect :options="[{ label: 'Ya', value: '1' }, { label: 'Tidak', value: '2' }]"
                            v-model="form.r112" placeholder="-- Pilih ya/tidak --" />
                    </div>
                </template>
                <template #modalFunction>
                    <button type="button" class="btn btn-sm bg-success-fordone" :disabled="form.processing"
                        @click.prevent="submit">Simpan</button>
                </template>
            </ModalBs>
            <ModalBs :ModalStatus="masterModalStatus" @close="masterModalStatus = false"
                :title="'Master Metadata Variabel'">
                <template #modalBody>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="label">Master Metadata Variabel</label>
                            <Multiselect :options="masterDrop.options" v-model="masterDrop.value"
                                placeholder="-- Pilih Master --" />
                        </div>
                    </div>
                </template>
                <template #modalFunction>
                    <button type="button" class="btn btn-sm bg-success-fordone" :disabled="form.processing"
                        @click.prevent="fetchMastertoApply">Simpan</button>
                </template>
            </ModalBs>
        </Teleport>
    </GeneralLayout>
</template>