<script setup>
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import Multiselect from "@vueform/multiselect";
import ModalBs from "@/Components/ModalBs.vue";
import TabelPreview from "@/Components/TabelPreview.vue";
import SpinnerBorder from "@/Components/SpinnerBorder.vue";

import { ref, defineComponent, onMounted, watch, onUnmounted } from "vue";
import { Head, usePage, useForm, Link } from "@inertiajs/vue3";

defineComponent({
    Multiselect,
});
const page = usePage();
const subjects = page.props.subjects;
const dinas = page.props.dinas;
const triggerSpinner = ref(false)
const previewModalStatus = ref(false)
const subjectDrop = ref({
    value: null,
    options: subjects,
});
const dinasDrop = ref({
    value: null,
    options: dinas,
});


const form = useForm({
    id: page.props.tabel.id,
    tabel: {
        nomor: page.props.tabel.nomor,
        label: page.props.tabel.label,
        unit: page.props.tabel.unit,
        id_dinas: page.props.tabel.id_dinas,
        id_subjek: page.props.tabel.id_subjek
    }
})

const submit = function () {
    form.post(route('tabel.update'), {
        onBefore: function () { triggerSpinner.value = true },
        onFinish: function () { triggerSpinner.value = false },
        onError: function () { triggerSpinner.value = false },
    })
}
</script>
<template>

    <Head title="Edit Tabel" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container pb-3">
            <div class="card">
                <div class="card-body bg-info-fordone text-center">
                    <h2>Edit Tabel</h2>
                </div>
            </div>
            <form @submit.prevent="submit" id="form-create-tabel">
                <div class="form-group">
                    <div class="card mb-3">
                        <div class="card-header">
                            <label class="h5 mb-0">Deskripsi Umum</label>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="dinas">Produsen Data</label>
                                <Multiselect v-model="form.tabel.id_dinas" :options="dinasDrop.options"
                                    placeholder="-- Pilih Produsen Data --" :searchable="true" />
                                <div class="text-danger text-left" v-if="true" id="error-dinas"></div>
                            </div>
                            <div class="mb-3">
                                <label for="nomor">Nomor Tabel</label>
                                <input v-model="form.tabel.nomor" type="text" id="nomor" class="form-control"
                                    placeholder="1.1.1" />
                                <div class="text-danger text-left" v-if="true" id="error-nomor"></div>
                            </div>
                            <div class="mb-3">
                                <label for="judul">Judul Tabel</label>
                                <input v-model="form.tabel.label" type="text" id="judul" class="form-control"
                                    placeholder="Isikan judul tabel" />
                                <div class="text-danger text-left" v-if="true" id="error-judul"></div>
                            </div>
                            <div class="mb-3">
                                <label for="subjek">Subjek Tabel</label>
                                <Multiselect v-model="form.tabel.id_subjek" :options="subjectDrop.options"
                                    placeholder="-- Pilih Subjek --" :searchable="true" />
                                <div class="text-danger text-left" v-if="true" id="error-subjek"></div>
                            </div>
                            <div class="mb-3">
                                <label for="unit">Satuan/Unit Data</label>
                                <input v-model="form.tabel.unit" type="text" id="unit" class="form-control"
                                    placeholder="Isikan satuan/unit data" />
                                <div class="text-danger text-left" v-if="true" id="error-unit"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="mb-2 d-flex">
                <div class="flex-grow-1">
                    <Link :href="route('tabel.master')" class="btn btn-light border"><i class="fas fa-chevron-left"></i>
                        Kembali
                    </Link>
                </div>
                <a @click.prevent="submit" class="btn bg-success-fordone"><i class="fa-solid fa-save"></i>
                    Simpan</a>
            </div>
        </div>
    </GeneralLayout>
</template>
<style scoped>
.card-header {
    border-bottom-color: #3d3b8e;
    border-bottom-width: 3px;
}
</style>
