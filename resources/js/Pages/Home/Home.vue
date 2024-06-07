<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import TabelListTimeline from '@/Components/TabelListTimeline.vue'
import SpinnerBorder from '@/Components/SpinnerBorder.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3'
import Multiselect from '@vueform/multiselect';
import { onMounted, ref, defineComponent, computed } from 'vue';

defineComponent({
    Multiselect
})
const page = usePage()
const wilayahCheckBox = ref([])
const subjectCheckBox = ref([])
const kecamatanCheckBox = ref([])
const desaCheckBox = ref([])
const wilayahSelected = ref([])
const subjectSelected = ref([])
const kecamatanSelected = ref([])
const desaSelected = ref([])
const triggerSpinner = ref(false)
const visibleKecs = ref([])
const visibleDesa = ref([])
const updateResult = ref(false)
var dataFiltered = ref(page.props.tabels)
const countDataFiltered = ref(page.props.counttabels)
const form = useForm({
    year: [],
    wilayah: [],
    kec: [],
    desa: [],
    dinas: ['all'],
    subject: [],
    label: [],
})
var all = [{ label: 'Pilih Semua', value: 'all' }]
const yearDrop = ref({
    value: ['all'],
    options: [...all, ...page.props.tahuns]
})
const dinasDrop = ref({
    value: ['all'],
    options: [...all, ...page.props.dinas]
})
visibleKecs.value = page.props.kecs.map((kec, index) => ({
    index: kec.index,
    parent_code: kec.parent_code,
    value: false
}))
visibleDesa.value = page.props.desa.map((desa, index) => ({
    index: desa.index,
    parent_code: desa.parent_code,
    value: false
}))
const isVisibleKecs = (parent_code) => {
    const theIndex = visibleKecs.value.findIndex(x => x.parent_code === parent_code)
    return visibleKecs.value[theIndex].value
}
const isVisibleDesa = (parent_code) => {
    const theIndex = visibleDesa.value.findIndex(x => x.parent_code === parent_code)
    return visibleDesa.value[theIndex].value
}
const toggleCheck = function (index, object) {
    object[index] = !object[index];
};
const submit = () => {
    dataFiltered.value = page.props.tabels
    triggerSpinner.value = true
    wilayahSelected.value = page.props.wilayahs.filter((_, index) => {
        return wilayahCheckBox.value[index]
    })
    subjectSelected.value = page.props.subjects.filter((_, index) => {
        return subjectCheckBox.value[index]
    })
    kecamatanSelected.value = page.props.kecs.filter((_, index) => {
        return kecamatanCheckBox.value[index]
    })
    desaSelected.value = page.props.desa.filter((_, index) => {
        return desaCheckBox.value[index]
    })
    let searchWilayah = []
    searchWilayah = [...wilayahSelected.value]
    if (kecamatanSelected.value.length > 0) {
        let parentDeleted = kecamatanSelected.value.map(obj => obj.wilayah_fullcode.substring(0, 4) + '000000')
        searchWilayah = [...searchWilayah, ...kecamatanSelected.value]
        searchWilayah = searchWilayah.filter(item => !parentDeleted.includes(item.wilayah_fullcode))
    }
    if (desaSelected.value.length > 0) {
        let parentDeleted = desaSelected.value.map(obj => obj.wilayah_fullcode.substring(0, 7) + '000')
        searchWilayah = [...searchWilayah, ...desaSelected.value]
        searchWilayah = searchWilayah.filter(item => !parentDeleted.includes(item.wilayah_fullcode))
    }
    let isDinasAll = false
    let isYearAll = false
    if (form.dinas.includes('all')) {
        form.dinas.splice(form.dinas.indexOf('all'), 1)
        isDinasAll = true
    }
    if (form.year.includes('all')) {
        form.year.splice(form.year.indexOf('all'), 1)
        isYearAll = true
    }
    form.wilayah = searchWilayah.map(obj => obj.wilayah_fullcode)
    form.subject = subjectSelected.value.map(obj => obj.id)
    form.kec = kecamatanSelected.value.map(obj => obj.wilayah_fullcode)
    form.desa = desaSelected.value.map(obj => obj.wilayah_fullcode)
    const ArrayBigObjects = [
        { key: 'tahun', valueFilter: form.year },
        { key: 'kode_wilayah', valueFilter: form.wilayah },
        { key: 'id_dinas', valueFilter: form.dinas },
        { key: 'id_subjek', valueFilter: form.subject },
        { key: 'label', valueFilter: form.label }
    ]
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter)
    let result = dataFiltered.value.filter(item => {
        let isValid = true;
        filters.forEach(filter => {
            const tempt = filter.valueFilter;
            if (filter.key == 'label' && tempt.length > 0) {
                if (!item[filter.key].toLowerCase().includes(tempt.toLowerCase())) isValid = false
            } else {
                if (tempt.length > 0 && !tempt.includes(item[filter.key])) isValid = false
            }
        });
        return isValid;
    });
    dataFiltered.value = result
    countDataFiltered.value = result.length
    updateResult.value = false
    setTimeout(() => {
        if (isYearAll) form.year.push('all')
        if (isDinasAll) form.dinas.push('all')
        triggerSpinner.value = false
    }, 500);
}
const reset = () => {
    triggerSpinner.value = true
    form.reset()
    wilayahSelected.value = []
    subjectSelected.value = []
    kecamatanSelected.value = []
    desaSelected.value = []

    if (wilayahCheckBox.value.length > 0) wilayahCheckBox.value = Array(wilayahCheckBox.value.length).fill(false)
    if (kecamatanCheckBox.value.length > 0) kecamatanCheckBox.value = Array(kecamatanCheckBox.value.length).fill(false)
    if (desaCheckBox.value.length > 0) desaCheckBox.value = Array(desaCheckBox.value.length).fill(false)
    if (subjectCheckBox.value.length > 0) subjectCheckBox.value = Array(subjectCheckBox.value.length).fill(false)
    dataFiltered.value = page.props.tabels
    countDataFiltered.value = dataFiltered.value.length
    updateResult.value = false
    setTimeout(() => {
        triggerSpinner.value = false
    }, 500);
}
var count_kabupaten = []
var count_kecamatan = []
const handleChangeCheckBoxKec = (index, object) => {
    let kecamatanValue = page.props.kecs[index].wilayah_fullcode.substring(2, 7)
    let desaValue = findDesa(page.props.desa, kecamatanValue)
    if (object[index]) {
        desaValue.forEach((item) => {
            count_kecamatan.push(item.parent_code)
        })
        count_kecamatan.forEach((item) => {
            const theIndex = visibleDesa.value.findIndex(x => x.parent_code === item)
            if (theIndex !== -1) visibleDesa.value[theIndex].value = true
        })
    } else {
        let removeDesa = []
        desaValue.forEach((item) => {
            removeDesa.push(item.parent_code)
        })
        removeDesa.forEach((item) => {
            const theIndex = visibleDesa.value.findIndex(x => x.parent_code === item)
            if (theIndex !== -1) visibleDesa.value[theIndex].value = false; desaCheckBox.value[theIndex] = false
        })
        count_kecamatan = count_kecamatan.filter((item) => !removeDesa.includes(item))
    }
}
const handleChangeCheckBoxKab = function (index, object) {
    let kabupatenValue = page.props.wilayahs[index].wilayah_fullcode.substring(2, 4)
    let kecamatanValue = findKecamatan(page.props.kecs, kabupatenValue)
    if (object[index]) {
        kecamatanValue.forEach((item) => {
            count_kabupaten.push(item.parent_code)
        })
        count_kabupaten.forEach((item) => {
            const theIndex = visibleKecs.value.findIndex(x => x.parent_code === item)
            if (theIndex !== -1) visibleKecs.value[theIndex].value = true
        })
    }
    else {
        let removeKecamatan = []
        kecamatanValue.forEach((item) => {
            removeKecamatan.push(item.parent_code)
        })
        let desaRemove = kecamatanValue.map((obj) => ({
            parent_code: obj.wilayah_fullcode.substring(2, 7)
        }))
        desaRemove.forEach((item) => {
            const theIndex = visibleDesa.value.findIndex(x => x.parent_code === item.parent_code)
            if (theIndex !== -1) visibleDesa.value[theIndex].value = false; desaCheckBox.value[theIndex] = false
        })
        removeKecamatan.forEach((item) => {
            const theIndex = visibleKecs.value.findIndex(x => x.parent_code === item)
            if (theIndex !== -1) visibleKecs.value[theIndex].value = false; kecamatanCheckBox.value[theIndex] = false
        })
        count_kabupaten = count_kabupaten.filter((item) => !removeKecamatan.includes(item))
    }
}
function findKecamatan(array, parent) {
    let kecamatan = [];
    for (let key in array) {
        if (array.hasOwnProperty(key) && array[key].parent_code === parent) {
            kecamatan.push(array[key]);
        }
    }
    return kecamatan;
}
function findDesa(array, parent) {
    let desa = [];
    for (let key in array) {
        if (array.hasOwnProperty(key) && array[key].parent_code === parent) {
            desa.push(array[key]);
        }
    }
    return desa;
}
const showCard = (targetVisible) => {
    return targetVisible.some(x => x.value === true)
}
const updateResultResults = (value) => {
    updateResult.value = value
}
</script>
<template>

    <Head title="Home" />
    <SpinnerBorder v-if="triggerSpinner" />
    <HomeLayout>
        <div class="container">
            <div class="col-xl-12 col-lg-7 p-0">
                <div class="card shadow mb-4 custom-card">
                    <!-- Card Body -->
                    <div class="card-body d-flex align-items-start flex-column">
                        <div class=" mb-auto p-2"></div>
                        <div class="p-2" id="header-banner-text">
                            <div>
                                <h1 class="adjust-text-1">Selamat datang di <b>For D One
                                        <br>
                                        Forum Data Online</b></h1>
                                <p class="lead">Temukan data Pemerintah Provinsi Sulawesi Utara dengan mudah!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row p-0">
                <!-- Area Chart -->
                <div class="col-xl-4 col-lg-7">
                    <form id="search-tabel">
                        <div class="mb-2">
                            <Multiselect :options="yearDrop.options" v-model="form.year" :searchable="true" mode="tags"
                                placeholder="-- Pilih Tahun --" />
                        </div>
                        <div class="card shadow mb-2">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold heading-card">Berdasarkan Daerah</h6>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <div class="card-large-homepage">
                                    <div v-if="page.props.wilayahs.length > 0"
                                        v-for="(node, index) in page.props.wilayahs" class="my-2 d-flex">
                                        <input :id="'checkbox-wilayah-' + index"
                                            @change="handleChangeCheckBoxKab(index, wilayahCheckBox)" type="checkbox"
                                            ref="checkbox" v-model="wilayahCheckBox[index]" :value="index">
                                        <label :for="'checkbox-wilayah-' + index" class="ml-3 mb-0 text-capitalize"
                                            :title="node.label">{{ node.label
                                            }}</label>
                                    </div>
                                    <div class="my-2" v-else>
                                        <label class="ml-3 mb-0 text-capitalize" data-target=""> Data belum ada yang
                                            final
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="showCard(visibleKecs)" id="card-kecs" class="card shadow mb-2">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold heading-card">Kecamatan</h6>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <div class="card-small-homepage">
                                    <template v-for="(node, index) in page.props.kecs" :key="index">
                                        <div v-if="isVisibleKecs(node.parent_code)" class="d-flex my-2" id="">
                                            <input type="checkbox" :id="'checkbox-kecs-' + index"
                                                @change="handleChangeCheckBoxKec(index, kecamatanCheckBox)"
                                                v-model="kecamatanCheckBox[index]">
                                            <label class="ml-3 mb-0 text-capitalize" :for="'checkbox-kecs-' + index"
                                                :title="node.label">
                                                {{ node.label }}
                                            </label>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div v-if="showCard(visibleDesa)" id="card-desa" class="card shadow mb-2">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold heading-card">Desa/Kelurahan</h6>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <!-- <div class="text-center card-overlay" id="spinner-desa">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div> -->
                                <div class="card-small-homepage">
                                    <template v-for="(node, index) in page.props.desa" :key="index">
                                        <div v-if="isVisibleDesa(node.parent_code)" class="d-flex my-2" id="">
                                            <input type="checkbox" :id="'checkbox-desa-' + index" class="desa-checkbox"
                                                v-model="desaCheckBox[index]">
                                            <label class="ml-3 mb-0 text-capitalize" :for="'checkbox-desa-' + index"
                                                :title="node.label">
                                                {{ node.label }}
                                            </label>
                                        </div>
                                    </template>
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
                                            <Multiselect :options="dinasDrop.options" v-model="form.dinas"
                                                placeholder="-- Pilih Produsen Data --" :searchable="true"
                                                mode="tags" />
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
                                    <div v-if="page.props.subjects.length > 0"
                                        v-for="(node, index) in page.props.subjects" class="my-2 d-flex">
                                        <input type="checkbox" ref="checkbox" v-model="subjectCheckBox[index]"
                                            :value="index">
                                        <label @click="toggleCheck(index, subjectCheckBox)"
                                            class="ml-3 mb-0 text-capitalize" :title="node.label">{{ node.label
                                            }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex">
                            <input type="text" v-model="form.label" name="searchData"
                                placeholder="Cari dengan Kata Kunci" class="form-control mb-3 mr-2 w-75">
                            <!-- {{-- <div class="row pr-3 pb-2"> --}} -->
                            <div @click.prevent="submit" class="btn ml-auto mb-3 w-25 button-search">
                                <div class="text-white">
                                    <font-awesome-icon icon="fa-brands fa-searchengin" /> Cari
                                </div>
                            </div>
                            <div class="btn button-search mb-3 ml-1" @click="reset" title="Reset">
                                <font-awesome-icon icon="fa-solid fa-rotate-right" />
                            </div>
                            <!-- {{-- </div> --}} -->
                        </div>
                    </form>
                </div>

                <!-- Pie Chart -->
                <div class="col-xl-8 col-lg-5">
                    <div class="card shadow" id="tabel-list">
                        <TabelListTimeline :count-tabels="countDataFiltered" :data="dataFiltered"
                            :update-result="updateResult" @update:update-result="updateResultResults" />
                    </div>
                </div>
            </div>

        </div>
    </HomeLayout>
</template>
<style>
.multiselect-tag {
    background: #a80606 !important;
}

.red-bg-homepage {
    background-color: #a80606;
}

.button-search {
    background-color: #a80606;
    color: whitesmoke;
}

.button-search:hover {
    background-color: #724452;
    color: whitesmoke;
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

/* .row-wilayah-homepage {
    min-width: 50vw;
} */

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
    /* background-image: url("../images/banner.png"); */
    background-image: url("../../../images/banner.png");
    height: 300px;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: 50% 50%;
}

.adjust-text-1 {
    font-size: 2em;
}
</style>