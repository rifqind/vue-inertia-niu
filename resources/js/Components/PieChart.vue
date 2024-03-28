<script setup>
import { Doughnut } from 'vue-chartjs'
import { defineComponent } from 'vue'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'

ChartJS.register(ArcElement, Tooltip)
defineComponent({
    Doughnut
})
const props = defineProps({
    chartValue: {
        type: Array,
        required: true
    },
})
const ChartData = {
    labels: ['Rilis', "Entri", "Periksa", "Perbaikan", "Tabel Baru"],
    datasets: [
        {
            label: "Jumlah Tabel",
            data: props.chartValue,
            backgroundColor: [
                "green",
                "#03254e",
                "#f18f01",
                "#8B1E3F",
                "#7286a0",
            ],
            hoverOffset: 4,
            borderColor: ["white"],
            borderWidth: [1, 1, 1, 1, 1, 1, 1],
        },
    ],
}
const ChartOptions = {
    maintainAspectRatio: false,
    responsive: true,
}
</script>
<template>
    <Doughnut :data="ChartData" :options="ChartOptions" />
</template>