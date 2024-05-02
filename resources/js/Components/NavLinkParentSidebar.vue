<script setup>
import { ref } from 'vue';

const props = defineProps({
    navIcon: {
        type: String,
    },
    role: {
        type: Boolean,
        default: true,
    },
    menuOpen: {
        type: Boolean,
        default: false,
    },
    toggleMenuOpen: {
        type: Function,
        required: true,
    },
    params: {
        type: String,
        default: null,
    }
})
</script>

<template>
    <li v-if="role" class="nav-item" :class="{ 'menu-is-open menu-open': menuOpen }">
        <div class="nav-link" @click="function () { toggleMenuOpen(params) }">
            <font-awesome-icon class="nav-icon" :icon="navIcon" />
            <p>
                <slot name="label" />
                <font-awesome-icon icon="fas fa-angle-left" class="right"/>
            </p>
        </div>
    </li>
    <ul class="nav nav-treeview" :class="{ 'd-block': menuOpen }">
        <slot name="content" />
    </ul>
</template>

<style scoped>
div.nav-link {
    cursor: pointer;
}
</style>