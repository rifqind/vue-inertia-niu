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
    }
})
</script>

<template>
    <li v-if="role" class="nav-item" :class="{ 'menu-is-open menu-open': menuOpen }">
        <div class="nav-link" @click="toggleMenuOpen">
            <i class="nav-icon" :class="navIcon"></i>
            <p>
                <slot name="label" />
                <i class="fas fa-angle-left right"></i>
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