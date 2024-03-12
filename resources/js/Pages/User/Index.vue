<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { getPagination } from '@/pagination'
import { Head, usePage } from '@inertiajs/vue3'

const page = usePage()
const users = page.props.users

</script>
<template>

    <Head title="Daftar Pengguna" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Pengguna
                </div>
                <a href="#" class="btn bg-success-fordone mr-2" title="Download" data-target="#downloadModal"
                    data-toggle="modal"><i class="fa-solid fa-circle-down"></i></a>
                <Link :href="route('dinas.create')" class="btn bg-info-fordone"><i class="fa-solid fa-plus"></i>
                Tambah Pengguna Baru (Belom)</Link>
            </div>
        </div>
        <!-- <div class="alert alert-success" v-if="" role="alert"></div> -->
        <table class="table table-sorted table-hover table-bordered table-search" id="tabel-user">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column">No.</th>
                    <th class="text-center">Username</th>
                    <th class="text-center tabel-width-15">Nama</th>
                    <th class="text-center tabel-width-20">Nama Instansi</th>
                    <th class="text-center tabel-width-20">Wilayah Kerja</th>
                    <th class="text-center">No. HP</th>
                    <th class="text-center">Peran</th>
                    <th class="text-center deleted tabel-width-5">Edit</th>
                    <th class="text-center deleted">Hapus</th>
                </tr>
                <tr class="">
                    <td class="search-header"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"></td>
                    <td class="search-header"><input type="text" class="search-input form-control"></td>
                    <td class="search-header deleted"></td>
                    <td class="search-header deleted"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.number }}</td>
                    <td>{{ user.username }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.dinas.nama }}</td>
                    <td>{{ user.wilayah_label }}</td>
                    <td>{{ user.noHp }}</td>
                    <td>{{ user.role }}</td>
                    <td class="text-center deleted">
                        <a href="" class="update-pen mx-1">
                            <i class="fa-solid fa-lock" title="Reset Password"></i>
                        </a>
                        <a href="" class="mx-1 role-update"><i class="role-icon fa-solid" title="Ubah Role"></i></a>
                        <a href="" class="edit-pen mx-1">
                            <i class="fa-solid fa-pencil" title="Edit Pengguna"></i>
                        </a>
                    </td>
                    <td class="text-center deleted">
                        <a href="" class="delete-trash">
                            <i class="fa-solid fa-trash-can icon-trash-color"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </GeneralLayout>
</template>