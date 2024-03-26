<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue'
import { usePage, useForm, Head } from '@inertiajs/vue3'

const page = usePage()
const form = useForm({
    dataContents: page.props.datacontents
})
var columnComponents, rowComponents
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

const submit = function () {
    form.post()
}
</script>
<template>

    <Head title="Entri Data" />
    <GeneralLayout>
        <div class="container pb-3">
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
                                            <input type="text" class="form-control"
                                                :value="getData(nodeRow, nodeColumn)"
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
        </div>
        <!-- <div><br /></div> -->
    </GeneralLayout>
</template>
<style scoped>
#RowTabel {
    table-layout: fixed;
    width: 400px;
    background: #f9fafc;
    border-right: 1px solid #e6eaf0;
    vertical-align: top;
}

#RowTabel td:first-child {
    width: 10%;
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