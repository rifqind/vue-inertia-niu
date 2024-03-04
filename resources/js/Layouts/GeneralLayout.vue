<script setup>
import Footer from '@/Components/Footer.vue';
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
// import 'admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css';
</script>
<template>
    <body class="hold-transition layout-fixed">
        <!-- Site wrapper -->
        <div class="wrapper">
            <div class="text-center overlay d-none" id="spinner-border">
                <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <!-- Navbar -->
            <!-- @include('layouts.modal') -->

            <Navbar />
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <!-- @include('layouts.sidebar') -->
            <Sidebar />

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- {{ -- < div class="loader d-none" >
                        <div class="spinner-grow text-success" > </div>
                            < div class="spinner-grow text-success" > </div>
                                < div class="spinner-grow text-success" > </div>
                                    < div class="spinner-grow text-success" > </div>
                                        < /div> --}} -->

                    <div class="container-fluid">
                        <!-- {{ $slot }} -->
                        <slot />
                    </div>

                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <Footer />

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
        </div>
        <!-- ./wrapper -->

        <!-- jQuery -->
    </body>
</template>
