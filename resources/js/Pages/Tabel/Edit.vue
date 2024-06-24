<script setup>
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import Multiselect from "@vueform/multiselect";
import ModalBs from "@/Components/ModalBs.vue";
import TabelPreview from "@/Components/TabelPreview.vue";
import SpinnerBorder from "@/Components/SpinnerBorder.vue";

import { ref, defineComponent, computed, watch } from "vue";
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
const columnLeft = ref(null)
const columnRight = ref(null)
const columnChange = ref({
    value: [],
    options: [],
})
const columnTemp = ref([])
const thisColumn = ref(page.props.columns)

const rowLeft = ref(null)
const rowRight = ref(null)
const rowChange = ref({
    value: [],
    options: [],
})
const rowTemp = ref([])
const thisRow = ref(page.props.rows)
const form = useForm({
    id: page.props.tabel.id,
    tabel: {
        nomor: page.props.tabel.nomor,
        label: page.props.tabel.label,
        unit: page.props.tabel.unit,
        id_dinas: page.props.tabel.id_dinas,
        id_subjek: page.props.tabel.id_subjek
    },
    destroyer: {
        rows: [],
        columns: []
    },
    _token: null,
})

const submit = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
    form.post(route('tabel.update'), {
        onBefore: function () { triggerSpinner.value = true },
        onFinish: function () { triggerSpinner.value = false },
        onError: function () { triggerSpinner.value = false },
    })
}
const columns = computed(() => {
    let result
    if (columnChange.value.value.length > 0) {
        result = thisColumn.value.filter(column => {
            return !(columnTemp.value.includes(column.value))
        })
    } else result = thisColumn.value
    return result
})
const rows = computed(() => {
    let result
    if (rowChange.value.value.length > 0) {
        result = thisRow.value.filter(row => {
            return !(rowTemp.value.includes(row.value))
        })
    } else result = thisRow.value
    return result
})
const addColumnChange = () => {
    if (columnLeft.value && columnRight.value) {
        let columnLabel = thisColumn.value.filter(x => x.value == columnLeft.value)
        let columnLabel2 = page.props.columnBase.filter(x => x.value == columnRight.value)
        let theArray = columnLeft.value + '->' + columnRight.value
        let arrayLabel = columnLabel[0].label + ' -> ' + columnLabel2[0].label
        let arrayValue = theArray
        columnChange.value.options.push(
            { value: arrayValue, label: arrayLabel }
        )
        columnChange.value.value.push(arrayValue)
        columnTemp.value.push(columnLeft.value)
        columnLeft.value = null
        columnRight.value = null
    }
}
const addRowChange = () => {
    if (rowLeft.value && rowRight.value) {
        let rowLabel = thisRow.value.filter(x => x.value == rowLeft.value)
        let rowLabel2 = page.props.rowBase.filter(x => x.value == rowRight.value)
        let theArray = rowLeft.value + '->' + rowRight.value
        let arrayLabel = rowLabel[0].label + ' -> ' + rowLabel2[0].label
        let arrayValue = theArray
        rowChange.value.options.push(
            { value: arrayValue, label: arrayLabel }
        )
        rowChange.value.value.push(arrayValue)
        rowTemp.value.push(rowLeft.value)
        rowLeft.value = null
        rowRight.value = null
    }
}
const changeStructure = async () => {
    const response = await axios.get(route('token'))
    form._token = response.data
    form.destroyer.columns = columnChange.value.value
    form.destroyer.rows = rowChange.value.value
    if (form.processing) return
    form.post(route('tabel.changeStructure'), {
        onBefore: function () { triggerSpinner.value = true },
        onFinish: function () { 
            triggerSpinner.value = false
            columnChange.value.value = []
            columnChange.value.options = []
            rowChange.value.value = []
            rowChange.value.options = []
        },
        onError: function () { triggerSpinner.value = false },
    })
}
watch(() => page.props.columns, (value) => {
    thisColumn.value = value
})
watch(()=>page.props.rows, (value) => {
    thisRow.value = value
})
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
                    <div class="card">
                        <div class="card-header">
                            <label class="h5 mb-0">The One Who Destroy The Database</label>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Daftar Perubahan di Kolom</label>
                                <Multiselect v-model="columnChange.value" mode="tags" :options="columnChange.options"
                                    :placeholder="'-- Daftar Perubahan di Kolom --'" />
                            </div>
                            <div class="mb-3 row">
                                <div class="col">
                                    <label for="column-groups">Daftar Kolom</label>
                                    <Multiselect v-model="columnLeft" :options="columns" :searchable="true"
                                        placeholder="-- Pilih Kolom --" />
                                </div>
                                <div class="col">
                                    <label for="column-groups">Kolom Pengganti</label>
                                    <Multiselect v-model="columnRight" :options="page.props.columnBase"
                                        :searchable="true" placeholder="-- Pilih Kolom Pengganti --" />
                                </div>
                                <div class="col-1">
                                    <label><br></label>
                                    <button @click.prevent="addColumnChange" type="button"
                                        class="btn btn-sm bg-success-fordone">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Daftar Perubahan di Baris</label>
                                <Multiselect v-model="rowChange.value" mode="tags" :options="rowChange.options"
                                    :placeholder="'-- Daftar Perubahan di Baris --'" />
                            </div>
                            <div class="mb-3 row">
                                <div class="col">
                                    <label for="column-groups">Daftar Baris</label>
                                    <Multiselect v-model="rowLeft" :options="rows" :searchable="true"
                                        placeholder="-- Pilih Baris --" />
                                </div>
                                <div class="col">
                                    <label for="column-groups">Baris Pengganti</label>
                                    <Multiselect v-model="rowRight" :options="page.props.rowBase"
                                        :searchable="true" placeholder="-- Pilih Baris Pengganti --" />
                                </div>
                                <div class="col-1">
                                    <label><br></label>
                                    <button @click.prevent="addRowChange" type="button"
                                        class="btn btn-sm bg-success-fordone">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button @click.prevent="changeStructure" type="button" class="btn btn-sm bg-success-fordone">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="mb-2 d-flex">
                <div class="flex-grow-1">
                    <Link :href="route('tabel.master')" class="btn btn-light border"><font-awesome-icon
                        icon="fas fa-chevron-left" />
                    Kembali
                    </Link>
                </div>
                <a @click.prevent="submit" class="btn bg-success-fordone"><font-awesome-icon icon="fa-solid fa-save" />
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
