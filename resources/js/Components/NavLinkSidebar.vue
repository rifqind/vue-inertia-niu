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
    }
})
</script>

<template>
    <li v-if="role" class="nav-item">
        <Link :href="href" class="nav-link">
            <i class="nav-icon" :class="navIcon"></i>
            <p>
                <slot />
            </p>
        </Link>
    </li>
</template>
<style scoped>
div.nav-link{
    cursor: pointer;
}
</style>