<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import Multiselect from '@vueform/multiselect'
import { ref, defineComponent } from 'vue'
import { Head, usePage, useForm, Link } from "@inertiajs/vue3"

defineComponent({
    Multiselect
})
const page = usePage()
const subjects = page.props.subjects
const dinas = page.props.dinas

const subjectDrop = ref({
    value: null,
    options: subjects,
})
const dinasDrop = ref({
    value: null,
    options: dinas,
})
</script>
<template>

    <GeneralLayout>
        <div class="container">
            <div class="card">
                <div class="card-body bg-info-fordone text-center">
                    <h2>Buat Tabel Baru</h2>
                </div>
            </div>
            <div class="form-group">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="card-header mb-3">
                            <h5>Deskripsi Umum</h5>
                        </div>
                        <div class="mb-3">
                            <label for="dinas">Produsen Data</label>
                            <Multiselect v-model="dinasDrop.value" :options="dinasDrop.options"
                                placeholder="-- Pilih Produsen Data --" :searchable="true" />
                            <div class="text-danger text-left" v-if="true" id="error-dinas"></div>
                        </div>
                        <div class="mb-3">
                            <label for="nomor">Nomor Tabel</label>
                            <input type="text" id="nomor" class="form-control">
                            <div class="text-danger text-left" v-if="true" id="error-nomor"></div>
                        </div>
                        <div class="mb-3">
                            <label for="judul">Judul Tabel</label>
                            <input type="text" id="judul" class="form-control">
                            <div class="text-danger text-left" v-if="true" id="error-judul"></div>
                        </div>
                        <div class="mb-3">
                            <label for="subjek">Subjek Tabel</label>
                            <Multiselect v-model="subjectDrop.value" :options="subjectDrop.options"
                                placeholder="-- Pilih Subjek --" :searchable="true" />
                            <div class="text-danger text-left" v-if="true" id="error-subjek"></div>
                        </div>
                        <div class="mb-3">
                            <label for="unit">Satuan/Unit Data</label>
                            <input type="text" id="unit" class="form-control">
                            <div class="text-danger text-left" v-if="true" id="error-unit"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GeneralLayout>
</template>