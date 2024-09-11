<script setup>
import { Head, usePage, useForm } from "@inertiajs/vue3";
import { Teleport, ref, watch } from "vue";
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import SpinnerBorder from "@/Components/SpinnerBorder.vue";
import ModalBs from "@/Components/ModalBs.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
import { GoDownload } from "@/download";
import Pagination from "@/Components/Pagination.vue";
import { computed } from "vue";
import * as XLSX from "xlsx";

const page = usePage();
var dataObject = page.props.data.data;
var data = ref(dataObject);
const createModalStatus = ref(false);
const deleteModalStatus = ref(false);
const toggleFlash = ref(false);
const searchLabel = ref(null);
const triggerSpinner = ref(false);
const dataFetched = ref(null);
const modalTitle = ref("Tambah Kelompok Kolom Baru");
const uploadModal = ref(false);
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

const form = useForm({
  id: null,
  label: null,
  _token: null,
});
const ArrayBigObjects = [{ key: "label", valueFilter: searchLabel }];
watch(
  ArrayBigObjects.map((obj) => obj.valueFilter),
  function () {
    // columnGroup.value = filteredColumns.value
    currentPage.value = 1;
    delayedFetchData();
  }
);
const delayedFetchData = debounce(() => {
  fetchData();
});

const toggleUpdateModal = function (id) {
  if (id) {
    fetchLabel(id).then(function () {
      modalTitle.value = "Update Kategori";
      triggerSpinner.value = false;
      createModalStatus.value = true;
    });
  }
};
const toggleDeleteModal = function (id) {
  deleteModalStatus.value = true;
  form.id = id;
};
const fetchLabel = async (id) => {
  //   console.log(id);
  try {
    const response = await axios.get("/tabel/kategori", {
      params: {
        isFetch: true,
        id: id,
      },
    });
    // console.log(response);
    dataFetched.value = response.data;
    form.id = dataFetched.value.id;
    form.label = dataFetched.value.label;
  } catch (error) {
    console.error("Error Fetching Data: ", error);
  }
};
//new Pagination
const currentPage = ref(1);
const showItems = ref(10);

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
  return data.value;
});
watch(
  () => page.props.data.data,
  (value) => {
    data.value = value;
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
    const response = await axios.get(route("tabel.kategori"), {
      params: {
        currentPage: currentPage.value,
        paginated: showItems.value,
        ArrayFilter: {
          label: searchLabel.value,
        },
        orderAttribute: orderAttribute.value,
      },
    });
    data.value = response.data.data.data;
    totalItems.value = response.data.countData;
  } catch (error) {
    console.error("Error fetching data: ", error);
  }
};

const submit = async function () {
  const response = await axios.get(route("token"));
  form._token = response.data;
  if (form.processing) return;
  form.post("/tabel/kategori", {
    onBefore: function () {
      triggerSpinner.value = true;
      createModalStatus.value = false;
      uploadModal.value = false;
    },
    onFinish: function () {
      triggerSpinner.value = false;
    },
    onSuccess: function () {
      if (flashObject) toggleFlash.value = true;
      form.reset();
      fetchData();
    },
    onError: function () {
      createModalStatus.value = true;
    },
  });
};

const deleteForm = async function () {
  const response = await axios.get(route("token"));
  form._token = response.data;
  if (form.processing) return;
  form.delete("/tabel/kategori", {
    onBefore: function () {
      triggerSpinner.value = true;
      deleteModalStatus.value = false;
    },
    onFinish: function () {
      triggerSpinner.value = false;
    },
    onSuccess: function () {
      if (flashObject) toggleFlash.value = true;
      form.reset();
      fetchData();
    },
    onError: function () {
      deleteModalStatus.value = true;
    },
  });
};
</script>
<template>
  <Head title="Daftar Kategori Tabel" />
  <SpinnerBorder v-if="triggerSpinner" />
  <GeneralLayout>
    <div class="container-fluid">
      <div class="mb-2 d-flex flex-wrap align-items-center">
        <div class="h4 mb-2 mb-md-0 flex-grow-1">Daftar Kategori Tabel</div>
        <a @click="createModalStatus = true" class="btn mb-2 mb-md-0 bg-info-fordone"
          ><font-awesome-icon icon="fa-solid fa-plus" /> Tambah Kategori Baru</a
        >
      </div>
    </div>
    <FlashMessage
      :toggleFlash="toggleFlash"
      @close="flashHandle"
      :flashObject="flashObject"
    />
    <div class="table-responsive-mobile">
      <table
        class="table table-hover table-bordered table-search"
        ref="tabelColumnGroup"
        id="tabel-kelompok-kolom"
      >
        <thead>
          <tr class="bg-info-fordone">
            <th class="first-column tabel-width-10">No.</th>
            <th
              class="text-center th-order tabel-width-70"
              @click="clickToOrder('label')"
            >
              Kategori
            </th>
            <th class="text-center deleted tabel-width-8">Edit</th>
            <th class="text-center deleted">Hapus</th>
          </tr>
          <tr class="">
            <td class="search-header"></td>
            <td class="search-header">
              <input
                v-model.trim="searchLabel"
                type="text"
                class="search-input form-control"
              />
            </td>
            <td class="search-header deleted"></td>
            <td class="search-header deleted"></td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="node in paginatedData" :key="node.id" v-if="data.length > 0">
            <td>{{ node.number }}</td>
            <td>{{ node.label }}</td>
            <td class="text-center deleted">
              <a @click.prevent="toggleUpdateModal(node.id)" class="edit-pen mx-1">
                <font-awesome-icon icon="fa-solid fa-pencil" title="Edit Pengguna" />
              </a>
            </td>
            <td class="text-center deleted">
              <a @click.prevent="toggleDeleteModal(node.id)" class="delete-trash">
                <font-awesome-icon
                  icon="fa-solid fa-trash-can"
                  class="icon-trash-color"
                />
              </a>
            </td>
          </tr>
          <tr v-else>
            <td class="text-center" colspan="4">Tidak ada data</td>
          </tr>
        </tbody>
      </table>
    </div>
    <Teleport to="body">
      <ModalBs
        :ModalStatus="uploadModal"
        @close="uploadModal = false"
        :title="'Tambah dengan Template'"
      >
        <template #modalBody>
          <div class="mb-3 row">
            <div class="col-6">
              <label>Download Template</label>
            </div>
            <div class="col">
              <button
                type="button"
                class="btn btn-sm bg-success-fordone"
                @click="downloadTemplate"
              >
                Download
              </button>
            </div>
          </div>
          <div class="mb-3">
            <input type="file" @change="handleUpload" class="form-control" />
          </div>
        </template>
        <template #modalFunction>
          <button
            id=""
            type="button"
            class="btn btn-sm bg-success-fordone"
            @click.prevent="submit"
          >
            Simpan
          </button>
        </template>
      </ModalBs>
      <ModalBs
        :ModalStatus="createModalStatus"
        @close="
          () => {
            createModalStatus = false;
            modalTitle = 'Tambah Kategori Tabel Baru';
            form.reset();
          }
        "
        :title="modalTitle"
      >
        <template #modalBody>
          <form>
            <div class="form-group">
              <label for="label">Nama Kategori</label>
              <input
                v-model="form.label"
                type="text"
                class="form-control"
                id="label"
                placeholder="Isi Nama Kategori"
              />
            </div>
          </form>
        </template>
        <template #modalFunction>
          <button
            id=""
            type="button"
            class="btn btn-sm bg-success-fordone"
            :disabled="form.processing"
            @click.prevent="submit"
          >
            Simpan
          </button>
        </template>
      </ModalBs>
      <ModalBs
        :ModalStatus="deleteModalStatus"
        @close="
          () => {
            deleteModalStatus = false;
            form.reset();
          }
        "
        :title="'Hapus Kelompok Kolom'"
      >
        <template v-slot:modalBody>
          <label>Apakah Anda yakin akan menghapus Kategori ini?</label>
        </template>
        <template v-slot:modalFunction>
          <button
            type="button"
            class="btn btn-sm badge-status-empat"
            :disabled="form.processing"
            @click.prevent="deleteForm"
          >
            Hapus
          </button>
        </template>
      </ModalBs>
    </Teleport>
    <Pagination
      v-if="data.length > 0"
      @update:currentPage="updateCurrentPage"
      @update:showItems="updateShowItems"
      :show-items="showItems"
      :total-items="totalItems"
      :current-page="currentPage"
      :current-show-items="paginatedData.length"
    />
  </GeneralLayout>
</template>
<style scoped>
.th-order {
  cursor: pointer;
}
</style>
