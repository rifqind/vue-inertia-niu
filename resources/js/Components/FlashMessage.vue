<script setup>
import { onUpdated } from 'vue';

const props = defineProps({
    toggleFlash: {
        type: Boolean,
        default: false,
    },
    flash: {
        type: [String, null],
        required: true,
    },
    types: {
        type: String,
        default: 'alert-success',
    }
})
const emit = defineEmits(['close'])
const hideFlashMessage = function () {
    setInterval(() => {
        emit('close')
    }, 4000);
}
onUpdated(() => {
    if (props.toggleFlash == true) {
        hideFlashMessage()
    }
})
</script>
<template>
    <div class="alert" :class="types" v-if="toggleFlash" role="alert">
        {{ flash }}
    </div>
</template>