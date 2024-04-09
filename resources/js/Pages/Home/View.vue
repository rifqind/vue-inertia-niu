<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue'
import { usePage, Link, Head } from '@inertiajs/vue3'
import { ref } from 'vue';

const page = usePage()

const badges = ref(null)
const triggerSpinner = ref(false)

var columnComponents, rowComponents

const getData = function (row, column) {
    columnComponents = column.id
    if (row.id) {
        rowComponents = row.id
    } else rowComponents = row.wilayah_fullcode
    const probablyTheData = page.props.datacontents.find(x => {
        return (x.id_row === rowComponents || x.wilayah_fullcode === rowComponents) && x.id_column === columnComponents
    })
    return probablyTheData.value
}
</script>
<template>
    <Head title="Lihat Tabel" />
    <HomeLayout>
        <div class="container pb-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-bold">{{ page.props.tabel.judul_tabel }}, Tahun {{ page.props.tahun }}
                    </h3>
                    <h4 class="my-0 d-flex">
                        <span class="ml-auto text-right small" id=""> Terakhir diupdate : {{
                        page.props.tabel.status_updated }}</span>
                    </h4>
                </div>
            </div>
            <div class="table-container">
                <div class="row">
                    <table class="table table-bordered" id="RowTabel">
                        <thead>
                            <tr>
                                <th class="text-center align-middle tabel-width-15" rowspan="2">#</th>
                                <th class="text-center align-middle" rowspan="2">{{ page.props.row_label }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(nodeRow, index) in page.props.rows" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ nodeRow.label }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="table-data-wrapper">
                        <table class="table table-bordered" id="ColumnTabel">
                            <thead>
                                <tr>
                                    <th class="text-center" :colspan="page.props.columns.length"
                                        v-for="(node, index) in page.props.turtahuns" :key="index">{{ node.label }}</th>
                                </tr>
                                <tr>
                                    <template v-for="(node, index) in page.props.turtahuns" :key="index">
                                        <th class="text-center" v-for="(node, index) in page.props.columns"
                                            :key="index">{{
                        node.label }}</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(nodeRow, index) in page.props.rows" :key="index">
                                    <template v-for="(nodeTurtahun, index) in page.props.turtahuns" :key="index">
                                        <td v-for="(nodeColumn, index) in page.props.columns" :key="index">
                                            {{ getData(nodeRow, nodeColumn) }}
                                        </td>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                        <div class="col text-bold">{{ page.props.tabels.unit }}
                        </div>
                    </div>
                </div>
            </div>
            <Link :href="route('home')" class="btn btn-light border mb-3"><i class="fas fa-chevron-left"></i>
            Kembali
            </Link>
        </div>
    </HomeLayout>
</template>
<style scoped>
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

#RowTabel thead,
#ColumnTabel thead {
    /* min-height: 200px; */
    height: 100px;
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