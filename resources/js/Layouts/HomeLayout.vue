<script setup>
import { Head, usePage, useForm, Link } from '@inertiajs/vue3'
import { ref } from 'vue';
const page = usePage()
const navbarStatus = ref(true);
if (page.props.auth.user) {
    navbarStatus.value = false;
}
</script>
<template>

    <body class="hold-transition">
        <!-- Site wrapper -->
        <div class="wrapper">
            <div class="text-center overlay d-none" id="spinner-border">
                <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light topbar static-top shadow" id="navbar-front">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active mx-3">
                        <Link class="nav-link text-white text-bold" :href="route('home')">Beranda</Link>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li v-if="navbarStatus" class="nav-item active mx-3">
                        <Link class="nav-link text-white text-bold" :href="route('login')">Masuk</Link>
                    </li>
                    <li v-else class="nav-item active mx-3">
                        <Link class="nav-link text-white text-bold" :href="route('home.dashboard')">Dashboard</Link>
                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper ml-0">
                <!-- Main content -->
                <section class="content-header">
                    <!-- /.container-fluid -->
                </section>
                <section class="content">
                    <!-- {{ $slot }} -->
                    <slot />
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            <footer class="main-footer ml-0">
                <div class="float-right d-none d-sm-block">
                    <b>Version</b> Beta
                </div>
                <div class="text-center">
                    <strong>Copyright &copy; 2024 <a href="https://sulut.bps.go.id">BPS Provinsi Sulawesi
                            Utara</a></strong>
                </div>
            </footer>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
        </div>
        <!-- ./wrapper -->
    </body>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>
<style scoped>

#navbar-front {
    background-color: #a80606;
}

</style>