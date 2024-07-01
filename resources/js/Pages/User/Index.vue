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
import axios from 'axios';
import { debounce } from '@/debounce';

const page = usePage()
var uObject = page.props.users.data
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
            fetchData()
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
// const filteredColumns = computed(() => {
//     let filters = ArrayBigObjects.filter(obj => obj.valueFilter.value)
//     if (filters.length === 0) {
//         return page.props.users
//     }
//     return page.props.users.filter(item => {
//         return filters.every(obj => {
//             const filterValue = obj.valueFilter.value.toLowerCase()
//             return item[obj.key].toLowerCase().includes(filterValue)
//         })
//     })
// })
watch(ArrayBigObjects.map(obj => obj.valueFilter), function () {
    // users.value = filteredColumns.value
    currentPage.value = 1
    delayedFetchData()
})
const delayedFetchData = debounce(() => {
    fetchData()
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
            fetchData()
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
// const showItemsValue = ref(10)
// const showItems = computed(() => {
//     if (filteredColumns.value.length < 10) return filteredColumns.value.length
//     return showItemsValue.value
// })
const showItems = ref(10)
const currentPage = ref(1)
const updateShowItems = (value) => {
    // if (value > filteredColumns.value.length) showItemsValue.value = filteredColumns.value.length
    // else showItemsValue.value = value
    // currentPage.value = 1
    showItems.value = value
    fetchData()
}
const updateCurrentPage = (value) => {
    currentPage.value = value
    fetchData()
}
const totalItems = ref(page.props.countData)
watch(() => page.props.countData, (value) => {
    totalItems.value = value
})
const paginatedData = computed(() => {
    // const start = (currentPage.value - 1) * showItems.value
    // const end = start + showItems.value
    // return filteredColumns.value.slice(start, end)
    return users.value
})
watch(() => page.props.users.data, (value) => {
    users.value = value
})
const orderAttribute = ref({
    before: null,
    label: null,
    value: 'asc',
})
const clickToOrder = (value) => {
    orderAttribute.value.label = value
    if (orderAttribute.value.before == null || orderAttribute.value.before == value) {
        if (orderAttribute.value.value == 'asc') orderAttribute.value.value = 'desc'
        else if (orderAttribute.value.value == 'desc') orderAttribute.value.value = null
        else orderAttribute.value.value = 'asc'
    } else orderAttribute.value.value = 'asc'
    orderAttribute.value.before = value
    fetchData()
}
const fetchData = async () => {
    try {
        const response = await axios.get(route('users.index'), {
            params: {
                currentPage: currentPage.value, paginated: showItems.value,
                ArrayFilter: {
                    username: searchUsername.value,
                    name: searchNama.value,
                    nama_dinas: searchInstansi.value,
                    wilayah_label: searchWilayah.value,
                    noHp: searchNoHp.value,
                    role: searchRole.value,
                },
                orderAttribute: orderAttribute.value,
            }
        })
        // console.log(response.data.users.data)
        users.value = response.data.users.data
        totalItems.value = response.data.countData
    } catch (error) {
        console.error('Error fetching data: ', error)
    }
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
                <button class="btn bg-success-fordone mr-2" title="Download"
                    @click="downloadModalStatus = true"><font-awesome-icon icon="fa-solid fa-circle-down" /></button>
                <Link :href="route('users.create')" class="btn bg-info-fordone"><font-awesome-icon
                    icon="fa-solid fa-plus" />
                Tambah Pengguna Baru</Link>
            </div>
        </div>
        <FlashMessage :toggleFlash="toggleFlash" @close="toggleFlash = false" :flash="page.props.flash.message" />
        <table class="table table-hover table-bordered table-search" ref="tabelUser" id="tabel-user">
            <thead>
                <tr class="bg-info-fordone">
                    <th class="first-column">No.</th>
                    <th class="text-center th-order" @click="clickToOrder('username')">Username</th>
                    <th class="text-center th-order tabel-width-15" @click="clickToOrder('name')">Nama</th>
                    <th class="text-center th-order tabel-width-20" @click="clickToOrder('dinas.nama')">Nama
                        Instansi</th>
                    <th class="text-center th-order tabel-width-20" @click="clickToOrder('w.label')">Wilayah
                        Kerja
                    </th>
                    <th class="text-center th-order" @click="clickToOrder('noHp')">No. HP</th>
                    <th class="text-center th-order" @click="clickToOrder('role')">Peran</th>
                    <th class="text-center th-order deleted tabel-width-8">Edit</th>
                    <th class="text-center th-order deleted">Hapus</th>
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
                            class="search-input form-control" placeholder="cari dengan 08..." ></td>
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
            :total-items="totalItems" :current-page="currentPage" :current-show-items="paginatedData.length" />
    </GeneralLayout>
</template>
<style scoped>
.role-update,
.th-order {
    cursor: pointer;
}
</style>