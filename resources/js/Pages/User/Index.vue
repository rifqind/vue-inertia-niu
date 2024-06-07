<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import SpinnerBorder from '@/Components/SpinnerBorder.vue';
import ModalBs from '@/Components/ModalBs.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Pagination from '@/Components/Pagination.vue'
import { Head, usePage, Link, useForm } from '@inertiajs/vue3'
import { onMounted, ref, watch } from 'vue';
import { clickSortProperties } from '@/sortAttribute';
import { GoDownload } from '@/download'
import { computed } from 'vue';

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
const downloadTitle = ref(null)
const triggerSpinner = ref(false)
const deleteModalStatus = ref(false)
const downloadModalStatus = ref(false)

//pagination
const tabelUser = ref(null)

const form = useForm({
    id: null,
    _token: null,
})

const resetPasswordLink = function (id) {
    form.id = id
    form.get(route('users.reset'), {
        onSuccess: function () { form.reset }
    })
}

const changeRolesLink = async function (id) {
    form.id = id
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
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
        onFinish: function () { triggerSpinner.value = false },
        onError: function () { triggerSpinner.value = false },
    })
}

const toggleDeleteModal = function (id) {
    deleteModalStatus.value = true
    form.id = id
}

const changeRoles = function (roles) {
    if (roles == 'produsen') {
        return "fa-solid fa-graduation-cap"
    } else if (roles == 'kominfo') {
        return "fa-solid fa-computer"
    } else return "fa-solid fa-user-tie"
}

const ArrayBigObjects = [
    { key: 'username', valueFilter: searchUsername },
    { key: 'name', valueFilter: searchNama },
    { key: 'nama_dinas', valueFilter: searchInstansi },
    { key: 'wilayah_label', valueFilter: searchWilayah },
    { key: 'noHp', valueFilter: searchNoHp },
    { key: 'role', valueFilter: searchRole },
]
const filteredColumns = computed(() => {
    let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
    if (filters.length === 0) {
        return page.props.users
    }
    return page.props.users.filter(item => {
        return filters.every(obj => {
            const filterValue = obj.valueFilter.value.toLowerCase()
            return item[obj.key].toLowerCase().includes(filterValue)
        })
    })
})
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    users.value = filteredColumns.value
})
onMounted(function () {
    //flash
    if (page.props.flash.message) toggleFlash.value = true
})
const deleteForm = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    if (form.processing) return
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
const changeNumber = (number) => {
    return number.replace(/^0/, '+62')
}
//new Pagination
const showItemsValue = ref(10)
const showItems = computed(() => {
    if (filteredColumns.value.length < 10) return filteredColumns.value.length
    return showItemsValue.value
})
const currentPage = ref(1)

const updateShowItems = (value) => {
    if (value > filteredColumns.value.length) showItemsValue.value = filteredColumns.value.length
    else showItemsValue.value = value
    currentPage.value = 1
}
const updateCurrentPage = (value) => {
    currentPage.value = value
}
const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * showItems.value
    const end = start + showItems.value
    return filteredColumns.value.slice(start, end)
})
watch(() => page.props.users, (value) => {
    users.value = value
})
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
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" /></button>
                <Link :href="route('users.create')" class="btn bg-info-fordone"><font-awesome-icon
                    icon="fa-solid fa-plus" />
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
                <tr v-if="users.length > 0" v-for="user in paginatedData" :key="user.id">
                    <td>{{ user.number }}</td>
                    <td>{{ user.username }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.nama_dinas }}</td>
                    <td>{{ user.wilayah_label }}</td>
                    <td>{{ changeNumber(user.noHp) }}</td>
                    <td>{{ user.role }}</td>
                    <td class="text-center deleted">
                        <a @click.prevent="resetPasswordLink(user.id)" class="update-pen mx-1">
                            <font-awesome-icon icon="fa-solid fa-lock" title="Reset Password" />
                        </a>
                        <a @click.prevent="changeRolesLink(user.id)" class="mx-1 role-update"><font-awesome-icon
                                :icon="changeRoles(user.role)" title="Ubah Role" /></a>
                        <a @click.prevent="editUser(user.id)" class="edit-pen mx-1">
                            <font-awesome-icon icon="fa-solid fa-pencil" title="Edit Pengguna" />
                        </a>
                    </td>
                    <td class="text-center deleted">
                        <a @click.prevent="toggleDeleteModal(user.id)" class="delete-trash">
                            <font-awesome-icon icon="fa-solid fa-trash-can" class="icon-trash-color" />
                        </a>
                    </td>
                </tr>
                <tr v-else>
                    <td colspan="9" class="text-center">Tidak ada data</td>
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
            <ModalBs :-modal-status="downloadModalStatus" @close="downloadModalStatus = false" :title="'Download Data'">
                <template #modalBody>
                    <label>Masukkan Judul File</label>
                    <input type="text" v-model="downloadTitle" class="form-control" />
                </template>
                <template #modalFunction>
                    <button type="button" class="btn btn-sm bg-success-fordone"
                        @click.prevent="GoDownload('tabel-user', downloadTitle)">Simpan</button>
                </template>
            </ModalBs>
        </Teleport>
        <Pagination @update:currentPage="updateCurrentPage" @update:showItems="updateShowItems" :show-items="showItems"
            :total-items="filteredColumns.length" :current-page="currentPage" />
    </GeneralLayout>
</template>
<style scoped>
.role-update {
    cursor: pointer;
}
</style>