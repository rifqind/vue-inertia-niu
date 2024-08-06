<script setup>
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, Teleport, defineComponent } from "vue";
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import ModalBs from "@/Components/ModalBs.vue";
import Multiselect from "@vueform/multiselect";

defineComponent({
  Multiselect,
});
const page = usePage();
var apiGroup = page.props.api;
var apis = ref(apiGroup);
const createModalStatus = ref(false);
const kabs = page.props.kabs;

const paginatedData = computed(() => {
  return apis.value;
});
const kabsDrop = ref({
  value: null,
  options: kabs,
});
watch(
  () => page.props.api,
  (value) => {
    apis.value = value;
  }
);
const form = useForm({
  wilayah_fullcode: null,
  _token: null,
});
const submit = async () => {
  const response = await axios.get(route("token"));
  form._token = response.data;
  if (form.processing) return;
  form.post("/api/create", {
    onFinish: () => {
      createModalStatus.value = false;
    },
  });
};
</script>
<template>
  <Head title="Daftar API" />
  <GeneralLayout>
    <div class="container-fluid">
      <div class="mb-2 d-flex">
        <div class="h4 flex-grow-1">Daftar API terdaftar</div>
        <a @click="createModalStatus = true" class="btn bg-info-fordone"
          ><font-awesome-icon icon="fa-solid fa-plus" /> Tambah API Baru</a
        >
      </div>
    </div>
    <table
      class="table table-hover table-bordered table-search"
      ref="tabelColumns"
      id="tabel-kolom"
    >
      <thead>
        <tr class="bg-info-fordone">
          <th class="first-column th-order tabel-width-10">No.</th>
          <th class="text-center th-order tabel-width-30">Wilayah</th>
          <th class="text-center th-order tabel-width-30">Key</th>
          <th class="text-center deleted tabel-width-8">Edit</th>
          <th class="text-center deleted">Hapus</th>
        </tr>
        <tr class="">
          <td class="search-header"></td>
          <td class="search-header">
            <input type="text" class="search-input form-control" />
          </td>
          <td class="search-header"></td>
          <td class="search-header deleted"></td>
          <td class="search-header deleted"></td>
        </tr>
      </thead>
      <tbody>
        <tr v-for="api in paginatedData" :key="api.key" v-if="apis.length > 0">
          <td>{{ api.number }}</td>
          <td>{{ api.wilayah_fullcode }}</td>
          <td>{{ api.key }}</td>
          <td class="text-center deleted">
            <a @click.prevent="toggleUpdateModal(api.key)" class="edit-pen mx-1">
              <font-awesome-icon icon="fa-solid fa-pencil" title="Edit Pengguna" />
            </a>
          </td>
          <td class="text-center deleted">
            <a @click.prevent="toggleDeleteModal(api.key)" class="delete-trash">
              <font-awesome-icon icon="fa-solid fa-trash-can" class="icon-trash-color" />
            </a>
          </td>
        </tr>
        <tr v-else>
          <td colspan="4" class="text-center">Data Tidak Ada</td>
        </tr>
      </tbody>
    </table>
    <Teleport to="body">
      <ModalBs
        :ModalStatus="createModalStatus"
        @close="createModalStatus = false"
        :title="'Tambah Baru'"
      >
        <template #modalBody>
          <form>
            <div class="form-group">
              <div class="mb-3">
                <label for="wilayah_fullcode">Wilayah</label>
                <Multiselect
                  v-model="form.wilayah_fullcode"
                  :options="kabsDrop.options"
                  placeholder="-- Pilih Wilayah --"
                  :searchable="true"
                />
              </div>
            </div>
          </form>
        </template>
        <template #modalFunction>
          <button
            id=""
            type="button"
            class="btn btn-sm bg-success-fordone"
            @click.prevent="submit"
          >
            Simpan
          </button></template
        >
      </ModalBs>
    </Teleport>
  </GeneralLayout>
</template>
