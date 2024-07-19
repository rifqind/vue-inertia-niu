<script setup>
import { onUpdated, ref } from 'vue';

const props = defineProps({
    toggleFlash: {
        type: Boolean,
        default: false,
    },
    // flash: {
    //     type: [String, null],
    //     required: true,
    // },
    flashObject: {
        type: Object,
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
const message = ref(null)
const typesFlash = ref('alert-success')

onUpdated(() => {
    if (props.toggleFlash == true) {
        if (props.flashObject.error) {
            typesFlash.value = 'alert-danger'
            message.value = props.flashObject.error 
        } else {
            message.value = props.flashObject.message
            typesFlash.value = 'alert-success'
        } 
        hideFlashMessage()
    }
})
</script>
<template>
    <div class="alert" :class="typesFlash" v-if="toggleFlash" role="alert">
        {{ message }}
    </div>
</template>