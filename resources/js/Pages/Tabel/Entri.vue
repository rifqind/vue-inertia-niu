<script setup>
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
import SpinnerBorder from "@/Components/SpinnerBorder.vue";
import { usePage, useForm, Head, Link } from "@inertiajs/vue3";
import { onMounted, ref, onUpdated } from "vue";
import ModalBs from "@/Components/ModalBs.vue";
import { downloadTabel } from "@/download";
import { watch } from "vue";

const page = usePage();
const form = useForm({
  dataContents: page.props.datacontents,
  decisions: null,
  catatans: page.props.catatans,
  _token: null,
});
const badges = ref(null);
const toggleFlash = ref(false);
const triggerSpinner = ref(false);
const inputDisabled = ref(false);
const downloadModalStatus = ref(false);
const downloadTitle = ref(null);
const TabelData = ref(null);
const RowTabel = ref(null);
const Rowee = ref(null);
const Columnee = ref(null);
const RowTbody = ref(null);
const ColumnTbody = ref(null);
const confirmationModal = ref(false);

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

var columnComponents, rowComponents, turtahunComponents;
var status = page.props.status_desc;

const getData = function (row, column, turtahun) {
  columnComponents = column.id;
  turtahunComponents = turtahun.id;
  if (row.id) {
    rowComponents = row.id;
  } else rowComponents = row.wilayah_fullcode;
  const probablyTheData = form.dataContents.find((x) => {
    return (
      (x.id_row === rowComponents || x.wilayah_fullcode === rowComponents) &&
      x.id_column === columnComponents &&
      x.id_turtahun === turtahunComponents
    );
  });
  //   console.log(probablyTheData);
  return probablyTheData.value;
};
const defineBadges = function (status) {
  const statusMapping = {
    1: "badge-status-satu",
    2: "badge-status-dua",
    3: "badge-status-tiga",
    4: "badge-status-empat",
    5: "badge-status-lima",
  };
  badges.value = statusMapping[status];
};
const buttonMapping = {
  admin: false,
  kominfo: false,
  produsen: true,
};
const defineButton = function (role, position) {
  const isAdmin = buttonMapping[role];
  const status = page.props.status_desc[0];
  if (isAdmin) {
    if (position == "right") return false;
    else {
      if (status == 3 || status > 4) return false;
      else return true;
    }
  } else {
    if (position == "left") return false;
    else {
      if (status < 3) return false;
      else return true;
    }
  }
};
const defineInputDisable = function (status, role) {
  if (status == 3) inputDisabled.value = true;
  if (status > 4) inputDisabled.value = true;
  if (!buttonMapping[role]) inputDisabled.value = true;
};
const handleInput = function (event, row, column, turtahun) {
  // console.log(event, row, column);
  turtahunComponents = turtahun.id;
  columnComponents = column.id;
  if (row.id) {
    rowComponents = row.id;
  } else rowComponents = row.wilayah_fullcode;
  const theIndex = form.dataContents.findIndex((x) => {
    return (
      (x.id_row === rowComponents || x.wilayah_fullcode === rowComponents) &&
      x.id_column === columnComponents &&
      x.id_turtahun === turtahunComponents
    );
  });
  // ??how to edit form.dataContents.value??
  if (theIndex !== -1) {
    form.dataContents[theIndex].value = event.target.value;
  }
};
const mountThis = ref(false);
onMounted(() => {
  defineBadges(status[0]);
  defineInputDisable(status[0], page.props.auth.user.role);
  mountThis.value = true;
  // nextTick(() => {
  //     let RowTheadHeight = Rowee.value.offsetHeight
  //     let DatasTheadHeight = Columnee.value.offsetHeight
  //     if (RowTheadHeight > DatasTheadHeight) {
  //         Columnee.value.style.height = `${RowTheadHeight}px`
  //     }
  //     else {
  //         Rowee.value.style.height = `${DatasTheadHeight}px`
  //     }
  // })
});

onUpdated(() => {
  status = page.props.status_desc;
  defineBadges(status[0]);
  defineInputDisable(status[0], page.props.auth.user.role);
});

const submit = async function (decision) {
  form.decisions = decision;
  const response = await axios.get(route("token"));
  form._token = response.data;
  if (form.processing) return;
  decisionConfirm.value = null;
  if (decision == "save" || decision == "send") {
    form.post(route("tabel.update_content"), {
      onSuccess: function () {
        toggleFlash.value = true;
      },
      onBefore: function () {
        triggerSpinner.value = true;
      },
      onFinish: function () {
        triggerSpinner.value = false;
        confirmationModal.value = false;
      },
      onError: function () {
        triggerSpinner.value = false;
      },
    });
  } else {
    form.post(route("tabel.adminHandleData"), {
      onSuccess: function () {
        toggleFlash.value = true;
      },
      onBefore: function () {
        triggerSpinner.value = true;
      },
      onFinish: function () {
        triggerSpinner.value = false;
        confirmationModal.value = false;
      },
      onError: function () {
        triggerSpinner.value = false;
      },
    });
  }
};
const handlePaste = (event, row, column, turtahun) => {
  const items = event.clipboardData.items;
  for (let i = 0; i < items.length; i++) {
    if (items[i].type === "text/plain") {
      items[i].getAsString((text) => {
        const columnIndex = event.target.closest("td").cellIndex;
        const rowIndex = event.target.closest("tr").rowIndex;
        const lines = text.trim().split("\n");
        lines.forEach((line, index) => {
          const cells = line.trim().split("\t");
          cells.forEach((cell, subIndex) => {
            const row = rowIndex + index;
            const col = columnIndex + subIndex;
            const table = event.target.closest("table");
            const tableRow = table.rows[row];
            if (tableRow) {
              const tableCell = tableRow.cells[col];
              if (tableCell) {
                const input = tableCell.querySelector('input:not([type="hidden"])');
                if (input) {
                  const rowComponents = input.id.split("-")[1];
                  const columnComponents = input.id.split("-")[2];
                  const turtahunComponents = input.id.split("-")[3];
                  input.value = cell;
                  const theIndex = form.dataContents.findIndex((x) => {
                    let founded =
                      (x.id_row == rowComponents ||
                        x.wilayah_fullcode == rowComponents) &&
                      x.id_column == columnComponents &&
                      x.id_turtahun == turtahunComponents;
                    return founded;
                  });
                  if (theIndex !== -1) {
                    form.dataContents[theIndex].value = cell;
                  }
                }
              }
            }
          });
        });
      });
    }
  }
};

const setId = (row, column, turtahun) => {
  columnComponents = column.id;
  turtahunComponents = turtahun.id;
  if (row.id) {
    rowComponents = row.id;
  } else rowComponents = row.wilayah_fullcode;
  return "cell-" + rowComponents + "-" + columnComponents + "-" + turtahunComponents;
};
const hiddenLabel = (value, index) => {
  if (value.length > 30) {
    indexExpanded.value[index] = false;
    return value.substring(0, 30) + " ";
  }
  return value;
};
const toggleLabel = (index) => {
  indexExpanded.value[index] = !indexExpanded.value[index];
};
const indexExpanded = ref(Array(page.props.columns.length).fill(true));
page.props.columns.forEach((column, index) => {
  if (column.label.length > 30) indexExpanded.value[index] = false;
});

// const setFormatGermanyNumber = () => {
//     firstClick.value = false
//     form.dataContents.forEach((value) => {
//         // console.log(Number(value.value))
//         let result
//         let numericValue = Number(value.value)
//         if (!Number.isNaN(numericValue))
//             result = numericValue.toLocaleString('de-DE')
//         else {
//             let temp = value.value
//             let SpaceThousand = temp.replace(' ', '')
//             if (!Number.isNaN(Number(SpaceThousand))) {
//                 result = Number(SpaceThousand).toLocaleString('de-DE')
//                 return //want to next iteration
//             }
//             let ComaWithoutThousand = temp.replace(',', '.')
//             if (!Number.isNaN(Number(ComaWithoutThousand))) {
//                 result = Number(ComaWithoutThousand).toLocaleString('de-DE')
//                 return //want to next iteration
//             }
//             let ComaThousand = temp.replace(',', '')
//             //still confuse
//         }
//     })
// }
const setFormatGermanyNumber = () => {
  firstClick.value = false;

  form.dataContents.forEach((item) => {
    let result;
    let temp = item.value;

    // Remove spaces
    temp = temp.replace(/\s+/g, "");

    // Check for commas
    if (temp.includes(",")) {
      // Check if it's a decimal point (e.g., 27,924506)
      let decimalMatch = temp.match(/,\d{2}$/);
      if (decimalMatch) {
        // Replace comma with dot for decimal point
        temp = temp.replace(",", ".");
      } else {
        // Remove all commas for thousand separators
        temp = temp.replace(/,/g, "");
      }
    }

    // Convert the cleaned string to a number
    let numericValue = Number(temp);

    if (!Number.isNaN(numericValue)) {
      // Format the number to German format
      result = numericValue.toLocaleString("de-DE");
    }

    // Update the value only if it was successfully formatted
    if (result) {
      item.value = result;
    }
  });
};
const firstClick = ref(true);
const HeaderColumn = (value) => {
  if (value == "Tahun") {
    return page.props.years;
  } else return value;
};
const warningCard = ref(true);
const decisionConfirm = ref(null);
const triggerConfirmation = (value) => {
  confirmationModal.value = true;
  decisionConfirm.value = value;
};
</script>
<template>
  <Head title="Entri Data" />
  <SpinnerBorder v-if="triggerSpinner" />
  <GeneralLayout :entri="mountThis">
    <div id="container-of-entry" class="pb-3">
      <div class="card">
        <div class="card-body">
          <h3 class="text-bold">
            <span id="nomor" class="badge">{{ page.props.nomor_tabel }}</span>
            {{ page.props.judul_tabel }}, Tahun
            {{ page.props.years }}
          </h3>
          <h4 class="my-0 d-flex">
            <span class="badge" :class="badges" id="badges-status"> {{ status[1] }}</span>
            <span class="ml-auto text-right small" id="">
              Terakhir diupdate : {{ page.props.status_updated }}
            </span>
          </h4>
        </div>
      </div>
      <Teleport to="body" v-if="warningCard">
        <div class="container-float">
          <div class="card card-float">
            <div class="chat-header">
              <button
                type="button"
                @click="warningCard = false"
                class="close mr-3 mt-2"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="card-body p-0">
              <ul class="text-white warning">
                <li>Ribuan harus dipisahkan dengan titik (.)</li>
                <li>Desimal harus dipisahkan dengan koma (,)</li>
                <li>
                  Jika ada desimal, maka minimal ada digit dua angka setelah desimal. Jika
                  hanya satu digit maka ganti dengan angka 0 untuk digit keduanya.
                </li>
              </ul>
            </div>
          </div>
        </div>
      </Teleport>
      <FlashMessage
        :toggleFlash="toggleFlash"
        @close="flashHandle"
        :flashObject="flashObject"
      />
      <!-- format two table  -->
      <!-- <div class="table-container">
        <div class="row mb-3">
          <div class="overflow-x-scroll p-0" id="imaginer">
            <table class="table table-bordered" id="RowTabel" ref="RowTabel">
              <thead ref="Rowee">
                <tr>
                  <th class="text-center align-middle tabel-width-15" rowspan="2">#</th>
                  <th class="text-center align-middle" rowspan="2">
                    {{ page.props.row_label }}
                  </th>
                </tr>
              </thead>
              <tbody ref="RowTbody">
                <tr v-for="(nodeRow, index) in page.props.rows" :key="index">
                  <td>{{ index + 1 }}</td>
                  <td>{{ nodeRow.label }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="table-data-wrapper" ref="TabelData" id="TabelData">
            <table class="table table-bordered" id="ColumnTabel">
              <thead ref="Columnee">
                <tr>
                  <th
                    class="text-center"
                    :colspan="page.props.columns.length"
                    v-for="(node, index) in page.props.turtahuns"
                    :key="index"
                  >
                    {{ HeaderColumn(node.label) }}
                  </th>
                </tr>
                <tr>
                  <template v-for="(node, index) in page.props.turtahuns" :key="index">
                    <th
                      class="text-center align-middle"
                      v-for="(node, index) in page.props.columns"
                      :key="index"
                    >
                      <template v-if="indexExpanded[index]">{{ node.label }} </template>
                      <template v-else>{{ hiddenLabel(node.label, index) }} </template>
                      <span
                        v-if="!indexExpanded[index] || node.label.length > 30"
                        class="badge badge-info ml-1"
                        @click="toggleLabel(index)"
                        >...</span
                      >
                    </th>
                  </template>
                </tr>
              </thead>
              <tbody ref="ColumnTbody">
                <tr v-for="(nodeRow, index) in page.props.rows" :key="index">
                  <template
                    v-for="(nodeTurtahun, index) in page.props.turtahuns"
                    :key="index"
                  >
                    <td v-for="(nodeColumn, index) in page.props.columns" :key="index">
                      <input
                        type="text"
                        class="w-100 text-center"
                        :id="setId(nodeRow, nodeColumn, nodeTurtahun)"
                        :value="getData(nodeRow, nodeColumn, nodeTurtahun)"
                        :disabled="inputDisabled"
                        @paste="
                          (event) => {
                            handlePaste(event, nodeRow, nodeColumn, nodeTurtahun);
                          }
                        "
                        @input="
                          (event) => {
                            handleInput(event, nodeRow, nodeColumn, nodeTurtahun);
                          }
                        "
                      />
                    </td>
                  </template>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div> -->
      <!-- format single table -->
      <div class="overflow-x-scroll mb-2">
        <table class="table table-bordered">
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
                  class="not-fixed"
                  v-for="(nodeColumn, index) in page.props.columns"
                  :key="index"
                >
                  <input
                    type="text"
                    class="w-100 text-center"
                    :id="setId(nodeRow, nodeColumn, nodeTurtahun)"
                    :value="getData(nodeRow, nodeColumn, nodeTurtahun)"
                    :disabled="inputDisabled"
                    @paste="
                      (event) => {
                        handlePaste(event, nodeRow, nodeColumn, nodeTurtahun);
                      }
                    "
                    @input="
                      (event) => {
                        handleInput(event, nodeRow, nodeColumn, nodeTurtahun);
                      }
                    "
                  />
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card">
        <div class="card-header">CATATAN</div>
        <div class="card-body">
          <textarea
            :disabled="inputDisabled"
            name="catatan"
            v-model="form.catatans"
            class="form-control"
            id="catatan"
            rows="5"
            placeholder="Masukkan Catatan Jika Perlu"
            >{{ form.catatans }}</textarea
          >
        </div>
      </div>
      <div class="mb-2 d-flex">
        <div class="flex-grow-1">
          <Link :href="route('tabel.index')" class="btn btn-light border"
            ><font-awesome-icon icon="fas fa-chevron-left" />
            Kembali
          </Link>
        </div>
        <button
          class="btn bg-success-fordone mr-2"
          title="Download"
          @click="downloadModalStatus = true"
        >
          <font-awesome-icon icon="fa-solid fa-circle-down" /> Download
        </button>
        <button
          v-if="defineButton(page.props.auth.user.role, 'left')"
          @click="submit((decision = 'save'))"
          class="btn bg-primary-fordone save-send mr-2"
          id="save-table"
        >
          <font-awesome-icon icon="fas fa-save" /> Simpan
        </button>
        <button
          v-if="defineButton(page.props.auth.user.role, 'left')"
          @click="triggerConfirmation('send')"
          class="btn bg-success-fordone save-send"
          id="save-table"
        >
          <font-awesome-icon icon="fas fa-paper-plane" /> Kirim
        </button>
        <button
          v-if="firstClick && defineButton(page.props.auth.user.role, 'right')"
          class="btn bg-info mr-2"
          @click="setFormatGermanyNumber"
          id="save-table"
        >
          Format Angka
        </button>
        <button
          v-if="defineButton(page.props.auth.user.role, 'right')"
          @click="submit((decision = 'reject'))"
          class="btn badge-status-empat mr-2"
          id="save-table"
        >
          <font-awesome-icon icon="fas fa-ban" /> Reject
        </button>
        <button
          v-if="defineButton(page.props.auth.user.role, 'right')"
          @click="triggerConfirmation('final')"
          class="btn bg-success-fordone"
          id="save-table"
        >
          <font-awesome-icon icon="fas fa-flag-checkered" /> Final
        </button>
      </div>
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
              @click.prevent="downloadTabel(downloadTitle)"
            >
              Simpan
            </button>
          </template>
        </ModalBs>
        <ModalBs
          :ModalStatus="confirmationModal"
          @close="confirmationModal = false"
          :title="'Konfirmasi'"
        >
          <template #modalBody>
            <label
              >Apakah format titik (.) dan koma (,) sudah sesuai? Harap diperhatikan
              karena berdampak pada hasil unduh data</label
            >
          </template>
          <template #modalFunction>
            <button
              type="button"
              class="btn btn-sm bg-success-fordone"
              @click="submit((decision = decisionConfirm))"
            >
              Setuju
            </button>
          </template>
        </ModalBs>
      </Teleport>
    </div>
  </GeneralLayout>
</template>
<style scoped>
.container-float {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 50%;
  z-index: 1000;
}

.card-float {
  background-color: rgb(239, 171, 46);
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  max-height: 400px;
}

.warning {
  font-size: 14px;
}

#container-of-entry {
  margin-right: 5%;
  margin-left: 5%;
  font-size: 12px;
}

.badge {
  cursor: pointer;
}

#RowTabel {
  table-layout: fixed;
  width: 400px;
  background: #f9fafc;
  border-right: 1px solid #e6eaf0;
  vertical-align: top;
}

#imaginer {
  width: 400px;
}

#RowTabel td:first-child {
  width: 12px;
}

#RowTabel thead,
#ColumnTabel thead {
  height: 120px;
  vertical-align: middle;
  padding: 0.1rem;
  text-overflow: ellipsis;
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
thead {
  background-color: #3d3b8e;
  color: whitesmoke;
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
  min-width: 300px;
}
</style>
