<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    navIcon: {
        type: String,
    },
    role: {
        type: Boolean,
        default: true,
    },
    href: {
        type: String,
        default: '#'
    },
    currentRoute: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <li v-if="role" class="nav-item">
        <Link :href="href" class="nav-link" :class="{ 'active': currentRoute }">
        <i class="nav-icon" :class="navIcon"></i>
        <p>
            <slot />
        </p>
        </Link>
    </li>
</template>
<style scoped>
div.nav-link {
    cursor: pointer;
}
.sidebar-dark-success .nav-sidebar>.nav-item>.nav-link.active,
.sidebar-light-success .nav-sidebar>.nav-item>.nav-link.active {
    background-color: #3d3b8e;
    color: #fff;
}
</style>