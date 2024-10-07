<script setup>
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";
import { ref, computed, watch } from "vue";

const props = defineProps({
  countTabels: {
    type: Number,
    required: true,
  },
  data: {
    type: Array,
    required: true,
  },
  updateResult: {
    type: Boolean,
    required: true,
    default: false,
  },
  ArrayFilter: {
    type: Array,
    required: false,
    default: false,
  },
  pageNumber: {
    type: Number,
    required: false,
    default: 1,
  },
  resetData: {
    type: Boolean,
    required: true,
    default: false,
  },
});
const displayedData = ref([]);
const filter = ref(props.ArrayFilter);
const dinas = computed(() => getValueOfArray("id_dinas"));
const tahun = computed(() => getValueOfArray("tahun"));
// const loadInitialData = () => {
//     displayedData.value = props.data.slice(0, 20)
// }
const getValueOfArray = (key) => {
  const obj = filter.value.find((item) => item.key == key);
  return obj.valueFilter;
};
watch(
  () => props.data,
  (value) => {
    displayedData.value = value;
  }
);
watch(
  () => props.resetData,
  (value) => {
    if (value == true) {
      displayedData.value = props.data;
      emits("update:updateResetComponent", false);
    }
  }
);
watch(
  () => props.ArrayFilter,
  (value) => {
    filter.value = value;
  }
);
const emits = defineEmits([
  "update:updateResult",
  "update:updatePageNumber",
  "update:updateResetComponent",
]);
const loadMoreData = async (state) => {
  try {
    const response = await axios.get(route("home"), {
      params: {
        currentPage: props.pageNumber,
        paginated: 20,
        ArrayFilter: {
          tahun: tahun.value,
          kode: getValueOfArray("kode_wilayah"),
          dinas: dinas.value,
          subjek: getValueOfArray("id_subjek"),
          label: getValueOfArray("label"),
          category: getValueOfArray("category"),
        },
      },
    });
    let nextData = response.data.tabels.data;
    // let nextData = props.data.slice(displayedData.value.length, displayedData.value.length + 20)
    if (nextData.length) {
      displayedData.value = displayedData.value.concat(nextData);
      state.loaded();
      emits("update:updatePageNumber", props.pageNumber + 1);
    } else {
      nextData.length == 0
        ? emits("update:updateResult", true)
        : emits("update:updateResult", false);
      state.complete();
    }
  } catch (error) {
    console.error("Error Fetching Data :", error);
  }
};
// onMounted(() => {
//     loadInitialData()
// })
const infiniteData = +new Date();
</script>
<template>
  <div class="card-header py-3">
    <h6 class="ml-3 my-0 mr-0 font-weight-bold heading-card">
      Menampilkan : {{ countTabels }} tabel
    </h6>
  </div>
  <div class="card-body" id="list-tabel-card">
    <div
      v-for="(node, index) in displayedData"
      :key="index"
      class="d-flex flex-column mb-2 ml-3"
    >
      <Link
        :href="`/show?id=${node.id_statustables}&year=${node.tahun}`"
        class="text-red behave-a"
      >
        <span class="badge badge-pill badge-danger">{{ node.nomor }}</span> -
        {{ node.label }}, Tahun {{ node.tahun }}
      </Link>
      <small class="lead smalltext-homepage"
        >{{ node.nama_dinas }} | {{ node.nama_regions }}</small
      >
      <small class="lead smalltext-homepage" id="subjek-weight">
        Subjek : {{ node.nama_subjects }}</small
      >
      <small class="lead smalltext-homepage"
        >Terakhir diupdate : {{ node.status_updated }}</small
      >
    </div>
    <div class="text-center" v-if="updateResult">Tidak ada data lagi</div>
    <div class="text-center" v-else>
      <InfiniteLoading @infinite="loadMoreData" :identifier="infiniteData" />
    </div>
  </div>
</template>
<style scoped>
.behave-a {
  color: #ff0000;
  /* Red color */
  text-decoration: underline;
  /* Underline text */
  cursor: pointer;
}

.behave-a:hover {
  color: #cc0000;
  /* Darker red color on hover */
}
</style>
