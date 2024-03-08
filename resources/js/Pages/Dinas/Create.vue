<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { defineComponent, ref } from 'vue';
import Multiselect from '@vueform/multiselect';
import VueMultiselect from 'vue-multiselect';

const page = usePage()
const kabs = page.props.kabs
const kecs = ref([])
const desa = ref([])

defineComponent({
    Multiselect, VueMultiselect
})

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
    options: kabs.slice(1)
})

const loadKecamatans = async (valueKabs) => {
    try {
        let kabs = valueKabs.substring(2, 4)
        const response = await axios.get('/master/wilayah/kecamatan/' + kabs);
        kecs.value = response.data.data

    } catch (error) {
        console.error('Error fetching kecamatan:', error);
    }
};
const kecsDrop = ref({
    value: null,
    options: kecs,
})
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
const desaDrop = ref({
    value: null,
    options: desa,
})
const form = useForm({
    nama: '',
    tingkat: '',
    kab: '',
    kec: '',
    desa: '',
})
const submit = function () {
    form.post(route('dinas.store'))
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
                    <form @submit.prevent="submit" id="DinasForm" class="form-horizontal mb-3">
                        <label for="nama">Nama Produsen Data</label>
                        <input class="form-control" name="nama" id="nama" v-model="form.nama" placeholder="Isi Nama Produsen Data">
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
                                placeholder="-- Pilih Kabupaten/Kota --" :searchable="true" @change="loadKecamatans" />
                        </div>
                        <div v-if="form.tingkat > 1" class="mb-3" id="kecamatan-group">
                            <label for="kec-label">Kecamatan</label>
                            <Multiselect v-model="form.kec" :options="kecsDrop.options"
                                placeholder="-- Pilih Kecamatan --" :searchable="true" @change="loadDesa"/>
                        </div>
                        <div v-if="form.tingkat > 2" class="mb-3" id="desa-group">
                            <label for="desa-label">Desa</label>
                            <Multiselect v-model="form.desa" :options="desaDrop.options"
                                placeholder="-- Pilih Desa/Kelurahan --" :searchable="true" />
                        </div>
                        <div class="ml-auto">
                            <button id="dinas-save" class="btn bg-success-fordone">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GeneralLayout>
</template>

<style src="@vueform/multiselect/themes/default.css"></style>
<!-- <style src="vue-multiselect/dist/vue-multiselect.css"></style> -->