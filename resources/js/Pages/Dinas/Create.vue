<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { defineComponent, ref } from 'vue';
import Multiselect from '@vueform/multiselect';
import VueMultiselect from 'vue-multiselect';

const page = usePage()
defineProps({
    kabs: Object
})
defineComponent({
    Multiselect, VueMultiselect
})
const value = ref("")
const options = [
    { label: "Provinsi", value: 0 },
    { label: "Kabupaten/Kota", value: 1 },
    { label: "Kecamatan", value: 2 },
    { label: "Desa/Kelurahan", value: 3 },
]
const optionsLabels = function ({ label }) {
    return `${label}`
}
</script>

<template>

    <Head title="Tambah Produsen" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="h4">
                PRODUSEN DATA
            </div>
            <div class="card">
                <div class="card-body">
                    <form id="DinasForm" method="post" class="form-horizontal mb-3">
                        <label for="nama">Nama Produsen Data</label>
                        <input class="form-control" name="nama" id="nama" placeholder="Isi Nama Produsen Data">
                        <div id="error-nama" class="text-danger mb-3"></div>
                        <div name="wilayah_fullcode" class="d-none">x</div>
                        <div class="mb-3">
                            <label for="tingkat-label">Tingkatan Wilayah</label>
                            <VueMultiselect v-model="value" :options="options" :custom-label="optionsLabels"
                                placeholder="-- Pilih Tingkatan --" label="name" track-by="name">
                            </VueMultiselect>
                        </div>
                        <div class="mb-3 d-none" id="kabupaten-group">
                            <label for="kab-label">Kabupaten</label>
                            <select name="kab" class="form-control row-select" id="kab-label-select">
                                <option value="" disabled selected hidden>-- Pilih Kabupaten --</option>
                                <option v-for="kab in kabs" :key="kab.wilayah_fullcode" :value="kab.wilayah_fullcode">{{
                                kab.label }}</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="kecamatan-group">
                            <label for="kec-label">Kecamatan</label>
                            <select name="kec" class="form-control row-select" id="kec-label-select">
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="desa-group">
                            <label for="desa-label">Desa</label>
                            <select name="desa" class="form-control row-select" id="desa-label-select">
                            </select>
                        </div>
                    </form>
                    <div class="ml-auto">
                        <button id="dinas-save" class="btn bg-success-fordone">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </GeneralLayout>
</template>

<style src="@vueform/multiselect/themes/default.css"></style>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
