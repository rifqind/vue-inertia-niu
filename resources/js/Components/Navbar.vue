<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const page = usePage()
const logoutDropdown = ref(false);
const toggleDropdown = function() {
    logoutDropdown.value = !logoutDropdown.value;
}
const form = useForm({})
const submit = function() {
    form.post(route('logout'))
}
</script>
<template>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><font-awesome-icon icon="fas fa-bars"/></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" @click="toggleDropdown" id="navbarDropdownMenuLink" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ page.props.auth.user.name }}
                </a>
                <div class="dropdown-menu d-block dropdown-menu-right" v-if="logoutDropdown" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item"><font-awesome-icon icon="nav-icon fas fa-user"/>
                        Profile</a>

                    <form @submit.prevent="submit" id="logoutButton">
                        <button type="submit" class="dropdown-item" href=""><font-awesome-icon icon="nav-icon fas fa-sign-out-alt"/> Log
                            Out</button>
                    </form>
                    <!-- <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                        Keluar
                    </ResponsiveNavLink> -->
                </div>
            </li>
        </ul>
    </nav>
</template>