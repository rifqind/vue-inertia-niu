<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { getPagination } from '@/pagination'
import { Head, usePage, Link, useForm } from '@inertiajs/vue3'
import { onMounted, ref, watch } from 'vue';

const page = usePage()
var uObject = page.props.users
var users = ref(uObject) 
const toggleFlash = ref(false)
const searchUsername = ref(null)
const searchNama = ref(null)
const searchInstansi = ref(null)
const searchWilayah = ref(null)
const searchNoHp = ref(null)
const searchRole = ref(null)

//pagination
const statusText = ref(false)
const tabelUser = ref(null)
const maxRows = ref(null)
const currentPagination = ref(null)

const hideFlashMessage = function () {
    setInterval(() => {
        toggleFlash.value = false
    }, 2000);
}
const form = useForm({
    id: null,
})

const resetPasswordLink = function (id) {
    form.id = id
    form.get(route('users.reset'), {
        onSuccess: function () { form.reset }
    })
}

const changeRolesLink = function (id) {
    form.id = id
    form.post(route('users.roleChange'), {
        onSuccess: function () { form.reset }
    })
}

const changeRoles = function (roles) {
    if (roles == 'produsen') {
        return "fa-graduation-cap"
    } else if (roles == 'kominfo') {
        return "fa-computer"
    } else return "fa-user-tie"
}
var watchArrays = [searchUsername, searchNama, searchInstansi, searchWilayah, searchNoHp, searchRole]
watch(watchArrays, function(){
    console.log(watchArrays);
    let filters = watchArrays.filter(filter => filter.value)
    if (filters.length === 0) {
        users.value = uObject
        return
    }
    
    users.value = uObject.filter(item => {
        // console.log(item);
        return filters.every(filter => {
            // console.log(filters)
            // return item[filter.property].toLowerCase().includes(filter.value.toLowerCase())
        })
    })
})
onMounted(function () {
    //flash
    if (page.props.flash.message) toggleFlash.value = true
    hideFlashMessage()

    //pagination
    let currentStatusText = statusText.value
    var rowsTabel = tabelUser.value.querySelectorAll('tbody tr').length
    getPagination(tabelUser, currentPagination, 10, statusText,
        currentStatusText, rowsTabel)
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelDinas, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
</script>
<template>

    <Head title="Daftar Pengguna" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Pengguna
                </div>
                <a href="#" class="btn bg-success-fordone mr-2" title="Download" data-target="#downloadModal"
                    data-toggle="modal"><i class="fa-solid fa-circle-down"></i></a>
                <Link :href="route('users.create')" class="btn bg-info-fordone"><i class="fa-solid fa-plus"></i>
                Tambah Pengguna Baru (Belom)</Link>
            </div>
        </div>
        <div class="alert alert-success" v-if="toggleFlash" role="alert">
            {{ page.props.flash.message }}
        </div>
        <table class="table table-sorted table-hover table-bordered table-search" ref="tabelUser" id="tabel-user">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column">No.</th>
                    <th class="text-center">Username</th>
                    <th class="text-center tabel-width-15">Nama</th>
                    <th class="text-center tabel-width-20">Nama Instansi</th>
                    <th class="text-center tabel-width-20">Wilayah Kerja</th>
                    <th class="text-center">No. HP</th>
                    <th class="text-center">Peran</th>
                    <th class="text-center deleted tabel-width-5">Edit</th>
                    <th class="text-center deleted">Hapus</th>
                </tr>
                <tr class="">
                    <td class="search-header"></td>
                    <td class="search-header"><input v-model.trim="searchUsername" type="text" class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchNama" type="text" class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchInstansi" type="text" class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchWilayah" type="text" class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchNoHp" type="text" class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchRole" type="text" class="search-input form-control"></td>
                    <td class="search-header deleted"></td>
                    <td class="search-header deleted"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.number }}</td>
                    <td>{{ user.username }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.nama_dinas }}</td>
                    <td>{{ user.wilayah_label }}</td>
                    <td>{{ user.noHp }}</td>
                    <td>{{ user.role }}</td>
                    <td class="text-center deleted">
                        <a @click.prevent="resetPasswordLink(user.id)" class="update-pen mx-1">
                            <i class="fa-solid fa-lock" title="Reset Password"></i>
                        </a>
                        <a @click.prevent="changeRolesLink(user.id)" class="mx-1 role-update"><i class="fa-solid" :class="changeRoles(user.role)" title="Ubah Role"></i></a>
                        <a href="" class="edit-pen mx-1">
                            <i class="fa-solid fa-pencil" title="Edit Pengguna"></i>
                        </a>
                    </td>
                    <td class="text-center deleted">
                        <a href="" class="delete-trash">
                            <i class="fa-solid fa-trash-can icon-trash-color"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-end align-items-center">
            <div id="statusText" ref="statusText" class="mb-3 mx-3 ml-auto">Menampilkan <span id="showPage"></span> dari
                <span id="showTotal"></span>
            </div>
            <div class="form-group"> <!--		Show Numbers Of Rows 		-->
                <select class="form-control" ref="maxRows" name="state" id="maxRows">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="pagination-container">
                <nav>
                    <ul class="pagination" id="currentPagination" ref="currentPagination">
                        <li data-page="prev" id="next">
                            <span>
                                < <span class="sr-only">(current)
                            </span></span>
                        </li>
                        <!--	Here the JS Function Will Add the Rows -->
                        <li data-page="next" id="prev">
                            <span> > <span class="sr-only">(current)</span></span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </GeneralLayout>
</template>