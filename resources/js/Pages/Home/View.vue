<script setup>
import HomeLayout from "@/Layouts/HomeLayout.vue";
import { usePage, useForm, Link, Head } from "@inertiajs/vue3";
import { clickSortProperties } from "@/sortAttribute";
import ModalBs from "@/Components/ModalBs.vue";
import { downloadTabel, newDownload } from "@/download";
import { onMounted, ref, defineComponent, computed } from "vue";
import Multiselect from "@vueform/multiselect";
import SpinnerBorder from "@/Components/SpinnerBorder.vue";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";
import { Line } from "vue-chartjs";
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
);
defineComponent({
  Multiselect,
});
const page = usePage();

var mvObject = page.props.metavars;
var metavars = ref(mvObject);

const createModalStatus = ref(false);
const ViewMetavar = ref(false);
const metavarModalStatus = ref(false);
const downloadModalStatus = ref(false);
const triggerSpinner = ref(false);
const check = ref(false);
const downloadTitle = ref(null);
// const Rowee = ref(null);
// const Columnee = ref(null);
var columnComponents, rowComponents, turtahunComponents;
const form = useForm({
  id: "",
  id_tabel: "",
  r101: "",
  r102: "",
  r103: "",
  r104: "",
  r105: "",
  r106: "",
  r107: "",
  r108: "",
  r109: "",
  r110: "",
  r111: "",
  r112: "",
});
const chartData = ref([]);
const chartForm = useForm({
  column: null,
  row: null,
});
const columnForChart = page.props.columns.map((data) => ({
  label: data.label,
  value: data.id,
}));
const rowForChart = page.props.rows.map((data) => ({
  label: data.label,
  value: data.id ? data.id + "-" + data.id : "0-" + data.wilayah_fullcode,
}));
const findChart = ref(false);
const fetchChart = async () => {
  findChart.value = false;
  if (!chartForm.column || !chartForm.row) return;
  try {
    let wilayah_fullcode = false;
    const check = computed(() => page.props.rows?.[0]).value.wilayah_fullcode;
    if (check) wilayah_fullcode = true;
    const response = await axios.get("/chart-show", {
      params: {
        id_column: chartForm.column,
        id_row: chartForm.row,
        id_tabel: page.props.tabel.id_tabel,
        tahun: [page.props.tahun, ...page.props.tahuns],
        wilayah_fullcode: wilayah_fullcode,
      },
    });
    chartData.value = response.data;
    lineData.value.datasets = chartData.value;
    // console.log(lineData.value);
    findChart.value = true;
  } catch (error) {
    console.error("Error Fetching Data :", error);
  }
};
const lineData = ref({
  labels: [page.props.tahun, ...page.props.tahuns].sort((a, b) => a - b),
  datasets: chartData.value,
});
const lineOptions = {
  responsive: true,
  locale: "de-DE",
  maintainAspectRatio: true,
};
const yearDrop = ref({
  value: [],
  options: page.props.tahuns,
});
// onMounted(() => {
//   let RowTheadHeight = Rowee.value.offsetHeight;
//   let DatasTheadHeight = Columnee.value.offsetHeight;
//   if (RowTheadHeight > DatasTheadHeight) {
//     Columnee.value.style.height = `${RowTheadHeight}px`;
//   } else {
//     Rowee.value.style.height = `${DatasTheadHeight}px`;
//   }
// });
const getData = function (row, column, turtahun, fetchdata) {
  columnComponents = column.id;
  turtahunComponents = turtahun.id;
  if (row.id) {
    rowComponents = row.id;
  } else rowComponents = row.wilayah_fullcode;

  if (fetchdata) {
    const probablyTheData = fetchdata.find((x) => {
      return (
        (x.id_row === rowComponents || x.wilayah_fullcode === rowComponents) &&
        x.id_column === columnComponents &&
        x.id_turtahun === turtahunComponents
      );
    });
    return probablyTheData.value;
  }
  const probablyTheData = page.props.datacontents.find((x) => {
    return (
      (x.id_row === rowComponents || x.wilayah_fullcode === rowComponents) &&
      x.id_column === columnComponents &&
      x.id_turtahun === turtahunComponents
    );
  });
  return probablyTheData.value;
};
const toggleUpdateModal = async function (id) {
  createModalStatus.value = true;
  try {
    const response = await axios.get(route("metavar.fetchData", { id: id }));
    form.id = id;
    form.id_tabel = page.props.tabel.id_tabel;
    form.r101 = response.data.r101;
    form.r102 = response.data.r102;
    form.r103 = response.data.r103;
    form.r104 = response.data.r104;
    form.r105 = response.data.r105;
    form.r106 = response.data.r106;
    form.r107 = response.data.r107;
    form.r108 = response.data.r108;
    form.r109 = response.data.r109;
    form.r110 = response.data.r110;
    form.r111 = response.data.r111;
    form.r112 = response.data.r112;
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};
const showMetavar = () => {
  ViewMetavar.value = !ViewMetavar.value;
  metavarModalStatus.value = true;
};
const download = (titles) => {
  window.location.href = `/export-view/${page.props.tabel.id_tabel}/${titles}`;
  downloadModalStatus.value = false;
};
// const hiddenLabel = (value, index) => {
//   if (value.length > 30) {
//     indexExpanded.value[index] = false;
//     return value.substring(0, 30) + " ";
//   }
//   return value;
// };
// const toggleLabel = (index) => {
//   indexExpanded.value[index] = !indexExpanded.value[index];
// };
const indexExpanded = ref(Array(page.props.columns.length).fill(true));
page.props.columns.forEach((column, index) => {
  if (column.label.length > 30) indexExpanded.value[index] = false;
});
const HeaderColumn = (value, tahun) => {
  if (value == "Tahun") {
    if (tahun) return tahun;
    return page.props.tahun;
  } else return value;
};
const fetchedData = ref(null);
const statusFetch = ref(false);
const fetchData = async () => {
  try {
    triggerSpinner.value = true;
    const response = await axios.get(route("view.fetch"), {
      params: {
        current: page.props.tahun,
        tahun: yearDrop.value.value,
        id_tabel: page.props.tabel.id_tabel,
        id_statustabel: page.props.tabel.id_statustables,
      },
    });
    // console.log(response.data);
    fetchedData.value = response.data;
    statusFetch.value = true;
  } catch (error) {
    console.error("Error fetching data:", error);
  }
  triggerSpinner.value = false;
};
const reset = () => {
  triggerSpinner.value = true;
  setTimeout(() => {
    fetchedData.value = null;
    statusFetch.value = false;
    yearDrop.value.value = [];
    triggerSpinner.value = false;
  }, 300);
};
</script>
<template>
  <Head title="Lihat Tabel" />
  <SpinnerBorder v-if="triggerSpinner" />
  <HomeLayout>
    <div id="container-of-entry" class="pb-3">
      <div class="card">
        <div class="card-body">
          <h3 class="text-bold">
            <span id="nomor" class="badge">{{ page.props.tabels.nomor }}</span>
            {{ page.props.tabel.judul_tabel }}, Tahun {{ page.props.tahun }}
          </h3>
          <h4 class="my-0 d-flex">
            <span class="ml-auto text-right small" id="">
              Terakhir diupdate : {{ page.props.tabel.status_updated }}</span
            >
          </h4>
        </div>
      </div>
      <div class="overflow-x-scroll mb-2">
        <table class="table table-bordered" id="tabel-entry">
          <thead>
            <tr>
              <th class="text-center align-middle fixed-thead" rowspan="2">
                {{ page.props.row_label }}
              </th>
              <th
                class="text-center"
                :colspan="page.props.columns.length"
                v-for="(node, index) in page.props.turtahuns"
                :key="index"
              >
                {{ HeaderColumn(node.label) }}
              </th>
              <template v-if="statusFetch">
                <template
                  v-for="(fetchNode, fetchIndex) in fetchedData"
                  :key="fetchIndex"
                >
                  <th
                    class="text-center"
                    :colspan="fetchNode.columns.length"
                    v-for="(turtahun, turtahunIndex) in fetchNode.turtahun"
                    :key="turtahunIndex"
                  >
                    {{ HeaderColumn(turtahun.label, fetchNode.tahun) }}
                  </th>
                </template>
              </template>
            </tr>
            <tr>
              <template v-for="(node, index) in page.props.turtahuns" :key="index">
                <th
                  class="text-center align-middle not-fixed"
                  v-for="(node, index) in page.props.columns"
                  :key="index"
                >
                  {{ node.label }}
                </th>
              </template>
              <template v-if="statusFetch">
                <template v-for="(fetchNode, fetchIndex) in fetchedData">
                  <template
                    v-for="(turtahun, turtahunIndex) in fetchNode.turtahun"
                    :key="turtahunIndex"
                  >
                    <th
                      class="text-center align-middle not-fixed"
                      v-for="(columns, columnsIndex) in fetchNode.columns"
                      :key="columnsIndex"
                    >
                      {{ columns.label }}
                    </th>
                  </template>
                </template>
              </template>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(nodeRow, index) in page.props.rows" :key="index">
              <td class="fixed-column">{{ nodeRow.label }}</td>
              <template
                v-for="(nodeTurtahun, index) in page.props.turtahuns"
                :key="index"
              >
                <td
                  class="not-fixed text-center"
                  v-for="(nodeColumn, index) in page.props.columns"
                  :key="index"
                >
                  {{ getData(nodeRow, nodeColumn, nodeTurtahun) }}
                </td>
              </template>
              <template v-if="statusFetch">
                <template v-for="(fetchNode, fetchIndex) in fetchedData">
                  <template
                    v-for="(turtahun, turtahunIndex) in fetchNode.turtahun"
                    :key="turtahunIndex"
                  >
                    <td
                      class="not-fixed text-center"
                      v-for="(column, columnIndex) in fetchNode.columns"
                      :key="columnIndex"
                    >
                      {{ getData(nodeRow, column, turtahun, fetchNode.data) }}
                    </td>
                  </template>
                </template>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mb-2 d-flex">
        <div class="flex-grow-1"></div>
        <div class="mr-2 year">
          <Multiselect
            :options="yearDrop.options"
            v-model="yearDrop.value"
            placeholder="-- Tambah tahun lain yang tersedia --"
            mode="tags"
            :searchable="true"
          />
        </div>
        <button
          @click.prevent="fetchData()"
          type="submit"
          class="btn mr-2 bg-info-fordone"
        >
          <font-awesome-icon icon="fa-solid fa-plus" />
        </button>
        <button @click.prevent="reset" type="submit" class="btn bg-info-fordone">
          <font-awesome-icon icon="fa-solid fa-rotate-right" />
        </button>
      </div>
      <div class="card mb-2" v-if="page.props.catatans">
        <div class="card-header">
          <h5 class="mb-0 text-bold">CATATAN</h5>
        </div>
        <div class="card-body">
          {{ page.props.catatans }}
        </div>
      </div>
      <Teleport to="body">
        <ModalBs
          :ModalStatus="createModalStatus"
          @close="
            () => {
              createModalStatus = false;
              form.reset();
            }
          "
          :modalSize="'modal-xl modal-dialog-scrollable'"
        >
          <template #modalBody>
            <div class="form-group">
              <label for="r101">Nama Variabel</label>
              <input
                disabled
                class="form-control mb-3"
                id="r101"
                v-model="form.r101"
                placeholder="Isi nama variabel"
              />
              <label for="r102">Konsep</label>
              <input
                disabled
                class="form-control mb-3"
                id="r102"
                v-model="form.r102"
                placeholder="Isi konsep yang sesuai"
              />
              <label for="r103">Definisi</label>
              <textarea
                disabled
                class="form-control mb-3"
                id="r103"
                v-model="form.r103"
                placeholder="Isi definisi yang sesuai"
              ></textarea>
              <label for="r104">Referensi Pemilihan</label>
              <textarea
                disabled
                class="form-control mb-3"
                id="r104"
                v-model="form.r104"
                placeholder="Isi referensi pemilihan dari variabel"
              ></textarea>
              <label for="r105">Referensi Waktu</label>
              <input
                disabled
                class="form-control mb-3"
                id="r105"
                v-model="form.r105"
                placeholder="Isi referensi waktu dari variabel"
              />
              <label for="satuan">Satuan</label>
              <input
                disabled
                class="form-control mb-3"
                id="satuan"
                v-model="form.satuan"
                placeholder=""
              />
              <label for="r106">Alias</label>
              <input
                disabled
                class="form-control mb-3"
                id="r106"
                v-model="form.r106"
                placeholder="Isi alias dari variabel"
              />
              <label for="r107">Ukuran</label>
              <input
                disabled
                class="form-control mb-3"
                id="r107"
                v-model="form.r107"
                placeholder="Isi ukuran dari variabel"
              />
              <label for="r108">Tipe Data</label>
              <input
                disabled
                class="form-control mb-3"
                id="r108"
                v-model="form.r108"
                placeholder="Isi tipe data dari variabel"
              />
              <label for="r109">Klasifikasi Isian</label>
              <input
                disabled
                class="form-control mb-3"
                id="r109"
                v-model="form.r109"
                placeholder="Apakah variabel memiliki klasifikasi isian?"
              />
              <label for="r110">Aturan Validasi</label>
              <input
                disabled
                class="form-control mb-3"
                id="r110"
                v-model="form.r110"
                placeholder="Bagaimana aturan validasi untuk variabel?"
              />
              <label for="r111">Kalimat Pertanyaan</label>
              <input
                disabled
                class="form-control mb-3"
                id="r111"
                v-model="form.r111"
                placeholder="Bagaimana bentuk kalimat pertanyaan untuk variabel?"
              />
              <label for="r112">Apakah variabel dapat diakses?</label>
              <input disabled class="form-control mb-3" id="r111" v-model="form.r112" />
            </div>
          </template>
        </ModalBs>
        <ModalBs
          :-modal-status="downloadModalStatus"
          @close="
            () => {
              downloadModalStatus = false;
              downloadTitle = null;
            }
          "
          :title="'Download Data'"
        >
          <template #modalBody>
            <label>Masukkan Judul File</label>
            <input type="text" v-model="downloadTitle" class="form-control" />
          </template>
          <template #modalFunction>
            <button
              v-if="check"
              type="button"
              class="btn btn-sm bg-success-fordone"
              @click.prevent="newDownload('tabel-entry', downloadTitle)"
            >
              Simpan
            </button>
            <!-- <button
              v-if="check"
              type="button"
              class="btn btn-sm bg-success-fordone"
              @click.prevent="downloadTabel(downloadTitle)"
            >
              Simpan
            </button> -->
            <button
              v-else
              type="button"
              class="btn btn-sm bg-success-fordone"
              @click.prevent="download(downloadTitle)"
            >
              Simpan
            </button>
          </template>
        </ModalBs>
      </Teleport>
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0 text-bold">Metadata</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col">
              <p>Subjek</p>
            </div>
            <div class="col text-bold">{{ page.props.tabels.subject_label }}</div>
          </div>
          <div class="row">
            <div class="col">
              <p>Produsen Data</p>
            </div>
            <div class="col text-bold">{{ page.props.tabels.dinas_label }}</div>
          </div>
          <div class="row">
            <div class="col">
              <p>Wilayah Kerja</p>
            </div>
            <div class="col text-bold">{{ page.props.tabels.wilayah_label }}</div>
          </div>
          <div class="row">
            <div class="col">
              <p>Satuan Data</p>
            </div>
            <div class="col text-bold">{{ page.props.tabels.unit }}</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <div class="row">
            <h5 class="mb-0 col text-bold">Grafik</h5>
            <button
              v-if="page.props.graphAvailability"
              @click.prevent="fetchChart"
              type="submit"
              class="btn ml-auto mr-1 chartBtn bg-info-fordone"
            >
              <font-awesome-icon icon="fa-solid fa-square-pen" />
            </button>
            <button
              v-if="page.props.graphAvailability"
              @click.prevent="findChart = false"
              type="submit"
              class="btn chartBtn bg-info-fordone"
            >
              <font-awesome-icon icon="fa-solid fa-rotate-right" />
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row mb-4 justify-content-start" v-if="page.props.graphAvailability">
            <Multiselect
              class="col-lg-3 col-md-3 col-sm-12 mr-2 ml-0"
              :options="columnForChart"
              v-model="chartForm.column"
              placeholder="-- Pilih Kolom --"
              :searchable="true"
            />
            <Multiselect
              class="col-lg-5 col-md-5 col-sm-12 ml-1 mr-1"
              :options="rowForChart"
              v-model="chartForm.row"
              placeholder="-- Pilih Baris --"
              mode="tags"
              :searchable="true"
            />
          </div>
          <div v-else class="text-center">Grafik tidak tersedia untuk tabel ini</div>
          <div class="chart-container text-center">
            <Line v-if="findChart" :data="lineData" :options="lineOptions" />
          </div>
        </div>
      </div>
      <div class="metavar-container" v-if="ViewMetavar">
        <table
          v-if="metavars.length > 0"
          class="table table-sorted table-hover table-bordered table-search"
          ref="tabelListMetavar"
          id="tabel-user"
        >
          <thead>
            <tr class="bg-info-fordone">
              <th
                class="first-column"
                @click="clickSortProperties(page.props.metavars, 'number')"
              >
                No.
              </th>
              <th
                class="text-center"
                @click="clickSortProperties(page.props.metavars, 'r101')"
              >
                Nama Variabel
              </th>
              <th
                class="text-center tabel-width-15"
                @click="clickSortProperties(page.props.metavars, 'r102')"
              >
                Konsep
              </th>
              <th
                class="text-center tabel-width-20"
                @click="clickSortProperties(page.props.metavars, 'r103')"
              >
                Definisi
              </th>
              <th
                class="text-center tabel-width-20"
                @click="clickSortProperties(page.props.metavars, 'r104')"
              >
                Referensi Pemilihan
              </th>
              <th
                class="text-center"
                @click="clickSortProperties(page.props.metavars, 'satuan')"
              >
                Satuan
              </th>
              <th class="text-center deleted">Lihat Detail</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(node, index) in metavars" :key="node.id">
              <td>{{ node.number }}</td>
              <td>{{ node.r101 }}</td>
              <td>{{ node.r102 }}</td>
              <td>{{ node.r103 }}</td>
              <td>{{ node.r104 }}</td>
              <td>{{ node.satuan }}</td>
              <td class="text-center deleted">
                <a @click.prevent="toggleUpdateModal(node.id)" class="edit-pen"
                  ><font-awesome-icon icon="fa-solid fa-eye" title="Cek"
                /></a>
              </td>
            </tr>
          </tbody>
        </table>
        <Teleport v-else to="body">
          <ModalBs
            :-modal-status="metavarModalStatus"
            @close="metavarModalStatus = false"
          >
            <template #modalBody>
              <label>Data ini belum ada Metadata Variabelnya</label>
            </template>
          </ModalBs>
        </Teleport>
      </div>
      <div class="mb-2 d-flex">
        <div class="flex-grow-1">
          <Link :href="route('home')" class="btn btn-light border"
            ><font-awesome-icon icon="fas fa-chevron-left" />
            Kembali
          </Link>
        </div>
        <button
          class="btn bg-success-fordone mr-2"
          title="Download"
          @click="
            () => {
              downloadModalStatus = true;
              indexExpanded.fill(true);
              check = true;
            }
          "
        >
          <font-awesome-icon icon="fa-solid fa-circle-down" /> Download Data
        </button>
        <button
          class="btn bg-success-fordone mr-2"
          title="Download"
          @click="
            () => {
              downloadModalStatus = true;
              check = false;
            }
          "
        >
          <font-awesome-icon icon="fa-solid fa-circle-down" /> Download Metadata
        </button>
        <button @click="showMetavar" class="btn bg-info-fordone" id="showMetavar">
          <font-awesome-icon icon="fa-solid mr-1 fa-book-bookmark" /> Metadata Variabel
        </button>
      </div>
    </div>
  </HomeLayout>
</template>
<style scoped>
.year {
  width: 500px;
}
.chartBtn {
  width: 50px;
}
#container-of-entry {
  margin-right: 5%;
  margin-left: 5%;
  font-size: 13px;
}

.badge {
  cursor: pointer;
}

.bg-success-fordone {
  background-color: #1d845b;
  color: whitesmoke;
}

.bg-success-fordone:hover {
  background-color: #385d4e;
  color: whitesmoke;
}

.bg-info-fordone {
  background-color: #3d3b8e;
  color: whitesmoke;
}

.bg-info-fordone:hover {
  background-color: #434272;
  color: whitesmoke;
}

.table thead tr th {
  background-color: #3d3b8e;
  color: whitesmoke;
}

#RowTabel {
  table-layout: fixed;
  width: 400px;
  background: #f9fafc;
  border-right: 1px solid #e6eaf0;
  vertical-align: top;
}

#RowTabel thead th:first-child {
  width: 50px;
}

#imaginer {
  width: 400px;
}

#RowTabel thead,
#ColumnTabel thead {
  height: 120px;
  vertical-align: middle;
  padding: 0.1rem;
  overflow: auto;
}

#ColumnTabel tbody tr td {
  min-width: 180px;
}

#RowTabel tbody tr td,
#ColumnTabel tbody tr td {
  height: 50px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.table-data-wrapper {
  overflow-x: auto;
  vertical-align: top;
  width: calc(100% - 400px);
  padding: 0;
}

.table-data-wrapper table {
  border-left: 0;
}

.table-container {
  padding-left: 7.5px;
  padding-right: 7.5px;
}

tbody {
  background-color: whitesmoke;
}

#nomor {
  background-color: #3d3b8e;
  color: whitesmoke;
  border-radius: 1rem;
  cursor: auto;
}
.fixed-column {
  position: sticky;
  min-width: 400px;
  left: 0;
  background-color: white;
  color: black;
  z-index: 1;
  box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.2);
  border-right: 1px solid #ccc;
  border-left: 1px solid #ccc;
}
.fixed-thead {
  position: sticky;
  min-width: 400px;
  left: 0;
  background-color: #3d3b8e;
  color: whitesmoke;
  z-index: 1;
  box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.2);
  border-right: 1px solid #ccc;
  border-left: 1px solid #ccc;
}
.not-fixed {
  min-width: 250px;
}
</style>
