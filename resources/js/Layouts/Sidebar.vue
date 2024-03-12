<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import NavLinkSidebar from '@/Components/NavLinkSidebar.vue';
import NavLinkParentSidebar from '@/Components/NavLinkParentSidebar.vue';

const page = usePage()
const role = page.props.auth.user.role
const currentRoute = page.props.route
const menuOpen = ref(false);
const toggleMenuOpen = function () {
    menuOpen.value = !menuOpen.value;
}
console.log(currentRoute == 'dinas.index');
</script>

<template>
    <aside class="main-sidebar sidebar-light-success elevation-4">
        <!-- Brand Logo -->
        <a href="{{ url('') }}" class="brand-link">
            <img src="../../images/favicon2.ico" id="icon-sidebar" alt="Nothing"
                class="brand-image img-circle elevation-3">
            <span class="brand-text font-weight-bold">For D One</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <li class="nav-item">
                        <div class="nav-link" :class="{ 'active': currentRoute == 'dashboard' }">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </div>
                    </li>
                    <NavLinkSidebar :role="role == 'admin'" :navIcon="'fa-solid fa-display'"> Monitoring
                    </NavLinkSidebar>
                    <NavLinkParentSidebar :role="role == 'admin'" :navIcon="'fa-solid fa-building'" :toggleMenuOpen="toggleMenuOpen">
                        <template v-slot:label> Kelola Tabel</template>

                        <template v-slot:content>
                            <NavLinkSidebar :navIcon="'fa-solid fa-list-ol'"> Daftar Tabel</NavLinkSidebar>
                            <NavLinkSidebar :role="role == 'admin'" :navIcon="'fa-solid fa-plus'"> Tambah Tabel
                            </NavLinkSidebar>
                            <NavLinkSidebar :navIcon="'fa-solid fa-recycle'"> Recycle Bin</NavLinkSidebar>
                        </template>
                    </NavLinkParentSidebar>
                    <NavLinkParentSidebar :navIcon="'fa-solid fa-building'" :toggleMenuOpen="toggleMenuOpen">

                        <template v-slot:label> Kelola Metadata</template>

                        <template v-slot:content>
                            <NavLinkSidebar :navIcon="'fa-solid fa-list-ol'"> Variabel</NavLinkSidebar>
                        </template>
                    </NavLinkParentSidebar>
                    <NavLinkSidebar :href="route('users.index')" :role="role == 'admin'" :navIcon="'fa-solid fa-users'" :currentRoute="(currentRoute == 'users.index')"> Kelola Pengguna
                    </NavLinkSidebar>
                    <NavLinkParentSidebar :role="role == 'admin'" :navIcon="'fa-solid fa-building'" :menuOpen="(currentRoute == 'dinas.index') || (currentRoute == 'dinas.create') || menuOpen" :toggleMenuOpen="toggleMenuOpen">

                        <template v-slot:label> Kelola Produsen</template>

                        <template v-slot:content>
                            <NavLinkSidebar :href="route('dinas.index')" :navIcon="'fa-solid fa-list-ol'" :currentRoute="(currentRoute == 'dinas.index')"> Daftar Produsen</NavLinkSidebar>
                            <NavLinkSidebar :href="route('dinas.create')" :navIcon="'fa-solid fa-plus'" :currentRoute="(currentRoute == 'dinas.create')"> Tambah Produsen</NavLinkSidebar>
                        </template>
                    </NavLinkParentSidebar>
                    <NavLinkParentSidebar :role="role == 'admin'" :navIcon="'fa-solid fa-building'" :toggleMenuOpen="toggleMenuOpen">

                        <template v-slot:label> Kelola Master</template>

                        <template v-slot:content>
                            <NavLinkSidebar :navIcon="'fas fa-table'"> Tabel</NavLinkSidebar>
                            <NavLinkSidebar :navIcon="'fa-solid fa-tags'"> Subjek</NavLinkSidebar>
                            <NavLinkSidebar :navIcon="'fa-solid fas fa-bars'"> Kelompok Kolom</NavLinkSidebar>
                            <NavLinkSidebar :navIcon="'fa-solid fas fa-bars'"> Kolom</NavLinkSidebar>
                            <NavLinkSidebar :navIcon="'fa-solid fas fa-th-list'"> Kelompok Kolom</NavLinkSidebar>
                            <NavLinkSidebar :navIcon="'fa-solid fas fa-th-list'"> Kolom</NavLinkSidebar>
                        </template>
                    </NavLinkParentSidebar>
                    <NavLinkSidebar :navIcon="'fas fa-user'"> Edit Profile</NavLinkSidebar>
                    <li class="nav-item">
                        <Link :href="route('home')" class="nav-link">
                        <i class="nav-icon fa-solid fa-home"></i>
                        <p class="text-bold">
                            Kembali ke Beranda
                        </p>
                        </Link>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
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