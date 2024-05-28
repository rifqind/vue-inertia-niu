<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { Head, usePage, useForm, Link } from "@inertiajs/vue3"
import Multiselect from '@vueform/multiselect'
import { defineComponent, ref } from 'vue';
import SpinnerBorder from '@/Components/SpinnerBorder.vue';

defineComponent({
    Multiselect
})

const page = usePage()
const dinas = page.props.dinas
const user = page.props.user
const changePassword = ref(false)
const toggleFlash = ref(false)
const triggerSpinner = ref(false)

const dinasDrop = ref({
    value: null,
    options: dinas
})
const form = useForm({
    id: user.id,
    username: user.username,
    name: user.name,
    email: user.email,
    id_dinas: user.id_dinas,
    noHp: user.noHp,
    password: null,
    password_confirmation: null,
    _token: null,
})

const submit = async function () {
    const response = await axios.get(route('token'))
    form._token = response.data
    form.post(route('users.editProfile'), {
        onBefore: function () { triggerSpinner.value = true },
        onFinish: function () {
            triggerSpinner.value = false
            if (page.props.flash.message) toggleFlash.value = true
            hideFlashMessage()
            changePassword.value = false
        }
    })
}
const hideFlashMessage = function () {
    setInterval(() => {
        toggleFlash.value = false
    }, 3000);
}
const goBack = function () {
    window.history.back()
}
</script>
<template>

    <Head title="Edit Pengguna" />
    <SpinnerBorder v-if="triggerSpinner" />
    <GeneralLayout>
        <div>
            <div class="h4">
                Edit Profil : {{ user.name }}
            </div>
            <div class="alert alert-success" v-if="toggleFlash" role="alert">
                {{ page.props.flash.message }}
            </div>
            <form @submit.prevent="submit">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="username">Username</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="text" id="username" v-model="form.username" class="form-control">
                                <div class="text-danger text-left" v-if="form.errors.username" id="error-username">{{
        form.errors.username }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <input id="idHidden" class="hiddenInput" hidden>
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="name">Nama</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="text" id="name" v-model="form.name" class="form-control">
                                <div class="text-danger text-left" v-if="form.errors.name" id="error-name">{{
        form.errors.name }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="email">Email</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="email" id="email" v-model="form.email" class="form-control">
                                <div class="text-danger text-left" v-if="form.errors.email" id="error-email">{{
        form.errors.email }}</div>
                            </div>
                        </div>
                        <div class="row mb-3" v-if="page.props.auth.user.role == 'admin'">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="iddinas">Instansi</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <Multiselect v-model="form.id_dinas" :options="dinasDrop.options"
                                    placeholder="-- Pilih Instansi --" :searchable="true" />
                                <div class="text-danger text-left" v-if="form.errors.id_dinas" id="error-id_dinas">{{
        form.errors.id_dinas }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="noHp">Nomor Hp</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="tel" id="noHp" v-model="form.noHp" pattern="[0-9]{12,13}"
                                    placeholder="081234567809" class="form-control">
                                <div class="text-danger text-left" v-if="form.errors.noHp" id="error-noHp">{{
        form.errors.noHp }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-sm bg-success-fordone editButton mr-2"> <font-awesome-icon
                                    icon="fa-solid fa-check mr-2" /> Simpan
                            </button>
                            <div id="changePassword" class="btn btn-sm btn-warning"
                                @click="changePassword = !changePassword"> <font-awesome-icon
                                    icon="fa-solid fa-key mr-2" /> Ganti
                                Password</div>
                        </div>
                    </div>
                </div>
                <div class="card" v-if="changePassword">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="password">Password</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="password" id="password" v-model="form.password" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="password_confirmation">Konfirmasi Password</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="password" id="password_confirmation" v-model="form.password_confirmation"
                                    class="form-control">
                                <div class="text-danger text-left" v-if="form.errors.password_confirmation"
                                    id="error-password">{{ form.errors.password_confirmation }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button id="addUsers" class="btn btn-sm btn-warning"> <font-awesome-icon
                                    icon="fa-solid fa-save" /> Simpan Password Baru</button>
                        </div>
                    </div>
                </div>
            </form>
            <a @click="goBack" class="btn btn-light border"><font-awesome-icon icon="fas fa-chevron-left" /> Kembali
            </a>
        </div>
    </GeneralLayout>
</template>