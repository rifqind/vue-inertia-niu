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
    ModalStatus: {
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
    // toggleModalClose: {
    //     type: Function,
    //     require: true,
    // }
})
</script>

<template>
    <Transition name="modal">
        <div v-if="ModalStatus" class="d-block overlay" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" :class="modalSize" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalBsTitle">{{ title }}</h5>
                        <button type="button" class="close" @click="$emit('close')" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <slot name="modalBody" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" @click="$emit('close')">Tutup</button>
                        <slot name="modalFunction" />
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style>
.overlay {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    /* Center horizontally */
    align-items: center;
    transition: opacity 0.3s ease;
}

.modal-title {
    margin-bottom: 0;
    line-height: 1.5;
}

.modal-enter-from {
    opacity: 0;
}

.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
}
</style>