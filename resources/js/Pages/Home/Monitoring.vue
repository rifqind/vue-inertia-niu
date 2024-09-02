<script setup>
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import Multiselect from "@vueform/multiselect";
import SpinnerBorder from "@/Components/SpinnerBorder.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { defineComponent, watch, ref, computed } from "vue";
import axios from "axios";
import { GoDownload } from "@/download";
import ModalBs from "@/Components/ModalBs.vue";
import Pagination from "@/Components/Pagination.vue";

defineComponent({
  Multiselect,
});
const page = usePage();
// var mObject = page.props.this_monitoring
const monitoring = ref(page.props.this_monitoring.data);
const searchLabel = ref(null);
const triggerSpinner = ref(false);
const downloadModalStatus = ref(false);
const downloadTitle = ref(null);

var all = [{ label: "Pilih Semua", value: "all" }];
const yearDrop = ref({
  value: null,
  options: [...all, ...page.props.years],
});
const kabsDrop = ref({
  value: null,
  options: page.props.kabs,
});
const tabelMonitoring = ref(null);

const ArrayBigObjects = [{ key: "nama_dinas", valueFilter: searchLabel }];

const delayedFetchData = debounce(() => {
  fetchData();
});
watch(
  ArrayBigObjects.map((obj) => obj.valueFilter),
  function () {
    currentPage.value = 1;
    delayedFetchData();
  }
);

//new Pagination
const showItems = ref(10);
const currentPage = ref(1);

const updateShowItems = (value) => {
  showItems.value = value;
  fetchData();
};
const updateCurrentPage = (value) => {
  currentPage.value = value;
  fetchData();
};
const totalItems = ref(page.props.countData);
watch(
  () => page.props.countData,
  (value) => {
    totalItems.value = value;
  }
);
const paginatedData = computed(() => {
  return monitoring.value;
});
watch(
  () => page.props.this_monitoring,
  (value) => {
    monitoring.value = value;
  }
);
const orderAttribute = ref({
  before: null,
  label: null,
  value: "asc",
});
const clickToOrder = (value) => {
  orderAttribute.value.label = value;
  if (orderAttribute.value.before == null || orderAttribute.value.before == value) {
    if (orderAttribute.value.value == "asc") orderAttribute.value.value = "desc";
    else if (orderAttribute.value.value == "desc") orderAttribute.value.value = null;
    else orderAttribute.value.value = "asc";
  } else orderAttribute.value.value = "asc";
  orderAttribute.value.before = value;
  fetchData();
};
const fetchData = async () => {
  try {
    const response = await axios.get(route("home.monitoring"), {
      params: {
        currentPage: currentPage.value,
        paginated: showItems.value,
        ArrayFilter: {
          nama_dinas: searchLabel.value,
          years: yearDrop.value.value,
          wilayah: kabsDrop.value.value,
        },
        orderAttribute: orderAttribute.value,
      },
    });
    monitoring.value = response.data.this_monitoring.data;
    totalItems.value = response.data.countData;
  } catch (error) {
    console.error("Error fetching data: ", error);
  }
};
const downloadRoute = () => {
  try {
    const response =
      route("export-monitoring") +
      "?" +
      new URLSearchParams({
        nama_dinas: searchLabel.value,
        years: yearDrop.value.value,
        wilayah: kabsDrop.value.value,
      }).toString();
    window.location.href = response;
  } catch (error) {
    alert("Gagal Download Data");
  }
};
</script>
<template>
  <Head title="Monitoring" />
  <SpinnerBorder v-if="triggerSpinner" />
  <GeneralLayout>
    <div class="container-fluid">
      <div class="mb-2 d-flex flex-wrap align-items-center">
        <!-- Title -->
        <div class="h4 flex-grow-1 mb-2 mb-md-0">Monitoring Pengisian Tabel</div>

        <!-- Kabupaten/Kota Dropdown -->
        <div class="mr-2 wilayah mb-2 mb-md-0">
          <Multiselect
            :options="kabsDrop.options"
            v-model="kabsDrop.value"
            placeholder="-- Pilih Kabupaten/Kota --"
          />
        </div>

        <!-- Year Dropdown -->
        <div class="mr-2 year mb-2 mb-md-0">
          <Multiselect
            :options="yearDrop.options"
            v-model="yearDrop.value"
            placeholder="-- Pilih Tahun --"
          />
        </div>

        <!-- Search Button -->
        <button
          @click.prevent="fetchData()"
          type="submit"
          class="btn mr-2 bg-info-fordone mb-2 mb-md-0"
        >
          <font-awesome-icon icon="fa-solid fa-magnifying-glass" />
        </button>

        <!-- Download Button -->
        <button
          class="btn bg-success-fordone mr-2"
          title="Download"
          @click="downloadRoute()"
        >
          <font-awesome-icon icon="fa-solid fa-circle-down" /> Download
        </button>
      </div>
    </div>
    <div class="table-responsive-mobile">
      <table
        class="table table-hover table-bordered"
        id="tabel-monitoring"
        ref="tabelMonitoring"
      >
        <thead>
          <tr>
            <th class="align-middle text-center tabel-width-5">#</th>
            <th
              class="align-middle text-center th-order tabel-width-35"
              @click="clickToOrder('d.nama')"
            >
              Produsen Data
            </th>
            <th
              class="align-middle text-center th-order tabel-width-8"
              @click="clickToOrder('jumlah_satu')"
            >
              Status Tabel Baru
            </th>
            <th
              class="align-middle text-center th-order tabel-width-8"
              @click="clickToOrder('jumlah_dua')"
            >
              Status Proses Entri
            </th>
            <th
              class="align-middle text-center th-order tabel-width-8"
              @click="clickToOrder('jumlah_tiga')"
            >
              Status Diperiksa
            </th>
            <th
              class="align-middle text-center th-order tabel-width-8"
              @click="clickToOrder('jumlah_empat')"
            >
              Status Perbaikan
            </th>
            <th
              class="align-middle text-center th-order tabel-width-8"
              @click="clickToOrder('jumlah_lima')"
            >
              Status Final
            </th>
            <th
              class="align-middle text-center th-order tabel-width-8"
              @click="clickToOrder('jumlah_enam')"
            >
              Tabel Dihapus
            </th>
          </tr>
          <tr>
            <td></td>
            <td><input type="text" class="form-control" v-model="searchLabel" /></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </thead>
        <tbody>
          <tr
            v-if="monitoring.length > 0"
            v-for="(node, index) in paginatedData"
            :key="index"
          >
            <td>{{ index + 1 }}</td>
            <td>{{ node.nama_dinas }}</td>
            <td>{{ node.jumlah_satu }}</td>
            <td>{{ node.jumlah_dua }}</td>
            <td>{{ node.jumlah_tiga }}</td>
            <td>{{ node.jumlah_empat }}</td>
            <td>{{ node.jumlah_lima }}</td>
            <td>{{ node.jumlah_enam }}</td>
          </tr>
          <tr v-else>
            <td colspan="8" class="text-center">Tidak ada data</td>
          </tr>
        </tbody>
      </table>
    </div>
    <Pagination
      @update:currentPage="updateCurrentPage"
      @update:showItems="updateShowItems"
      :show-items="showItems"
      :total-items="totalItems"
      :current-page="currentPage"
      :current-show-items="paginatedData.length"
    />
    <Teleport to="body">
      <ModalBs
        :-modal-status="downloadModalStatus"
        @close="downloadModalStatus = false"
        :title="'Download Data'"
      >
        <template #modalBody>
          <label>Masukkan Judul File</label>
          <input type="text" v-model="downloadTitle" class="form-control" />
        </template>
        <template #modalFunction>
          <button
            type="button"
            class="btn btn-sm bg-success-fordone"
            @click.prevent="GoDownload('tabel-monitoring', downloadTitle)"
          >
            Simpan
          </button>
        </template>
      </ModalBs>
    </Teleport>
  </GeneralLayout>
</template>
<style scoped>
.year {
  width: 250px;
}
.wilayah {
  width: 400px;
}
.th-order {
  cursor: pointer;
}
</style>
