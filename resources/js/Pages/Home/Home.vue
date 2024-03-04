<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3'

const page = usePage()


</script>
<template>
    <Head title="Home" />
    <HomeLayout>
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-7 p-0">
                    <div class="card shadow mb-4 custom-card">
                        <!-- Card Body -->
                        <div class="card-body d-flex align-items-start flex-column">
                            <div class=" mb-auto p-2"></div>
                            <div class="p-2" id="header-banner-text">
                                <div>
                                    <h1>Selamat datang di <b>For D One
                                            <br>
                                            Forum Data Online</b></h1>
                                    <p class="lead">Temukan data Pemerintah Provinsi Sulawesi Utara dengan mudah!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Area Chart -->
                    <div class="col-xl-4 col-lg-7">
                        <form action="{{ route('home.search') }}" id="search-tabel">
                            @csrf
                            <div class="mb-2">
                                <select multiple name="tahuns[]" id="tahun-select">
                                    @foreach ($tahuns as $tahun)
                                    <!-- <option value="{{ $tahun->tahun }}">{{ $tahun->tahun }}</option> -->
                                    @endforeach
                                </select>
                            </div>
                            <div class="card shadow mb-2">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold heading-card">Berdasarkan Daerah</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="card-large-homepage">
                                        @if (sizeof($wilayahs) > 0)
                                        @foreach ($wilayahs as $wilayah)
                                        <div class="row my-2 row-wilayah-homepage" id="">
                                            <input type="checkbox" id="{{ 'dinas-' . $wilayah['wilayah_fullcode'] }}"
                                                class="ml-3 kabs-checkbox" name="wilayah[]"
                                                value="{{ $wilayah['wilayah_fullcode'] }}">
                                            <label class="ml-3 mb-0 click-to-check-kabs-prov text-capitalize"
                                                data-target="{{ 'dinas-' . $wilayah['wilayah_fullcode'] }}">
                                                <!-- {{ $wilayah['label'] }} -->
                                            </label>
                                        </div>
                                        @endforeach
                                        @else
                                        <div class="row my-2 row-wilayah-homepage" id="">
                                            <label class="ml-3 mb-0 click-to-check-kabs-prov text-capitalize"
                                                data-target=""> Data belum ada yang final
                                            </label>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div id="card-kecs" class="card shadow mb-2 d-none">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold heading-card">Kecamatan</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="text-center card-overlay" id="spinner-kecamatan">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="card-small-homepage">
                                        @foreach ($kecs as $kec)
                                        <div class="row my-2 d-none row-wilayah-homepage"
                                            id="{{ $kec['wilayah_fullcode'] }}">
                                            <input type="checkbox" id="{{ 'dinas-' . $kec['wilayah_fullcode'] }}"
                                                class="ml-3 kecs-checkbox" name="kecs[]"
                                                value="{{ $kec['wilayah_fullcode'] }}">
                                            <label class="ml-3 mb-0 click-to-check-kecs text-capitalize"
                                                data-target="{{ 'dinas-' . $kec['wilayah_fullcode'] }}">
                                                <!-- {{ $kec['label'] }} -->
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div id="card-desa" class="card shadow mb-2 d-none">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold heading-card">Desa/Kelurahan</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="text-center card-overlay" id="spinner-desa">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="card-small-homepage">
                                        @foreach ($desa as $des)
                                        <div class="row my-2 d-none row-wilayah-homepage"
                                            id="{{ $des['wilayah_fullcode'] }}">
                                            <input type="checkbox" id="{{ 'dinas-' . $des['wilayah_fullcode'] }}"
                                                class="ml-3 desa-checkbox" name="desa[]"
                                                value="{{ $des['wilayah_fullcode'] }}">
                                            <label class="ml-3 mb-0 click-to-check-desa text-capitalize"
                                                data-target="{{ 'dinas-' . $des['wilayah_fullcode'] }}">
                                                <!-- {{ $des['label'] }} -->
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow mb-2">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold heading-card">Berdasarkan Produsen Data</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="card-small-homepage">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <select multiple name="dinas[]" id="dinas-select" class="form-control">
                                                    @foreach ($dinas as $din)
                                                    <!-- <option value="{{ $din['id'] }}">{{ $din['nama'] }}</option> -->
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow mb-2">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold heading-card">Berdasarkan Subjek Data</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="card-large-homepage">
                                        @foreach ($subjects as $subject)
                                        <div class="row my-2">
                                            <input type="checkbox" id="{{ 'subject-' . $subject['id'] }}" class="ml-3"
                                                name="subject[]" value="{{ $subject['id'] }}">
                                            <label class="col ml-3 mb-0 click-to-check"
                                                data-target="{{ 'subject-' . $subject['id'] }}">
                                                <!-- {{ $subject['label'] }} -->
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex">
                                <input type="text" name="searchData" placeholder="Cari dengan Kata Kunci"
                                    class="form-control mb-3 mr-2 w-75">
                                <!-- {{-- <div class="row pr-3 pb-2"> --}} -->
                                <button type="submit" class="btn ml-auto mb-3 w-25 red-bg-homepage">
                                    <div class="text-white">
                                        <i class="fa-brands fa-searchengin"></i> Cari
                                    </div>
                                </button>
                                <!-- {{-- </div> --}} -->
                            </div>
                        </form>
                    </div>

                    <!-- Pie Chart -->
                    <div class="col-xl-8 col-lg-5">
                        <div class="card shadow" id="tabel-list">
                            @include('tabel-list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
<style>
#navbar-front {
    background-color: #a80606;
}

.red-bg-homepage {
    background-color: #a80606;
}

#header-banner-text {
    margin-top: 40px;
    color: whitesmoke;
}

.heading-card {
    color: #a80606;
}

.card-large-homepage {
    height: 200px;
    overflow-y: scroll;
    overflow-x: hidden;
    color: #333333;
}

.card-small-homepage {
    height: auto;
    color: #333333;
}

.row-wilayah-homepage {
    min-width: 50vw;
}

#list-tabel-card {
    height: 720px;
    overflow-y: scroll;
}

.smalltext-homepage {
    font-size: 12px;
}

#subjek-weight {
    font-weight: 500;
}

#icon-sidebar {
    opacity: 0.8;
}

.custom-card {
    background-image: url("../images/banner.png");
    height: 300px;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: 50% 50%;
}
</style>