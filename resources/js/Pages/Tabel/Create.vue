<script setup>
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import Multiselect from "@vueform/multiselect";
import ModalBs from "@/Components/ModalBs.vue";
import TabelPreview from "@/Components/TabelPreview.vue";
import SpinnerBorder from "@/Components/SpinnerBorder.vue";
import draggable from 'vuedraggable';

import { ref, defineComponent, onMounted, watch, onUnmounted } from "vue";
import { Head, usePage, useForm, Link } from "@inertiajs/vue3";
import axios from "axios";

defineComponent({
    Multiselect, draggable
});
const page = usePage();
const subjects = page.props.subjects;
const dinas = page.props.dinas;
const rowListFetched = ref([]);
const columnListFetched = ref([]);
const turtahunListFetched = ref([]);
const triggerSpinner = ref(false)
const tabelRowList = ref(null);
const kecs = ref([]);
const desa = ref([]);
const kabupatens = page.props.kabupatens;
const previewModalStatus = ref(false)
var kecLists = [];
var desaLists = [];
var kabLists = [];
const provinsi = {
    label: "PROVINSI SULAWESI UTARA",
    value: "7100000000",
};

const rowGroups = page.props.row_groups;
var notUsedRowGroups = [{ label: '-- Tidak Menggunakan Kelompok Baris --', value: -1 }]
const rowGroupsDrop = ref({
    value: null,
    options: [...notUsedRowGroups, ...rowGroups.slice(1)],
});
const allRowIncluded = ref([])
const allRowTempt = ref([])
const allRowSelected = ref([])
const allRowFinal = ref([])
const colGroups = page.props.column_groups;
var notUsedColGroups = [{ label: '-- Tidak Menggunakan Kelompok Kolom --', value: -1 }]
const colGroupsDrop = ref({
    value: null,
    options: [...notUsedColGroups, ...colGroups],
});
watch(allRowSelected, (value) => {
    allRowFinal.value = allRowTempt.value.filter(item => value.includes(item.value))
})
const allColumnIncluded = ref([])
const allColumnSelected = ref([])
const allColumnTempt = ref([])
const allColumnFinal = ref([])
watch(allColumnSelected, (value) => {
    allColumnFinal.value = allColumnTempt.value.filter(item => value.includes(item.value))
})
const yearDrop = ref({
    value: null,
    options: [],
});
const yearDownDrop = ref({
    value: null,
    options: page.props.turtahun_groups
})
const currentYear = new Date().getFullYear();
const years = Array.from({ length: 11 }, (_, index) => currentYear - index);
yearDrop.value.options = years.map((year) => ({
    label: year.toString(),
    value: year.toString(),
}));
const kabs = page.props.kabupatens.map((kab) => ({
    label: kab.label,
    value: kab.wilayah_fullcode,
}));
// onMounted(() => {
// })
// onUnmounted(()=>{
//    kabupatens = page.props.kabupatens
// })

//try first
const rowsCheckBox = ref([]);
const columnsCheckBox = ref([])
const rowsSelected = ref([])
const columnsSelected = ref([])
// Array(rowListFetched.value.length).fill(false)
// rowsCheckBox.value = Array(kabupatens.length).fill(false);

const sended = function () {
    rowsSelected.value = kabupatens.filter((_, index) => {
        return rowsCheckBox.value[index];
    });
};

// const rowsSelected = kabupatens.filter(x => )
const kabsDrop = ref({
    value: null,
    options: kabs,
});
const kecsDrop = ref({
    value: null,
    options: kecs,
});
const subjectDrop = ref({
    value: null,
    options: subjects,
});
const dinasDrop = ref({
    value: null,
    options: dinas,
});
const rowTypeDrop = ref({
    value: null,
    options: [
        { value: "1", label: "wilayah" },
        { value: "2", label: "non-wilayah" },
    ],
});

const tingkatanDrop = ref({
    value: null,
    options: [
        { label: "Provinsi", value: 0 },
        { label: "Kabupaten/Kota", value: 1 },
        { label: "Kecamatan", value: 2 },
        { label: "Desa/Kelurahan", value: 3 },
    ],
});

const toggleCheck = function (index, object) {
    object[index] = !object[index];
};
const toggleAll = function (object) {
    object.forEach((_, index) => {
        object[index] = !object[index];
    });
};
const triggerTingkatan = function (value) {
    if (value < 2) {
        assignRowListWilayah(value, null);
    }
};
const assignRowListWilayah = function (options, parents) {
    rowListFetched.value = []
    switch (options) {
        case 0:
            rowListFetched.value = [
                {
                    tipe: "PROVINSI",
                    label: "PROVINSI SULAWESI UTARA",
                    value: "7100000000",
                },
            ];
            break;
        case 1:
            kabLists = kabupatens.map((obj) => ({
                label: obj.label,
                value: obj.wilayah_fullcode,
            }));
            kabLists.push(provinsi);
            kabLists.map((item, key, array) => {
                item.tipe =
                    key === array.length - 1 ? "PROVINSI" : "KABUPATEN/KOTA";
            });
            rowListFetched.value = kabLists;
            break;
        case 2:
            kecLists = kecs.value;
            kecLists.push(parents);
            kecLists.map((item, key, array) => {
                item.tipe =
                    key === array.length - 1 ? "KABUPATEN/KOTA" : "KECAMATAN";
            });
            rowListFetched.value = kecLists;
            break;
        case 3:
            desaLists = desa.value;
            desaLists.push(parents);
            desaLists.map((item, key, array) => {
                item.tipe =
                    key === array.length - 1 ? "KECAMATAN" : "DESA/KELURAHAN";
            });
            rowListFetched.value = desaLists;
            break;

        default:
            break;
    }
    rowsCheckBox.value = Array(rowListFetched.value.length).fill(false);
};
const fetchRow = async function (value) {
    rowListFetched.value = []
    try {
        if (value == -1) {
            const response = await axios.get(route('fetchAllRows'))
            allRowIncluded.value = response.data.map((row) => ({
                label: row.label + " - " + row.cg_label,
                value: row.value
            }))
            allRowTempt.value = response.data
        } else {
            const response = await axios.get("/row/fetchCreate/" + value);
            rowListFetched.value = response.data.data.map((obj) => ({
                label: obj.label,
                tipe: obj.tipe,
                value: obj.id,
            }));
            rowsCheckBox.value = Array(rowListFetched.value.length).fill(false);
        }
    } catch (error) {
        console.error("Error Fetching data:", error);
    }
};
const fetchColumn = async function (value) {
    // columnListFetched.value = []
    try {
        if (value == -1) {
            const response = await axios.get(route('fetchAllColumns'))
            allColumnIncluded.value = response.data.map((column) => ({
                label: column.label + " - " + column.cg_label,
                value: column.value
            }))
            allColumnTempt.value = response.data
        } else {
            const response = await axios.get("/column/fetchCreate/" + value);
            columnListFetched.value = response.data.data.map((obj) => ({
                label: obj.label,
                tipe: obj.tipe,
                value: obj.id,
            }));
            columnsCheckBox.value = Array(columnListFetched.value.length).fill(false)
        }
    } catch (error) {
        console.error("Error Fetching data:", error);
    }
};
const fetchTurtahun = async function (value) {
    turtahunListFetched.value = []
    try {
        const response = await axios.get('/turtahun/fetch/' + value)
        turtahunListFetched.value = response.data.data
    } catch (error) {
        console.error("Error Fetching data:", error);
    }
}
const loadKecamatans = async (valueKabs) => {
    try {
        let kabs = valueKabs.substring(2, 4);
        const response = await axios.get("/master/wilayah/kecamatan/" + kabs);
        kecs.value = response.data.data;
        let tempt = kabsDrop.value.options.filter((x) =>
            x.value.includes(valueKabs)
        );
        let parents = {
            label: tempt[0].label,
            value: tempt[0].value,
        };
        assignRowListWilayah(2, parents);
    } catch (error) {
        console.error("Error fetching kecamatan:", error);
    }
};

const loadDesa = async (valueKecs) => {
    try {
        let kabsFetch = valueKecs.substring(2, 4);
        let kecsFetch = valueKecs.substring(4, 7);
        const response = await axios.get(
            "/master/wilayah/desa/" + kabsFetch + "/" + kecsFetch
        );
        desa.value = response.data.data;
        // console.log(kecs.value)
        // let tempt = 
        let tempt = kecs.value.filter((x) => {
            return x.value.includes(valueKecs)
        })
        let parents = {
            label: tempt[0].label,
            value: tempt[0].value,
        }
        assignRowListWilayah(3, parents);
    } catch (error) {
        console.error("Error fetching desa:", error);
    }
};

const form = useForm({
    tabel: {
        nomor: null,
        label: null,
        unit: null,
        id_dinas: null,
        id_subjek: null,
        edited_by: page.props.auth.user.id
    },
    rows: {
        tipe: null,
        selected: [],
    },
    columns: [],
    tahun: null,
    id_turtahun: null,
    orderRow: null,
    orderColumn: null,
    _token: null,
})
const errorModalStatus = ref(false)
const submit = async function () {
    previewModalStatus.value = false
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
    form.post(route('tabel.store'), {
        onBefore: function () { triggerSpinner.value = true },
        onFinish: function () { triggerSpinner.value = false },
        onError: function () {
            triggerSpinner.value = false
            errorModalStatus.value = true
        },
    })
}

const rowPreview = ref(null)
const columnPreview = ref(null)
const buildValue = function () {
    rowsSelected.value = rowListFetched.value.filter((_, index) => {
        return rowsCheckBox.value[index];
    });
    columnsSelected.value = columnListFetched.value.filter((_, index) => {
        return columnsCheckBox.value[index];
    });
    if (rowGroupsDrop.value.value == -1) {
        form.rows.selected = allRowFinal.value
    } else {
        form.rows.selected = rowsSelected.value
    }
    if (colGroupsDrop.value.value == -1) {
        form.columns = allColumnFinal.value
    } else {
        form.columns = columnsSelected.value
    }
    // if (!form.orderColumn) columnPreview.value = form.columns
    columnPreview.value = (form.orderColumn) ? form.orderColumn : form.columns
    // if (!form.orderRow) rowPreview.value = form.rows.selected
    rowPreview.value = (form.orderRow) ? form.orderRow : form.rows.selected
    previewModalStatus.value = true
}

onMounted(() => {
    // console.log(tabelRowList.value.querySelectorAll('.row-select'))
});
const orderDropRow = ref(2)
const setupOrderRow = (value) => {
    if (value == 1) {
        orderDropRow.value = 1
        form.orderRow = rowListFetched.value.filter((_, index) => {
            return rowsCheckBox.value[index];
        });
    } else {
        orderDropRow.value = 2
        form.orderRow = null
    }
}
const orderDropColumn = ref(2)
const setupOrderColumn = (value) => {
    if (value == 1) {
        orderDropColumn.value = 1
        form.orderColumn = columnListFetched.value.filter((_, index) => {
            return columnsCheckBox.value[index];
        });
    } else {
        orderDropColumn.value = 2
        form.orderColumn = null
    }
}

watch(() => form.rows.tipe, (value) => {
    if (!value) rowListFetched.value = []; tingkatanDrop.value.value = null; orderDropRow.value = 2; rowGroupsDrop.value.value = null;
})
watch(() => colGroupsDrop.value.value, (value) => {
    if (!value) orderDropColumn.value = 2; colGroupsDrop.value.value = null
})

</script>
<template>

    <Head title="Tambah Tabel Baru" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container pb-3">
            <div class="card">
                <div class="card-body bg-info-fordone text-center">
                    <h2>Buat Tabel Baru</h2>
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
                            <label class="h5 mb-0">Detail Baris</label>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="rowType">Tipe Baris</label>
                                <Multiselect v-model="form.rows.tipe" :options="rowTypeDrop.options"
                                    placeholder="-- Pilih Tipe Baris --" :searchable="true" />
                                <div class="text-danger text-left" v-if="true" id="error-rowType"></div>
                            </div>
                            <div class="mb-3" v-if="form.rows.tipe == '1'">
                                <label for="tingkat-label">Tingkatan Wilayah</label>
                                <Multiselect v-model="tingkatanDrop.value" :options="tingkatanDrop.options"
                                    @change="triggerTingkatan" :searchable="true" placeholder="-- Pilih Tingkatan --" />
                            </div>
                            <div v-if="tingkatanDrop.value > 1" class="mb-3" id="kabupaten-group">
                                <label for="kab-label">Kabupaten</label>
                                <Multiselect v-model="kabsDrop.value" :options="kabsDrop.options"
                                    placeholder="-- Pilih Kabupaten/Kota --" :searchable="true"
                                    @change="loadKecamatans" />
                            </div>
                            <div v-if="tingkatanDrop.value > 2" class="mb-3" id="kecamatan-group">
                                <label for="kec-label">Kecamatan</label>
                                <Multiselect v-model="kecsDrop.value" :options="kecsDrop.options"
                                    placeholder="-- Pilih Kecamatan --" :searchable="true" @change="loadDesa" />
                            </div>
                            <div class="mb-3" v-if="form.rows.tipe == '2'">
                                <label for="row-groups">Kelompok Baris</label>
                                <Multiselect v-model="rowGroupsDrop.value" :options="rowGroupsDrop.options"
                                    @change="fetchRow" :searchable="true" placeholder="-- Pilih Kelompok Baris --" />
                            </div>
                            <div class="mb-3" v-if="rowGroupsDrop.value == -1">
                                <label for="row-groups">Daftar Baris</label>
                                <Multiselect v-model="allRowSelected" :options="allRowIncluded" :searchable="true"
                                    mode="tags" placeholder="-- Pilih Baris --" />
                            </div>
                            <div class="mb-3" v-if="rowGroupsDrop.value != -1">
                                <label for="judul">Daftar Baris</label>
                                <table class="table table-hover table-bordered" ref="tabelRowList" id="tabelRowList">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Kelompok Baris</th>
                                            <th>Label</th>
                                            <th>
                                                <div class="btn btn-sm bg-success-fordone mr-1"
                                                    @click="toggleAll(rowsCheckBox)">
                                                    <font-awesome-icon icon="fa fa-check" />
                                                </div>
                                                Pilih Semua
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="form.rows.tipe">
                                        <tr v-if="rowListFetched.length > 0" v-for="(node, index) in rowListFetched"
                                            :key="index" @click="
        toggleCheck(index, rowsCheckBox)
        ">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ node.tipe }}</td>
                                            <td>{{ node.label }}</td>
                                            <td class="text-center">
                                                <input class="row-select" type="checkbox" v-model="rowsCheckBox[index]"
                                                    :value="node.id" ref="checkbox" />
                                            </td>
                                        </tr>
                                        <tr v-else>
                                            <td colspan="4" class="text-center"> Belum ada daftar baris yang tersedia
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mb-3" v-if="rowGroupsDrop.value != -1">
                                <label class="mb-0" for="column-groups">Urutan Baris</label>
                                <div>
                                    <small>(Abaikan Jika tidak ada perubahan urutan baris)</small>
                                </div>
                                <Multiselect class="mb-3" placeholder="Apakah ada perubahan urutan?" :value="2"
                                    @change="setupOrderRow"
                                    :options="[{ label: 'Ada perubahan', value: '1' }, { label: 'Sudah sesuai', value: '2' }]" />
                                <table v-if="orderDropRow == 1" class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Daftar Baris</th>
                                        </tr>
                                    </thead>
                                    <draggable v-model="form.orderRow" tag="tbody" item-key="label">
                                        <template #item="{ element }">
                                            <tr>
                                                <td>{{ element.label }}</td>
                                            </tr>
                                        </template>
                                    </draggable>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <label class="h5 mb-0">Detail Kolom</label>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="column-groups">Kelompok Kolom</label>
                                <Multiselect v-model="colGroupsDrop.value" :options="colGroupsDrop.options"
                                    @change="fetchColumn" :searchable="true" placeholder="-- Pilih Kelompok Kolom --" />
                            </div>
                            <div class="mb-3" v-if="colGroupsDrop.value == -1">
                                <label for="column-groups">Daftar Kolom</label>
                                <Multiselect v-model="allColumnSelected" :options="allColumnIncluded" :searchable="true"
                                    mode="tags" placeholder="-- Pilih Kolom --" />
                            </div>
                            <div class="mb-3" v-if="colGroupsDrop.value != -1">
                                <label for="judul">Daftar Kolom</label>
                                <table class="table table-hover table-bordered" ref="tabelRowList" id="tabelRowList">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Kelompok Kolom</th>
                                            <th>Label</th>
                                            <th>
                                                <div class="btn btn-sm bg-success-fordone mr-1"
                                                    @click="toggleAll(columnsCheckBox)">
                                                    <font-awesome-icon icon="fa fa-check" />
                                                </div>
                                                Pilih Semua
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="columnListFetched.length > 0" v-for="(
                                                node, index
                                            ) in columnListFetched" :key="index" @click="
        toggleCheck(index, columnsCheckBox)
        ">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ node.tipe }}</td>
                                            <td>{{ node.label }}</td>
                                            <td class="text-center">
                                                <input class="row-select" type="checkbox"
                                                    v-model="columnsCheckBox[index]" :value="node.id" ref="checkbox" />
                                            </td>
                                        </tr>
                                        <tr v-else>
                                            <td colspan="4" class="text-center"> Belum ada daftar kolom yang tersedia
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mb-3" v-if="colGroupsDrop.value != -1">
                                <label class="mb-0" for="column-groups">Urutan Kolom</label>
                                <div>
                                    <small>(Abaikan Jika tidak ada perubahan urutan kolom)</small>
                                </div>
                                <Multiselect class="mb-3" placeholder="Apakah ada perubahan urutan?" :value="2"
                                    @change="setupOrderColumn"
                                    :options="[{ label: 'Ada perubahan', value: '1' }, { label: 'Sudah sesuai', value: '2' }]" />
                                <table v-if="orderDropColumn == 1" class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Daftar Kolom</th>
                                        </tr>
                                    </thead>
                                    <draggable v-model="form.orderColumn" tag="tbody" item-key="label">
                                        <template #item="{ element }">
                                            <tr>
                                                <td>{{ element.label }}</td>
                                            </tr>
                                        </template>
                                    </draggable>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <label class="h5 mb-0">Detail Waktu</label>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="column-groups">Pilih Tahun</label>
                                <Multiselect v-model="form.tahun" :options="yearDrop.options" :searchable="true"
                                    placeholder="-- Pilih Tahun --" />
                            </div>
                            <div class="mb-3">
                                <label for="column-groups">Pilih Periode/Turunan Tahun</label>
                                <Multiselect v-model="form.id_turtahun" :options="yearDownDrop.options"
                                    :searchable="true" @change="fetchTurtahun"
                                    placeholder="-- Pilih Periode/Turunan Tahun --" />
                            </div>
                            <div class="mb-3" v-if="true">
                                <label for="judul">Daftar Turunan Tahun</label>
                                <table class="table table-hover table-bordered" ref="tabelRowList" id="tabelRowList">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Tipe</th>
                                            <th>Label</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="form.id_turtahun">
                                        <tr v-for="(
                                                node, index
                                            ) in turtahunListFetched" :key="index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ node.tipe }}</td>
                                            <td>{{ node.label }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body text-right">
                            <div @click="buildValue" class="btn bg-info-fordone"><font-awesome-icon icon="fa fa-save" />
                                Buat
                                Tabel</div>
                        </div>
                    </div>
                </div>
                <Teleport to="body">
                    <ModalBs :ModalStatus="previewModalStatus" :modalSize="'modal-xl modal-dialog-scrollable'"
                        @close="previewModalStatus = false" :title="'Preview Tabel'">
                        <template #modalBody>
                            <div class="tabel-container mb-3">
                                <TabelPreview :rows="rowPreview" :columns="columnPreview"
                                    :turtahun="turtahunListFetched" />
                            </div>
                            <div class="text-center">
                                <label>Apakah Anda yakin tidak ada tabel dengan judul yang sama?</label>
                            </div>
                        </template>
                        <template #modalFunction>
                            <button id="" type="submit" class="btn btn-sm bg-success-fordone"
                                :disabled="form.processing" @click.prevent="submit">Simpan</button>
                        </template>
                    </ModalBs>
                    <ModalBs :-modal-status="errorModalStatus" @close="errorModalStatus = false"
                        :title="'Daftar Error'">
                        <template #modalBody>
                            <ul>
                                <li class="text-danger" v-for="(node, index) in page.props.errors" :key="index">
                                    {{ node }}
                                </li>
                            </ul>
                        </template>
                    </ModalBs>
                </Teleport>
            </form>
        </div>
        <!-- <div><br /></div> -->
    </GeneralLayout>
</template>
<style scoped>
.card-header {
    border-bottom-color: #3d3b8e;
    border-bottom-width: 3px;
}

.tabel-container {
    max-height: 500px;
    overflow-y: scroll;
    overflow-x: scroll;
}
</style>