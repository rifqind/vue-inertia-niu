<script setup>
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import Multiselect from "@vueform/multiselect";
import draggable from "vuedraggable";
import SpinnerBorder from "@/Components/SpinnerBorder.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
import { ref, defineComponent, computed, watch } from "vue";
import { Head, usePage, useForm, Link } from "@inertiajs/vue3";

defineComponent({
  Multiselect,
  draggable,
});
const page = usePage();
const subjects = page.props.subjects;
const dinas = page.props.dinas;
const triggerSpinner = ref(false);
const subjectDrop = ref({
  value: null,
  options: subjects,
});
const dinasDrop = ref({
  value: null,
  options: dinas,
});
const columnLeft = ref(null);
const columnRight = ref(null);
const columnChange = ref({
  value: [],
  options: [],
});
const columnTemp = ref([]);
const thisColumn = ref(page.props.columns);

const columnTransfer = ref({
  value: [],
  options: [],
});
const rowTransfer = ref({
  value: [],
  options: [],
});

const rowLeft = ref(null);
const rowRight = ref(null);
const rowChange = ref({
  value: [],
  options: [],
});
const rowTemp = ref([]);
const thisRow = ref(page.props.rows);

const columnDelete = ref([]);
const flashObject = ref(page.props.flash);
watch(
  () => page.props.flash,
  (value) => {
    flashObject.value = value;
  }
);
const flashHandle = () => {
  toggleFlash.value = false;
  flashObject.value = {
    message: null,
    error: null,
  };
};
const toggleFlash = ref(false);
const form = useForm({
  id: page.props.tabel.id,
  tabel: {
    nomor: page.props.tabel.nomor,
    label: page.props.tabel.label,
    unit: page.props.tabel.unit,
    id_dinas: page.props.tabel.id_dinas,
    id_subjek: page.props.tabel.id_subjek,
  },
  destroyer: {
    rows: [],
    columns: [],
  },
  lab: {
    tahun: [],
    newCol: [],
    newRow: [],
    delCol: [],
    delRow: [],
    labOrderCol: [],
    labOrderRow: [],
  },
  columnToDelete: [],
  columnToTransfer: [],
  rowToTransfer: [],
  setWilayah: null,
  transfer: null,
  year: [],
  _token: null,
});

const submit = async function () {
  const response = await axios.get(route("token"));
  form._token = response.data;
  if (form.processing) return;
  form.post(route("tabel.update"), {
    onBefore: function () {
      triggerSpinner.value = true;
    },
    onFinish: function () {
      triggerSpinner.value = false;
    },
    onError: function () {
      triggerSpinner.value = false;
    },
  });
};
const columns = computed(() => {
  let result;
  if (columnChange.value.value.length > 0) {
    result = thisColumn.value.filter((column) => {
      return !columnTemp.value.includes(column.value);
    });
  } else result = thisColumn.value;
  return result;
});
const rows = computed(() => {
  let result;
  if (rowChange.value.value.length > 0) {
    result = thisRow.value.filter((row) => {
      return !rowTemp.value.includes(row.value);
    });
  } else result = thisRow.value;
  return result;
});
const addColumnChange = () => {
  if (columnLeft.value && columnRight.value) {
    let columnLabel = thisColumn.value.filter((x) => x.value == columnLeft.value);
    let columnLabel2 = page.props.columnBase.filter((x) => x.value == columnRight.value);
    let theArray = columnLeft.value + "->" + columnRight.value;
    let arrayLabel = columnLabel[0].label + " -> " + columnLabel2[0].label;
    let arrayValue = theArray;
    columnChange.value.options.push({
      value: arrayValue,
      label: arrayLabel,
    });
    columnChange.value.value.push(arrayValue);
    columnTemp.value.push(columnLeft.value);
    columnLeft.value = null;
    columnRight.value = null;
  }
};
const addRowChange = () => {
  if (rowLeft.value && rowRight.value) {
    let rowLabel = thisRow.value.filter((x) => x.value == rowLeft.value);
    let rowLabel2 = page.props.rowBase.filter((x) => x.value == rowRight.value);
    let theArray = rowLeft.value + "->" + rowRight.value;
    let arrayLabel = rowLabel[0].label + " -> " + rowLabel2[0].label;
    let arrayValue = theArray;
    rowChange.value.options.push({ value: arrayValue, label: arrayLabel });
    rowChange.value.value.push(arrayValue);
    rowTemp.value.push(rowLeft.value);
    rowLeft.value = null;
    rowRight.value = null;
  }
};
const addColumnDelete = () => {
  if (columnLeft.value) {
    let columnLabel = thisColumn.value.filter((x) => x.value == columnLeft.value);
    columnDelete.value.push({
      value: columnLeft.value,
      label: columnLabel[0].label,
    });
    form.columnToDelete.push(columnLeft.value);
    columnLeft.value = null;
  }
};
const addTransferColumn = () => {
  if (columnLeft.value && columnRight.value) {
    let columnLabel = thisColumn.value.filter((x) => x.value == columnLeft.value);
    let columnLabel2 = yearFor.value.filter((x) => x.value == columnRight.value);
    let theArray = columnLeft.value + "->" + columnRight.value;
    let arrayLabel = columnLabel[0].label + " -> " + columnLabel2[0].label;
    let arrayValue = theArray;
    columnTransfer.value.options.push({
      value: arrayValue,
      label: arrayLabel,
    });
    columnTransfer.value.value.push(arrayValue);
    columnLeft.value = null;
    columnRight.value = null;
  }
};
const addTransferRow = () => {
  if (rowLeft.value && columnRight.value) {
    let columnLabel = rows.value.filter((x) => x.value == rowLeft.value);
    let columnLabel2 = yearFor.value.filter((x) => x.value == columnRight.value);
    let theArray = rowLeft.value + "->" + columnRight.value;
    let arrayLabel = columnLabel[0].label + " -> " + columnLabel2[0].label;
    let arrayValue = theArray;
    rowTransfer.value.options.push({
      value: arrayValue,
      label: arrayLabel,
    });
    rowTransfer.value.value.push(arrayValue);
    columnLeft.value = null;
    columnRight.value = null;
  }
};
const rowTransporse = ref(null);
const columnTransporsed = ref(null);
const rowListForTransporse = ref([]);
const rowTransporseNext = ref(null);
const columnTransporsedNext = ref(null);
const addRowToColumn = () => {
  if (rowTransporse.value && columnTransporsed.value) {
    let rowTransLabel = page.props.transporseRow.filter(
      (x) => x.value == rowTransporse.value
    );
    let columnTransLabel = page.props.transporseColumn.filter(
      (x) => x.value == columnTransporsed.value
    );
    let theArray = rowTransporse.value + "->" + columnTransporsed.value;
    let arrayLabel = rowTransLabel[0].label + " -> " + columnTransLabel[0].label;
    let arrayValue = theArray;
    rowListForTransporse.value.push({
      value: arrayValue,
      label: arrayLabel,
    });
    rowTransporse.value = null;
    columnTransporsed.value = null;
  }
};
const changeStructure = async () => {
  const response = await axios.get(route("token"));
  form._token = response.data;
  form.destroyer.columns = columnChange.value.value;
  form.destroyer.rows = rowChange.value.value;
  form.columnToTransfer = columnTransfer.value.value;
  form.rowToTransfer = rowTransfer.value.value;
  if (form.processing) return;
  form.post(route("tabel.changeStructure"), {
    onBefore: function () {
      triggerSpinner.value = true;
    },
    onFinish: function () {
      triggerSpinner.value = false;
      form.reset();
      columnChange.value.value = [];
      columnChange.value.options = [];
      rowChange.value.value = [];
      rowChange.value.options = [];
      columnDelete.value = [];
    },
    onError: function () {
      triggerSpinner.value = false;
    },
  });
};
watch(
  () => page.props.columns,
  (value) => {
    thisColumn.value = value;
  }
);
watch(
  () => page.props.rows,
  (value) => {
    thisRow.value = value;
  }
);
const currentYear = new Date().getFullYear();
const years = Array.from({ length: 11 }, (_, index) => currentYear - index);
const yearDrop = ref({
  value: null,
  options: [],
});
yearDrop.value.options = years.map((year) => ({
  label: year.toString(),
  value: year.toString(),
}));
const yearFor = computed(() => {
  return form.year.map((x) => {
    return {
      label: x,
      value: x,
    };
  });
});
const labDelete = ref(false);
const labFetchedData = ref({
  column: null,
  row: null,
});
const labFetch = async (value) => {
  if (value.length > 0) {
    labDelete.value = !labDelete.value;
    try {
      const response = await axios.get("/labFetch", {
        params: {
          id_tabel: page.props.tabel.id,
          tahun: form.lab.tahun,
        },
      });
      labFetchedData.value.column = response.data.column;
      labFetchedData.value.row = response.data.row;
    } catch (error) {
      console.error(error.message);
    }
  }
};
const confirmation = ref(false);
const dataLayer = ref({
  column: [],
  row: [],
});

const buildOrder = async (value) => {
  if (value.length === 0) return;
  confirmation.value = !confirmation.value;
  try {
    const response = await axios.get("/labFetch", {
      params: {
        id_tabel: page.props.tabel.id,
        tahun: form.lab.tahun,
      },
    });
    const { column: fetchedColumns, row: fetchedRows } = response.data;
    // Update the labOrder arrays
    form.lab.labOrderCol = fetchedColumns;
    form.lab.labOrderRow = fetchedRows;
    // Filter and update dataLayer
    if (form.lab.newCol.length > 0) {
      dataLayer.value.column = page.props.columnBase.filter((x) =>
        form.lab.newCol.includes(x.value)
      );
    }
    if (form.lab.newRow.length > 0) {
      dataLayer.value.row = page.props.rowBase.filter((x) =>
        form.lab.newRow.includes(x.value)
      );
    }
    // Remove deleted columns/rows
    if (form.lab.delCol.length > 0) {
      form.lab.labOrderCol = form.lab.labOrderCol.filter(
        (x) => !form.lab.delCol.includes(x.value)
      );
    }
    if (form.lab.delRow.length > 0) {
      form.lab.labOrderRow = form.lab.labOrderRow.filter(
        (x) => !form.lab.delRow.includes(x.value)
      );
    }
    // Merge new columns/rows with fetched ones
    form.lab.labOrderCol = [...form.lab.labOrderCol, ...dataLayer.value.column];
    form.lab.labOrderRow = [...form.lab.labOrderRow, ...dataLayer.value.row];
    dataLayer.value.column = [];
    dataLayer.value.row = [];
  } catch (error) {
    console.error("Error fetching lab data:", error.message);
  }
};

const confirmOrder = ref({
  column: null,
  row: null,
});
const labSubmit = async () => {
  const response = await axios.get(route("token"));
  form._token = response.data;
  if (form.processing) return;
  form.post(route("tabel.lab"), {
    onBefore: function () {
      triggerSpinner.value = true;
    },
    onFinish: function () {
      triggerSpinner.value = false;
    },
    onSuccess: function () {
      if (flashObject) toggleFlash.value = true;
      form.reset();
      confirmation.value = false;
      labDelete.value = false;
    },
    onError: function () {
      triggerSpinner.value = false;
    },
  });
};
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
      <FlashMessage
        :toggleFlash="toggleFlash"
        @close="flashHandle"
        :flashObject="flashObject"
      />
      <form @submit.prevent="submit" id="form-create-tabel">
        <div class="form-group">
          <div class="card mb-3">
            <div class="card-header">
              <label class="h5 mb-0">Deskripsi Umum</label>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label for="dinas">Produsen Data</label>
                <Multiselect
                  v-model="form.tabel.id_dinas"
                  :options="dinasDrop.options"
                  placeholder="-- Pilih Produsen Data --"
                  :searchable="true"
                />
                <div class="text-danger text-left" v-if="true" id="error-dinas"></div>
              </div>
              <div class="mb-3">
                <label for="nomor">Nomor Tabel</label>
                <input
                  v-model="form.tabel.nomor"
                  type="text"
                  id="nomor"
                  class="form-control"
                  placeholder="1.1.1"
                />
                <div class="text-danger text-left" v-if="true" id="error-nomor"></div>
              </div>
              <div class="mb-3">
                <label for="judul">Judul Tabel</label>
                <input
                  v-model="form.tabel.label"
                  type="text"
                  id="judul"
                  class="form-control"
                  placeholder="Isikan judul tabel"
                />
                <div class="text-danger text-left" v-if="true" id="error-judul"></div>
              </div>
              <div class="mb-3">
                <label for="subjek">Subjek Tabel</label>
                <Multiselect
                  v-model="form.tabel.id_subjek"
                  :options="subjectDrop.options"
                  placeholder="-- Pilih Subjek --"
                  :searchable="true"
                />
                <div class="text-danger text-left" v-if="true" id="error-subjek"></div>
              </div>
              <div class="mb-3">
                <label for="unit">Satuan/Unit Data</label>
                <input
                  v-model="form.tabel.unit"
                  type="text"
                  id="unit"
                  class="form-control"
                  placeholder="Isikan satuan/unit data"
                />
                <div class="text-danger text-left" v-if="true" id="error-unit"></div>
              </div>
            </div>
          </div>
          <div class="card mb-3">
            <div class="card-header">
              <label class="h5 mb-0">Laboratorium Tabel</label>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label>Pilih Tahun :</label>
                <Multiselect
                  v-model="form.lab.tahun"
                  :searchable="true"
                  mode="tags"
                  :options="page.props.availableYear"
                  placeholder="-- Pilih Tahun --"
                />
              </div>
              <div class="mb-3">
                <label>Menambah Kolom Baru :</label>
                <Multiselect
                  v-model="form.lab.newCol"
                  :options="page.props.columnBase"
                  mode="tags"
                  :searchable="true"
                  placeholder="-- Pilih Kolom Tambahan --"
                />
              </div>
              <div class="mb-3">
                <label>Menambah Baris Baru :</label>
                <Multiselect
                  v-model="form.lab.newRow"
                  :options="page.props.rowBase"
                  mode="tags"
                  :searchable="true"
                  placeholder="-- Pilih Baris Tambahan --"
                />
              </div>
              <div class="mb-3">
                <button
                  type="button"
                  class="btn btn-sm bg-success-fordone mr-2"
                  @click="labFetch(form.lab.tahun)"
                >
                  Hapus Baris/Kolom?
                </button>
              </div>
              <template v-if="labDelete">
                <div class="mb-3">
                  <label>Hapus Kolom :</label>
                  <Multiselect
                    v-model="form.lab.delCol"
                    :options="labFetchedData.column"
                    mode="tags"
                    :searchable="true"
                    placeholder="-- Pilih Kolom --"
                  />
                </div>
                <div class="mb-3">
                  <label>Hapus Baris :</label>
                  <Multiselect
                    v-model="form.lab.delRow"
                    :options="labFetchedData.row"
                    mode="tags"
                    :searchable="true"
                    placeholder="-- Pilih Baris --"
                  />
                </div>
              </template>
              <div class="mb-3">
                <button
                  type="button"
                  class="btn btn-sm bg-success-fordone"
                  @click="buildOrder(form.lab.tahun)"
                >
                  Lanjut >>>
                </button>
              </div>
              <template v-if="confirmation">
                <div class="mb-3">
                  <label>Hasil Kolom :</label>
                  <table class="table table-hover table-bordered">
                    <thead>
                      <tr>
                        <th>Daftar Kolom</th>
                      </tr>
                    </thead>
                    <draggable
                      v-model="form.lab.labOrderCol"
                      tag="tbody"
                      item-key="label"
                    >
                      <template #item="{ element }">
                        <tr>
                          <td>{{ element.label }}</td>
                        </tr>
                      </template>
                    </draggable>
                  </table>
                </div>
                <div class="mb-3">
                  <label>Kolom Sudah Urut?</label>
                  <Multiselect
                    v-model="confirmOrder.column"
                    :options="[
                      { label: 'Ya', value: '1' },
                      { label: 'Tidak', value: '2' },
                    ]"
                    placeholder="-- Konfirmasi --"
                  />
                </div>
                <div class="mb-3">
                  <label>Hasil Baris :</label>
                  <table class="table table-hover table-bordered">
                    <thead>
                      <tr>
                        <th>Daftar Baris</th>
                      </tr>
                    </thead>
                    <draggable
                      v-model="form.lab.labOrderRow"
                      tag="tbody"
                      item-key="label"
                    >
                      <template #item="{ element }">
                        <tr>
                          <td>{{ element.label }}</td>
                        </tr>
                      </template>
                    </draggable>
                  </table>
                </div>
                <div class="mb-3">
                  <label>Baris Sudah Urut?</label>
                  <Multiselect
                    v-model="confirmOrder.row"
                    :options="[
                      { label: 'Ya', value: '1' },
                      { label: 'Tidak', value: '2' },
                    ]"
                    placeholder="-- Konfirmasi --"
                  />
                </div>
                <div
                  v-if="confirmOrder.column != 1 || confirmOrder.row != 1"
                  class="text-danger mb-3"
                >
                  Perubahan belum dikonfirmasi!!!
                </div>
                <div class="mb-3">
                  <button
                    v-if="confirmOrder.column == 1 || confirmOrder.row == 1"
                    @click.prevent="labSubmit"
                    type="button"
                    class="btn btn-sm bg-success-fordone"
                  >
                    Simpan
                  </button>
                </div>
              </template>
            </div>
          </div>
          <div class="card" v-if="page.props.auth.user.username == 'niu'">
            <div class="card-header">
              <label class="h5 mb-0">Transfer Column to Another Table</label>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label>Pilih Tabel Tujuan :</label>
                <Multiselect
                  v-model="form.transfer"
                  :searchable="true"
                  :options="page.props.tabelList"
                  placeholder="-- Pilih Tabel --"
                />
              </div>
              <div class="mb-3">
                <button
                  @click.prevent="changeStructure"
                  type="button"
                  class="btn btn-sm bg-success-fordone"
                >
                  Simpan
                </button>
              </div>
            </div>
          </div>
          <div class="card" v-if="page.props.auth.user.username == 'niu'">
            <div class="card-header">
              <label class="h5 mb-0d">Transfer Column to Another Year</label>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label>Daftar Tahun</label>
                <Multiselect
                  v-model="form.year"
                  mode="tags"
                  :searchable="true"
                  :options="yearDrop.options"
                  :placeholder="'-- Daftar Tahun --'"
                />
              </div>
              <div class="mb-3">
                <label>Daftar Transfer Kolom</label>
                <Multiselect
                  v-model="columnTransfer.value"
                  mode="tags"
                  :options="columnTransfer.options"
                  :placeholder="'-- Daftar Transfer --'"
                />
              </div>
              <div class="mb-3 row">
                <div class="col">
                  <label for="column-groups">Daftar Kolom</label>
                  <Multiselect
                    v-model="columnLeft"
                    :options="thisColumn"
                    :searchable="true"
                    placeholder="-- Pilih Kolom --"
                  />
                </div>
                <div class="col">
                  <label for="column-groups">Tabel Tahun Pengganti</label>
                  <Multiselect
                    v-model="columnRight"
                    :options="yearFor"
                    :searchable="true"
                    placeholder="-- Pilih Tabel Tahun --"
                  />
                </div>
                <div class="col-1">
                  <label><br /></label>
                  <button
                    @click.prevent="addTransferColumn"
                    type="button"
                    class="btn btn-sm bg-success-fordone"
                  >
                    Tambah
                  </button>
                </div>
              </div>
              <div class="mb-3">
                <label>Daftar Transfer Baris</label>
                <Multiselect
                  v-model="rowTransfer.value"
                  mode="tags"
                  :options="rowTransfer.options"
                  :placeholder="'-- Daftar Transfer --'"
                />
              </div>
              <div class="mb-3 row">
                <div class="col">
                  <label for="column-groups">Daftar Baris</label>
                  <Multiselect
                    v-model="rowLeft"
                    :options="rows"
                    :searchable="true"
                    placeholder="-- Pilih Baris --"
                  />
                </div>
                <div class="col">
                  <label for="column-groups">Tabel Tahun Pengganti</label>
                  <Multiselect
                    v-model="columnRight"
                    :options="yearFor"
                    :searchable="true"
                    placeholder="-- Pilih Tabel Tahun --"
                  />
                </div>
                <div class="col-1">
                  <label><br /></label>
                  <button
                    @click.prevent="addTransferRow"
                    type="button"
                    class="btn btn-sm bg-success-fordone"
                  >
                    Tambah
                  </button>
                </div>
              </div>
              <div class="mb-3">
                <button
                  @click.prevent="changeStructure"
                  type="button"
                  class="btn btn-sm bg-success-fordone"
                >
                  Simpan
                </button>
              </div>
            </div>
          </div>
          <div class="card" v-if="page.props.auth.user.username == 'niu'">
            <div class="card-header">
              <label class="h5 mb-0">The One Who Destroy The Database</label>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label>Daftar Perubahan di Kolom</label>
                <Multiselect
                  v-model="columnChange.value"
                  mode="tags"
                  :options="columnChange.options"
                  :placeholder="'-- Daftar Perubahan di Kolom --'"
                />
              </div>
              <div class="mb-3 row">
                <div class="col">
                  <label for="column-groups">Daftar Kolom</label>
                  <Multiselect
                    v-model="columnLeft"
                    :options="columns"
                    :searchable="true"
                    placeholder="-- Pilih Kolom --"
                  />
                </div>
                <div class="col">
                  <label for="column-groups">Kolom Pengganti</label>
                  <Multiselect
                    v-model="columnRight"
                    :options="page.props.columnBase"
                    :searchable="true"
                    placeholder="-- Pilih Kolom Pengganti --"
                  />
                </div>
                <div class="col-1">
                  <label><br /></label>
                  <button
                    @click.prevent="addColumnChange"
                    type="button"
                    class="btn btn-sm bg-success-fordone"
                  >
                    Tambah
                  </button>
                </div>
              </div>
              <div class="mb-3">
                <label>Daftar Perubahan di Baris</label>
                <Multiselect
                  v-model="rowChange.value"
                  mode="tags"
                  :options="rowChange.options"
                  :placeholder="'-- Daftar Perubahan di Baris --'"
                />
              </div>
              <div class="mb-3 row">
                <div class="col">
                  <label for="column-groups">Daftar Baris</label>
                  <Multiselect
                    v-model="rowLeft"
                    :options="rows"
                    :searchable="true"
                    placeholder="-- Pilih Baris --"
                  />
                </div>
                <div class="col">
                  <label for="column-groups">Baris Pengganti</label>
                  <Multiselect
                    v-model="rowRight"
                    :options="page.props.rowBase"
                    :searchable="true"
                    placeholder="-- Pilih Baris Pengganti --"
                  />
                </div>
                <div class="col-1">
                  <label><br /></label>
                  <button
                    @click.prevent="addRowChange"
                    type="button"
                    class="btn btn-sm bg-success-fordone"
                  >
                    Tambah
                  </button>
                </div>
              </div>
              <div class="mb-3">
                <label>Ubah Baris menjadi Wilayah</label>
                <Multiselect
                  v-model="form.setWilayah"
                  :options="[
                    { value: 1, label: 'Ya' },
                    { value: 0, label: 'Tidak' },
                  ]"
                  :placeholder="'-- Daftar Perubahan di Baris --'"
                />
              </div>
              <div class="mb-3">
                <button
                  @click.prevent="changeStructure"
                  type="button"
                  class="btn btn-sm bg-success-fordone"
                >
                  Simpan
                </button>
              </div>
            </div>
          </div>
          <div class="card" v-if="page.props.auth.user.username == 'niu'">
            <div class="card-header">
              <label class="h5 mb-0">Transporse</label>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label>Daftar Perubahan di Baris</label>
                <Multiselect
                  v-model="rowChange.value"
                  mode="tags"
                  :options="rowChange.options"
                  :placeholder="'-- Daftar Perubahan di Baris --'"
                />
              </div>
              <div class="mb-3 row">
                <div class="col">
                  <label for="column-groups">Daftar Baris</label>
                  <Multiselect
                    v-model="rowTransporse"
                    :options="page.props.transporseRow"
                    :searchable="true"
                    placeholder="-- Pilih Baris --"
                  />
                </div>
                <div class="col">
                  <label for="column-groups">Pilih Kolom</label>
                  <Multiselect
                    v-model="columnTransporsed"
                    :options="page.props.transporseColumn"
                    :searchable="true"
                    placeholder="-- Pilih Kolom --"
                  />
                </div>
                <div class="col-1">
                  <label><br /></label>
                  <button
                    @click.prevent="addRowToColumn"
                    type="button"
                    class="btn btn-sm bg-success-fordone"
                  >
                    Tambah
                  </button>
                </div>
              </div>
              <div class="mb-3 row">
                <div class="col">
                  <label for="column-groups">Daftar Baris Transporse Kolom</label>
                  <Multiselect
                    v-model="rowTransporseNext"
                    :options="rowListForTransporse"
                    :searchable="true"
                    placeholder="-- Pilih Baris --"
                  />
                </div>
                <div class="col">
                  <label for="column-groups">Pilih Baris</label>
                  <Multiselect
                    v-model="columnTransporsedNext"
                    :options="page.props.transporseRow"
                    :searchable="true"
                    placeholder="-- Pilih Baris --"
                  />
                </div>
                <div class="col-1">
                  <label><br /></label>
                  <button
                    @click.prevent="addRowChange"
                    type="button"
                    class="btn btn-sm bg-success-fordone"
                  >
                    Tambah
                  </button>
                </div>
              </div>
              <div class="mb-3">
                <button
                  @click.prevent="changeStructure"
                  type="button"
                  class="btn btn-sm bg-success-fordone"
                >
                  Simpan
                </button>
              </div>
            </div>
          </div>
          <div class="card" v-if="page.props.auth.user.username == 'niu'">
            <div class="card-header">
              <label class="h5 mb-0">Hapus Kolom Dulu</label>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label>Daftar Kolom yang Dihapus</label>
                <Multiselect
                  v-model="form.columnToDelete"
                  mode="tags"
                  :options="columnDelete"
                  :placeholder="'-- Daftar Kolom Dihapus --'"
                />
              </div>
              <div class="mb-3 row">
                <div class="col">
                  <label for="column-groups">Daftar Kolom</label>
                  <Multiselect
                    v-model="columnLeft"
                    :options="thisColumn"
                    :searchable="true"
                    placeholder="-- Pilih Kolom --"
                  />
                </div>
                <div class="col-1">
                  <label><br /></label>
                  <button
                    @click.prevent="addColumnDelete"
                    type="button"
                    class="btn btn-sm bg-success-fordone"
                  >
                    Tambah
                  </button>
                </div>
              </div>
              <div class="mb-3">
                <button
                  @click.prevent="changeStructure"
                  type="button"
                  class="btn btn-sm bg-success-fordone"
                >
                  Simpan
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
      <div class="mb-2 d-flex">
        <div class="flex-grow-1">
          <Link :href="route('tabel.master')" class="btn btn-light border"
            ><font-awesome-icon icon="fas fa-chevron-left" />
            Kembali
          </Link>
        </div>
        <a @click.prevent="submit" class="btn bg-success-fordone"
          ><font-awesome-icon icon="fa-solid fa-save" /> Simpan</a
        >
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
