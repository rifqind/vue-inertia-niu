<script setup>
const props = defineProps({
    countTabels: {
        type: Number,
        required: true,
    },
    data: {
        type: Array,
        required: true,
    }
})
</script>
<template>
    <div class="card-header py-3">
        <h6 class="ml-3 my-0 mr-0 font-weight-bold heading-card">
            Menampilkan : {{ countTabels }} tabel</h6>
    </div>
    <div class="card-body" id="list-tabel-card">
        <div v-for="(node, index) in data" :key="index" class="d-flex flex-column mb-2 ml-3">
            <a class="text-red">
                <span class="badge badge-pill badge-danger">{{ node.nomor }}</span> -
                {{ node.label }}, Tahun {{ node.tahun }}
            </a>
            <small class="lead smalltext-homepage">{{ node.nama_dinas }} |
                {{ node.nama_regions }}</small>
            <small class="lead smalltext-homepage" id="subjek-weight"> Subjek :
                {{ node.nama_subjects }}</small>
            <small class="lead smalltext-homepage">Terakhir diupdate :
                {{ node.status_updated }}</small>
        </div>
    </div>
</template>