<script setup>
const props = defineProps({
    turtahun: {
        type: Array,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    rows: {
        type: Array,
        required: true,
    }
})
</script>
<template>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center align-middle" rowspan="2">#</th>
                <th class="text-center align-middle" rowspan="2">{{ rows[0].tipe
                    }}</th>
                <th class="text-center" :colspan="columns.length" v-for="(node, index) in turtahun"
                    :key="index">{{ node.label }}
                </th>
            </tr>
            <tr>
                <template v-for="(node, index) in turtahun" :key="index">
                    <th class="text-center" v-for="(node, index) in columns" :key="index">{{ node.label }}</th>
                </template>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(node, index) in rows" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ node.label }}</td>
                <template v-for="(node, index) in turtahun" :key="index">
                    <td v-for="(node, index) in columns" :key="index"></td>
                </template>
            </tr>
        </tbody>
    </table>
</template>