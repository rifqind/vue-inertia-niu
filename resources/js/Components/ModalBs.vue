<script setup>
import { computed } from 'vue';
import { ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
    },
    modalSize: {
        type: String,
        default: null,
    },
    updateModalStatus: {
        type: Boolean,
        default: false
    },
    inputFunction: {
        type: Function,
        default: null,
    },
    functionLabel: {
        type: String,
        default: 'Simpan',
    },
    inputClass: {
        type: String,
        default: 'bg-success-fordone'
    },
    toggleModalClose: {
        type: Function,
        require: true,
    }
})
</script>

<template>
    <div v-if="updateModalStatus" class="modal fade show d-block" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalBs"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" :class="modalSize" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBsTitle">{{ title }}</h5>
                    <button type="button" class="close" @click="toggleModalClose" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <slot />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" @click="toggleModalClose"
                        data-bs-dismiss="modal">Tutup</button>
                    <button id="" type="button" class="btn btn-sm" :class="inputClass" @click="inputFunction">{{
                        functionLabel }}</button>
                </div>
            </div>
        </div>
    </div>
</template>