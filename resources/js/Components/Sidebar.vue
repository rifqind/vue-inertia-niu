<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage()
const role = page.props.auth.user.role
</script>
<template>
    <aside class="main-sidebar sidebar-light-success elevation-4">
        <!-- Brand Logo -->
        <a href="{{ url('') }}" class="brand-link">
            <img src="../../images/favicon2.ico" id="icon-sidebar" alt="Nothing" class="brand-image img-circle elevation-3">
            <span class="brand-text font-weight-bold">For D One</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ url('dashboard') }}"
                            class="nav-link {{ Request::is('/', 'dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>
                    <li v-if="role == 'admin' | role == 'kominfo'" class="nav-item">
                        <a href="{{ url('monitoring') }}" class="nav-link">
                            <i class="nav-icon fa-solid fa-display"></i>
                            <p>
                                Monitoring
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="nav-icon fa-solid fa-building"></i>
                            <p>
                                Kelola Tabel
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('tabel.index') }}"
                                    class="nav-link {{ Request::is('tabel/index') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-list-ol"></i>
                                    <p>
                                        Daftar Tabel
                                    </p>
                                </a>
                            </li>
                            <li v-if="role == 'admin'" class="nav-item">
                                <a href="{{ route('tabel.create') }}"
                                    class="nav-link {{ Request::is('tabel/create') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-plus"></i>
                                    <p>
                                        Tambah Tabel
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('tabel.deletedList') }}"
                                    class="nav-link {{ Request::is('tabel/deletedList') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-recycle"></i>
                                    <p>
                                        Recycle Bin
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="nav-icon fa-solid fa-building"></i>
                            <p>
                                Kelola Metadata
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('metavar.index') }}"
                                    class="nav-link {{ Request::is('metavar/index') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-list-ol"></i>
                                    <p>
                                        Variabel
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li v-if="role == 'admin'" class="nav-item">
                        <a href="{{ url('user/index') }}" class="nav-link">
                            <i class="nav-icon fa-solid fa-users"></i>
                            <p>
                                Kelola Pengguna
                            </p>
                        </a>
                    </li>
                    <li v-if="role == 'admin'" class="nav-item">
                        <Link class="nav-link">
                            <i class="nav-icon fa-solid fa-building"></i>
                            <p class="">
                                Kelola Produsen
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </Link>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('dinas/index') }}"
                                    class="nav-link {{ Request::is('dinas/index') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-list-ol"></i>
                                    <p>
                                        Daftar Produsen
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('dinas/create') }}"
                                    class="nav-link {{ Request::is('dinas/create') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-plus"></i>
                                    <p>
                                        Tambah Produsen
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li v-if="role == 'admin'" class="nav-item">
                        <a href="" class="nav-link">
                            <i class="nav-icon fa-solid fa-building"></i>
                            <p>
                                Kelola Master
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('tabel.master') }}" class="nav-link">
                                    <i class="nav-icon fas fa-table"></i>
                                    <p>
                                        Tabel
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('subject/index') }}" class="nav-link">
                                    <i class="nav-icon fa-solid fa-tags"></i>
                                    <p> Subjek</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('column-group/index') }}" class="nav-link">
                                    <i class="nav-icon fa-solid fas fa-bars"></i>
                                    <p> Kelompok Kolom</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('column/index') }}" class="nav-link">
                                    <i class="nav-icon fa-solid fas fa-bars"></i>
                                    <p> Kolom</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('rowLabels/index') }}" class="nav-link">
                                    <i class="nav-icon fa-solid fas fa-th-list"></i>
                                    <p> Kelompok Baris</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('rows/index') }}" class="nav-link">
                                    <i class="nav-icon fa-solid fas fa-th-list"></i>
                                    <p> Baris</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users.edit') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Edit Profile
                            </p>
                        </a>
                    </li>
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