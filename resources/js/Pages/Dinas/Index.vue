<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { Link, usePage } from '@inertiajs/vue3'
import { getPagination } from '@/pagination';
import { ref, onMounted, onUpdated } from 'vue'
import ModalBs from '@/Components/ModalBs.vue';
import Modal from '@/Components/Modal.vue';

const tabelDinas = ref(null)
const currentPagination = ref(null)
const maxRows = ref(null)
const statusText = ref(null)
const updateModalStatus = ref(false)
const DOMLoaded = ref(false)
const dialog = ref(false)

const toggleUpdateModal = function () {
    updateModalStatus.value = !updateModalStatus.value
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
const showModal = ref(false)
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
                            <a @click.prevent="toggleUpdateModal" class="update-pen"
                                data-dinas="din.id . ';' . din.nama . ';' . din.wilayah_fullcode" data-bs-toggle="modal"
                                data-bs-target="#updateModal">
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
        <ModalBs v-if="DOMLoaded" :updateModalStatus="updateModalStatus" :toggleModalClose="toggleUpdateModal"
            :title="'Update Produsen Data'"></ModalBs>
        <button id="show-modal" @click="showModal = true">Show Modal</button>
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