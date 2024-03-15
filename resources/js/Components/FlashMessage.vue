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
    }
})
const emit = defineEmits(['close'])
const hideFlashMessage = function () {
    setInterval(() => {
        emit('close')
    }, 3000);
}
onUpdated(() => {
    if (props.toggleFlash == true) {
        hideFlashMessage()
    }
})
</script>
<template>
    <div class="alert alert-success" v-if="toggleFlash" role="alert">
        {{ flash }}
    </div>
</template>