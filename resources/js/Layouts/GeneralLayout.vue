<script setup>
import Footer from '@/Components/Footer.vue';
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Layouts/Sidebar.vue';


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
<style src="@vueform/multiselect/themes/default.css"></style>
<style>

.table-sorted th {
    cursor: pointer;
}

.table-sorted .th-sort-asc::after {
    content: "\25b4";
}

.table-sorted .th-sort-desc::after {
    content: "\25be";
}

.table-sorted .th-sort-asc::after,
.table-sorted .th-sort-desc::after {
    margin-left: 5px;
}

.table-sorted .th-sort-asc,
.table-sorted .th-sort-desc {
    background: rgba(0, 0, 0, 0.1);
}

.pagination>li {
    padding-top: 6px;
    padding-right: 12px;
    padding-bottom: 6px;
    padding-left: 12px;
}

.pagination>li.active {
    color: whitesmoke;
    background-color: #17a2b8;
}

.pagination>li:hover {
    cursor: pointer;
}

.bg-success-fordone {
    background-color: #1D845B;
    color: whitesmoke;
}

.bg-success-fordone:hover {
    background-color: #385d4e;
    color: whitesmoke;
}

.bg-info-fordone {
    background-color: #3d3b8e;
    color: whitesmoke;
}

.bg-info-fordone:hover{
    background-color: #434272;
    color: whitesmoke;
}

.bg-primary-fordone {
    background-color: #2E7AE6;
    color: whitesmoke;
}

.badge-status-satu {
    background-color: #7286a0;
    color: whitesmoke;
}

.badge-status-dua {
    background-color: #03254e;
    color: whitesmoke;
}

.badge-status-tiga {
    background-color: #f18f01;
    color: whitesmoke;
}

.badge-status-empat {
    background-color: #8B1E3F;
    color: whitesmoke;
}

.badge-status-lima {
    background-color: green;
    color: whitesmoke;
}
.icon-trash-color {
    color: #9a091f;
}
.update-pen {
    cursor: pointer;
}
.delete-trash {
    cursor: pointer;
}
.table thead tr th {
    background-color: #3d3b8e;
    color: whitesmoke;
}
</style>
