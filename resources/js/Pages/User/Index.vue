<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import SpinnerBorder from '@/Components/SpinnerBorder.vue';
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import { getPagination } from '@/pagination'
import { Head, usePage, Link, useForm } from '@inertiajs/vue3'
import { onMounted, ref, watch, onUpdated } from 'vue';
import { clickSortProperties } from '@/sortAttribute';

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
const triggerSpinner = ref(false)
const deleteModalStatus = ref(false)

//pagination
const tabelUser = ref(null)
const statusText = ref(false)
const maxRows = ref(null)
const currentPagination = ref(null)

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
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            form.reset
        },
        onBefore: function () { triggerSpinner.value = true },
        onFinish: function () { triggerSpinner.value = false }
    })
}

const editUser = function (id) {
    form.id = id
    form.get(route('users.edit'), {
        onBefore: function () { triggerSpinner.value = true },
        onFinish: function () { triggerSpinner.value = false }
    })
}

const toggleDeleteModal = function (id) {
    deleteModalStatus.value = true
    form.id = id
}

const changeRoles = function (roles) {
    if (roles == 'produsen') {
        return "fa-graduation-cap"
    } else if (roles == 'kominfo') {
        return "fa-computer"
    } else return "fa-user-tie"
}

const ArrayBigObjects = [
    { key: 'username', valueFilter: searchUsername },
    { key: 'name', valueFilter: searchNama },
    { key: 'nama_dinas', valueFilter: searchInstansi },
    { key: 'wilayah_label', valueFilter: searchWilayah },
    { key: 'noHp', valueFilter: searchNoHp },
    { key: 'role', valueFilter: searchRole },
]
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
    if (filters.length === 0) {
        users.value = uObject
        return
    }
    users.value = uObject.filter(item => {
        return filters.every(obj => {
            const filterValue = obj.valueFilter.value.toLowerCase()
            return item[obj.key].toLowerCase().includes(filterValue)
        })
    })
})
onMounted(function () {
    //flash
    if (page.props.flash.message) toggleFlash.value = true

    //pagination
    let currentStatusText = statusText.value
    var rowsTabel = tabelUser.value.querySelectorAll('tbody tr').length
    getPagination(tabelUser, currentPagination, 10, statusText,
        currentStatusText, rowsTabel)
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelUser, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
onUpdated(() => {
    uObject = page.props.users
    users = ref(uObject)
    let currentStatusText = statusText.value
    var rowsTabel = tabelDinas.value.querySelectorAll('tbody tr').length
    currentStatusText.querySelector('#showTotal').textContent = rowsTabel
    if (maxRows.value.value > rowsTabel) {
        currentStatusText.querySelector('#showPage').textContent = rowsTabel
    } else {
        currentStatusText.querySelector('#showPage').textContent = maxRows.value.value
    }
    maxRows.value.addEventListener("change", function (e) {
        let valueChanged = this.value
        getPagination(tabelUser, currentPagination, valueChanged, statusText,
            currentStatusText, rowsTabel)
    })
})
const deleteForm = function () {
    form.post(route('users.delete'), {
        onSuccess: function () {
            if (page.props.flash.message) toggleFlash.value = true
            form.reset()
        },
        onBefore: function () {
            deleteModalStatus.value = false
            triggerSpinner.value = true
        },
        onFinish: function () {
            triggerSpinner.value = false
        },
        onError: function () { deleteModalStatus.value = true }
    })
}
</script>
<template>

    <Head title="Daftar Pengguna" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div class="container-fluid">
            <div class="mb-2 d-flex">
                <div class="h4 flex-grow-1">
                    Daftar Pengguna
                </div>
                <a href="#" class="btn bg-success-fordone mr-2" title="Download" data-target="#downloadModal"
                    data-toggle="modal"><i class="fa-solid fa-circle-down"></i></a>
                <Link :href="route('users.create')" class="btn bg-info-fordone"><i class="fa-solid fa-plus"></i>
                Tambah Pengguna Baru</Link>
            </div>
        </div>
        <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
        <table class="table table-sorted table-hover table-bordered table-search" ref="tabelUser" id="tabel-user">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column" @click="clickSortProperties(users, 'number')">No.</th>
                    <th class="text-center" @click="clickSortProperties(users, 'username')">Username</th>
                    <th class="text-center tabel-width-15" @click="clickSortProperties(users, 'name')">Nama</th>
                    <th class="text-center tabel-width-20" @click="clickSortProperties(users, 'nama_dinas')">Nama
                        Instansi</th>
                    <th class="text-center tabel-width-20" @click="clickSortProperties(users, 'wilayah_label')">Wilayah
                        Kerja
                    </th>
                    <th class="text-center" @click="clickSortProperties(users, 'noHp')">No. HP</th>
                    <th class="text-center" @click="clickSortProperties(users, 'role')">Peran</th>
                    <th class="text-center deleted tabel-width-8">Edit</th>
                    <th class="text-center deleted">Hapus</th>
                </tr>
                <tr class="">
                    <td class="search-header"></td>
                    <td class="search-header"><input v-model.trim="searchUsername" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchNama" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchInstansi" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchWilayah" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchNoHp" type="text"
                            class="search-input form-control"></td>
                    <td class="search-header"><input v-model.trim="searchRole" type="text"
                            class="search-input form-control"></td>
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
                        <a @click.prevent="changeRolesLink(user.id)" class="mx-1 role-update"><i class="fa-solid"
                                :class="changeRoles(user.role)" title="Ubah Role"></i></a>
                        <a @click.prevent="editUser(user.id)" class="edit-pen mx-1">
                            <i class="fa-solid fa-pencil" title="Edit Pengguna"></i>
                        </a>
                    </td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleDeleteModal(user.id)" class="delete-trash">
                            <i class="fa-solid fa-trash-can icon-trash-color"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <Teleport to="body">
            <ModalBs :ModalStatus="deleteModalStatus" @close="deleteModalStatus = false" :title="'Hapus Pengguna'">
                <template v-slot:modalBody>
                    <label>Apakah Anda yakin akan menghapus akun Pengguna ini?</label>
                </template>
                <template v-slot:modalFunction>
                    <button type="button" class="btn btn-sm badge-status-empat" :disabled="form.processing"
                        @click.prevent="deleteForm">Hapus</button>
                </template>
            </ModalBs>
        </Teleport>
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
<style scoped>
.role-update {
    cursor: pointer;
}
</style>