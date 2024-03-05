<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
// const Dinas = page.props.dinas
defineProps({
    dinas: Object,
})
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
            <table class="table table-sorted table-hover table-bordered table-search" id="tabel-dinas">
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
                            <a href="" class="update-pen"
                                data-dinas="{{ din.id . ';' . din.nama . ';' . din.wilayah_fullcode }}"
                                data-toggle="modal" data-target="#updateModal">
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
        @include('dinas.modal')
        <div class="row d-flex justify-content-end align-items-center">
            <div class="mb-3 mx-3 ml-auto">Menampilkan <span id="showPage"></span> dari <span id="showTotal"></span>
            </div>
            <div class="form-group"> <!--		Show Numbers Of Rows 		-->
                <select class="form-control" name="state" id="maxRows">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="pagination-container">
                <nav>
                    <ul class="pagination">
                        <li data-page="prev">
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