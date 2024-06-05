<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import FlashMessage from '@/Components/FlashMessage.vue';
import SpinnerBorder from '@/Components/SpinnerBorder.vue';
import { usePage, useForm, Head, Link } from '@inertiajs/vue3'
import { onMounted, ref, onUpdated } from 'vue';
import ModalBs from '@/Components/ModalBs.vue';
import { downloadTabel } from '@/download'

const page = usePage()
const form = useForm({
    dataContents: page.props.datacontents,
    decisions: null,
    catatans: page.props.catatans,
    _token: null,
})
const badges = ref(null)
const toggleFlash = ref(false)
const triggerSpinner = ref(false)
const inputDisabled = ref(false)
const downloadModalStatus = ref(false)
const downloadTitle = ref(null)
const TabelData = ref(null)
const RowTabel = ref(null)
const Rowee = ref(null)
const Columnee = ref(null)
const RowTbody = ref(null)
const ColumnTbody = ref(null)

var columnComponents, rowComponents
var status = page.props.status_desc

const getData = function (row, column) {
    columnComponents = column.id
    if (row.id) {
        rowComponents = row.id
    } else rowComponents = row.wilayah_fullcode
    const probablyTheData = form.dataContents.find(x => {
        return (x.id_row === rowComponents || x.wilayah_fullcode === rowComponents) && x.id_column === columnComponents
    })
    return probablyTheData.value
}
const defineBadges = function (status) {
    const statusMapping = {
        1: "badge-status-satu",
        2: "badge-status-dua",
        3: "badge-status-tiga",
        4: "badge-status-empat",
        5: "badge-status-lima"
    }
    badges.value = statusMapping[status]
}
const buttonMapping = {
    'admin': false,
    'kominfo': false,
    'produsen': true,
}
const defineButton = function (role, position) {
    const isAdmin = buttonMapping[role]
    const status = page.props.status_desc[0]
    if (isAdmin) {
        if (position == 'right') return false
        else {
            if (status == 3 || status > 4) return false
            else return true
        }
    } else {
        if (position == 'left') return false
        else {
            if (status < 3) return false
            else return true
        }
    }
}
const defineInputDisable = function (status, role) {
    if (status == 3) inputDisabled.value = true
    if (status > 4) inputDisabled.value = true
    if (!buttonMapping[role]) inputDisabled.value = true
}
const handleInput = function (event, row, column) {
    // console.log(event, row, column);
    columnComponents = column.id
    if (row.id) {
        rowComponents = row.id
    } else rowComponents = row.wilayah_fullcode
    const theIndex = form.dataContents.findIndex(x => {
        return (x.id_row === rowComponents || x.wilayah_fullcode === rowComponents) && x.id_column === columnComponents
    })
    // ??how to edit form.dataContents.value?? 
    if (theIndex !== -1) {
        form.dataContents[theIndex].value = event.target.value
    }
}
onMounted(() => {
    defineBadges(status[0])
    defineInputDisable(status[0], page.props.auth.user.role)
    let RowTheadHeight = Rowee.value.offsetHeight
    let DatasTheadHeight = Columnee.value.offsetHeight
    if (RowTheadHeight > DatasTheadHeight) {
        Columnee.value.style.height = `${RowTheadHeight}px`
    }
    else {
        Rowee.value.style.height = `${DatasTheadHeight}px`
    }
})

onUpdated(() => {
    status = page.props.status_desc
    defineBadges(status[0])
    defineInputDisable(status[0], page.props.auth.user.role)
})

const submit = async function (decision) {
    form.decisions = decision
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
    if (decision == 'save' || decision == 'send') {
        form.post(route('tabel.update_content'), {
            onSuccess: function () { toggleFlash.value = true },
            onBefore: function () { triggerSpinner.value = true },
            onFinish: function () { triggerSpinner.value = false },
            onError: function () { triggerSpinner.value = false }
        })
    } else {
        form.post(route('tabel.adminHandleData'), {
            onSuccess: function () { toggleFlash.value = true },
            onBefore: function () { triggerSpinner.value = true },
            onFinish: function () { triggerSpinner.value = false },
            onError: function () { triggerSpinner.value = false }
        })
    }
}
const handlePaste = (event, row, column) => {
    const items = event.clipboardData.items
    for (let i = 0; i < items.length; i++) {
        if (items[i].type === "text/plain") {
            items[i].getAsString((text) => {
                const columnIndex = event.target.closest("td").cellIndex;
                const rowIndex = event.target.closest("tr").rowIndex;
                const lines = text.trim().split("\n")
                lines.forEach((line, index) => {
                    const cells = line.trim().split("\t")
                    cells.forEach((cell, subIndex) => {
                        const row = rowIndex + index;
                        const col = columnIndex + subIndex;
                        const table = event.target.closest("table");
                        const tableRow = table.rows[row];
                        if (tableRow) {
                            const tableCell = tableRow.cells[col];
                            if (tableCell) {
                                const input =
                                    tableCell.querySelector(
                                        'input:not([type="hidden"])'
                                    );
                                if (input) {
                                    const rowComponents = input.id.split("-")[1]
                                    const columnComponents = input.id.split("-")[2]
                                    input.value = cell;
                                    const theIndex = form.dataContents.findIndex(x => {
                                        let founded = (x.id_row == rowComponents || x.wilayah_fullcode == rowComponents) && x.id_column == columnComponents
                                        return founded
                                    })
                                    if (theIndex !== -1) {
                                        form.dataContents[theIndex].value = cell
                                    }
                                }
                            }
                        }
                    })
                })
            })
        }
    }
}

const setId = (row, column) => {
    columnComponents = column.id
    if (row.id) {
        rowComponents = row.id
    } else rowComponents = row.wilayah_fullcode
    return 'cell-' + rowComponents + '-' + columnComponents
}
</script>
<template>

    <Head title="Entri Data" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container pb-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-bold">{{ page.props.judul_tabel }}, Tahun {{ page.props.years }}
                    </h3>
                    <h4 class="my-0 d-flex">
                        <span class="badge" :class="badges" id="badges-status"> {{ status[1] }}</span>
                        <span class="ml-auto text-right small" id=""> Terakhir diupdate : {{
        page.props.status_updated }}</span>
                    </h4>
                </div>
            </div>
            <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
            <div class="table-container">
                <div class="row mb-3">
                    <div class="overflow-x-scroll p-0" id="imaginer">
                        <table class="table table-bordered" id="RowTabel" ref="RowTabel">
                            <thead ref="Rowee">
                                <tr>
                                    <th class="text-center align-middle tabel-width-15" rowspan="2">#</th>
                                    <th class="text-center align-middle" rowspan="2">{{ page.props.row_label }}</th>
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
                                    <th class="text-center" :colspan="page.props.columns.length"
                                        v-for="(node, index) in page.props.turtahuns" :key="index">{{ node.label }}</th>
                                </tr>
                                <tr>
                                    <template v-for="(node, index) in page.props.turtahuns" :key="index">
                                        <th class="text-center align-middle" v-for="(node, index) in page.props.columns"
                                            :key="index">{{
        node.label }}</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody ref="ColumnTbody">
                                <tr v-for="(nodeRow, index) in page.props.rows" :key="index">
                                    <template v-for="(nodeTurtahun, index) in page.props.turtahuns" :key="index">
                                        <td v-for="(nodeColumn, index) in page.props.columns" :key="index">
                                            <input type="text" class="form-control" :id="setId(nodeRow, nodeColumn)"
                                                :value="getData(nodeRow, nodeColumn)" :disabled="inputDisabled"
                                                @paste="(event) => { handlePaste(event, nodeRow, nodeColumn) }"
                                                @input="(event) => { handleInput(event, nodeRow, nodeColumn) }">
                                        </td>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- format single table -->
            <!-- <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center align-middle" rowspan="2">#</th>
                        <th class="text-center align-middle" rowspan="2">{{ page.props.row_label }}</th>
                        <th class="text-center" :colspan="page.props.columns.length"
                            v-for="(node, index) in page.props.turtahuns" :key="index">{{ node.label }}</th>
                    </tr>
                    <tr>
                        <template v-for="(node, index) in page.props.turtahuns" :key="index">
                            <th class="text-center" v-for="(node, index) in page.props.columns" :key="index">{{
                                    node.label }}</th>
                        </template>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(nodeRow, index) in page.props.rows" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>{{ nodeRow.label }}</td>
                        <template v-for="(nodeTurtahun, index) in page.props.turtahuns" :key="index">
                            <td v-for="(nodeColumn, index) in page.props.columns" :key="index">
                                <input type="text" class="form-control" :value="getData(nodeRow, nodeColumn)"
                                    @input="(event) => { handleInput(event, nodeRow, nodeColumn) }">
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table> -->
            <div class="card">
                <div class="card-header">
                    CATATAN
                </div>
                <div class="card-body">
                    <textarea :disabled="inputDisabled" name="catatan" v-model="form.catatans" class="form-control"
                        id="catatan" rows="5" placeholder="Masukkan Catatan Jika Perlu">{{ form.catatans }}</textarea>
                </div>
            </div>
            <div class="mb-2 d-flex">
                <div class="flex-grow-1">
                    <Link :href="route('tabel.index')" class="btn btn-light border"><font-awesome-icon
                        icon="fas fa-chevron-left" />
                    Kembali
                    </Link>
                </div>
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" />
                    Download</button>
                <button v-if="defineButton(page.props.auth.user.role, 'left')" @click="submit(decision = 'save')"
                    class="btn bg-primary-fordone save-send mr-2" id="save-table"><font-awesome-icon
                        icon="fas fa-save" />
                    Simpan</button>
                <button v-if="defineButton(page.props.auth.user.role, 'left')" @click="submit(decision = 'send')"
                    class="btn bg-success-fordone save-send" id="save-table"><font-awesome-icon
                        icon="fas fa-paper-plane" />
                    Kirim</button>
                <button v-if="defineButton(page.props.auth.user.role, 'right')" @click="submit(decision = 'reject')"
                    class="btn badge-status-empat mr-2" id="save-table"><font-awesome-icon icon="fas fa-ban" />
                    Reject</button>
                <button v-if="defineButton(page.props.auth.user.role, 'right')" @click="submit(decision = 'final')"
                    class="btn bg-success-fordone" id="save-table"><font-awesome-icon icon="fas fa-flag-checkered" />
                    Final</button>
            </div>
            <Teleport to="body">
                <ModalBs :-modal-status="downloadModalStatus" @close="downloadModalStatus = false"
                    :title="'Download Data'">
                    <template #modalBody>
                        <label>Masukkan Judul File</label>
                        <input type="text" v-model="downloadTitle" class="form-control" />
                    </template>
                    <template #modalFunction>
                        <button type="button" class="btn btn-sm bg-success-fordone"
                            @click.prevent="downloadTabel(downloadTitle)">Simpan</button>
                    </template>
                </ModalBs>
            </Teleport>
        </div>
    </GeneralLayout>
</template>
<style scoped>
#RowTabel {
    table-layout: fixed;
    width: 400px;
    background: #f9fafc;
    border-right: 1px solid #e6eaf0;
    vertical-align: top;
    /* white-space: nowrap; */
    /* text-overflow: ellipsis; */
    /* overflow: hidden; */
}

#imaginer {
    width: 400px;
}

#RowTabel td:first-child {
    width: 12px;
}

#RowTabel thead,
#ColumnTabel thead {
    /* min-height: 200px; */
    /* height: 100px; */
    vertical-align: middle;
    padding: .1rem;
    /* white-space: nowrap; */
    /* text-overflow: ellipsis; */
    overflow: auto;
}

#ColumnTabel tbody tr td {
    /* padding-right: 1rem; */
    min-width: 180px;
}

#RowTabel tbody tr td,
#ColumnTabel tbody tr td {
    /* height: 20px; */
    height: 65px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    /* padding: 0; */
    /* text-align: left; */
    /* display: flex; */
    /* align-content: center; */
    /* align-items: center; */
}

.table-data-wrapper {
    /* display: inline-block; */
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
</style>