<script setup>
import { watch, computed } from "vue";
const props = defineProps({
  showItems: {
    type: Number,
    require: true,
  },
  totalItems: {
    type: Number,
    require: true,
  },
  currentPage: {
    type: Number,
    require: true,
  },
  adjustPage: {
    type: Array,
    default: [10, 15, 20, 50],
  },
  currentShowItems: {
    type: Number,
    require: true,
  },
});
const totalPages = computed(() => {
  return Math.ceil(props.totalItems / props.showItems);
});
const emits = defineEmits(["update:showItems", "update:currentPage"]);
const changePage = (node, index) => {
  let current = props.currentPage;
  let lasted = visiblePages.value.length;
  // console.log(index, lasted)
  if (node < 1 || node > totalPages.value) return;
  if (node == "...") {
    if (index == 0) {
      node = Math.ceil(current / 2);
    } else if (index + 1 == lasted) {
      let tempMid = current + 4;
      node = Math.ceil((current + tempMid) / 2);
    } else node = Math.ceil(totalPages.value / 2);
  }
  emits("update:currentPage", node);
};
const updateShowItems = (event) => {
  emits("update:showItems", parseInt(event.target.value, 10));
};
watch(
  () => props.currentPage,
  (newPage) => {
    if (newPage > totalPages) emits("update:currentPage", totalPages);
  }
);
const visiblePages = computed(() => {
  let pages = [];
  let total = totalPages.value;
  let current = props.currentPage;
  if (total <= 6) {
    for (let i = 1; i <= total; i++) {
      pages.push(i);
    }
  } else {
    let threeMax = total - 2;
    if (current <= 3 || current >= threeMax) {
      for (let i = 1; i <= 3; i++) pages.push(i);
      if (current <= 3) {
        if (current == 3) {
          pages.push(4);
        }
        pages.push("...");
      }
      if (current > 3 && current < threeMax) {
        if (!(current - 1 == 3)) {
          if (!(current - 2 == 3)) pages.push("...");
          pages.push(current - 1);
        }
        pages.push(current);
        if (!(current + 1 == threeMax)) {
          pages.push(current + 1);
          if (!(current + 2 == threeMax)) pages.push("...");
        }
      }
      if (current >= threeMax) {
        if (current == threeMax) {
          pages.push("...");
          pages.push(threeMax - 1);
        } else {
          pages.push("...");
        }
      }
      for (let i = threeMax; i <= total; i++) pages.push(i);
    } else {
      let firstPart = current - 2;
      let lastPart = current + 2;
      pages.push("...");
      for (let i = firstPart; i <= lastPart; i++) pages.push(i);
      pages.push("...");
    }
  }
  return pages;
});
</script>
<template>
  <div class="pagination-wrapper mt-2">
    <div class="d-flex align-items-center mb-3">
      <div id="statusText" ref="statusText" class="mr-3">
        Menampilkan <span id="showItems">{{ currentShowItems }}</span> dari
        <span id="showTotal">{{ totalItems }}</span>
      </div>
      <div class="form-group mb-0">
        <select
          class="form-control"
          ref="maxRows"
          @change="updateShowItems"
          name="state"
          id="maxRows"
        >
          <option v-for="(node, index) in adjustPage" :key="index" :value="node">
            {{ node }}
          </option>
        </select>
      </div>
    </div>

    <div class="pagination-container">
      <nav>
        <ul class="pagination" id="currentPagination" ref="currentPagination">
          <li
            @click="changePage(1)"
            ata-page="prev"
            id="next"
            :class="{ disabled: currentPage == 1 }"
          >
            <span>&lt;&lt;<span class="sr-only">(current) </span></span>
          </li>
          <li
            @click="changePage(currentPage - 1)"
            ata-page="prev"
            id="next"
            :class="{ disabled: currentPage == 1 }"
          >
            <span>&lt;<span class="sr-only">(current) </span></span>
          </li>
          <!--	Here the JS Function Will Add the Rows -->
          <template v-for="(node, index) in visiblePages" :key="index">
            <li :class="{ active: node == currentPage }" @click="changePage(node, index)">
              <span>{{ node }}</span>
            </li>
          </template>
          <li
            @click="changePage(currentPage + 1)"
            data-page="next"
            id="prev"
            :class="{ disabled: currentPage == totalPages }"
          >
            <span>&gt;<span class="sr-only">(current)</span></span>
          </li>
          <li
            @click="changePage(totalPages)"
            ata-page="prev"
            id="next"
            :class="{ disabled: currentPage == 1 }"
          >
            <span>&gt;&gt;<span class="sr-only">(current) </span></span>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
<style scoped>
.pagination-wrapper {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
}

.pagination-wrapper > div {
  flex: 1;
  min-width: 100%;
}

.pagination-container {
  display: flex;
  justify-content: flex-end;
  margin-top: 10px;
}

@media (min-width: 768px) {
  .pagination-wrapper {
    flex-direction: row;
    flex-wrap: nowrap;
  }

  .pagination-wrapper > div {
    min-width: auto;
    margin-right: 15px;
  }

  .pagination-container {
    margin-top: 0;
  }
}
</style>
