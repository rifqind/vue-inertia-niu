<script setup>
import { watch } from 'vue';
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
        default: [10, 15, 20, 50]
    }
})
const totalPages = () => {
    return Math.ceil(props.totalItems / props.showItems)
}
const emits = defineEmits([
    'update:showItems',
    'update:currentPage',
])
const changePage = (node) => {
    if (node < 1 || node > totalPages()) return
    emits('update:currentPage', node)
}
const updateShowItems = (event) => {
    emits('update:showItems', parseInt(event.target.value, 10))
}
watch(() => props.currentPage, (newPage) => {
    if (newPage > totalPages()) emits('update:currentPage', totalPages())   
})
</script>
<template>
    <div class="d-flex justify-content-end align-items-center">
        <div id="statusText" ref="statusText" class="mb-3 mx-3 ml-auto">Menampilkan <span id="showItems">{{ showItems
                }}</span> dari
            <span id="showTotal">{{ totalItems }}</span>
        </div>
        <div class="form-group"> <!--		Show Numbers Of Rows 		-->
            <select class="form-control" ref="maxRows" @change="updateShowItems" name="state"
                id="maxRows">
                <option v-for="(node, index) in adjustPage" :key="index" :value="node">{{ node }}</option>
            </select>
        </div>
        <div class="pagination-container">
            <nav>
                <ul class="pagination" id="currentPagination" ref="currentPagination">
                    <li @click="changePage(currentPage - 1)" ata-page="prev" id="next"
                        :class="{ disabled: currentPage == 1 }">
                        <span>
                            < <span class="sr-only">(current)
                        </span></span>
                    </li>
                    <!--	Here the JS Function Will Add the Rows -->
                    <template v-for="(node, index) in totalPages()" :key="index">
                        <li :class="{ active: node == currentPage }" @click="changePage(node)">
                            <span>{{ node }}</span>
                        </li>
                    </template>
                    <li @click="changePage(currentPage + 1)" data-page="next" id="prev"
                        :class="{ disabled: currentPage == totalPages() }">
                        <span> > <span class="sr-only">(current)</span></span>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>