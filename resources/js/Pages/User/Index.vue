<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { getPagination } from '@/pagination'
import { Head } from '@inertiajs/vue3'
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
        
    </GeneralLayout>
</template>