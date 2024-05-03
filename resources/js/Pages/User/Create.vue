<script setup>
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import { Head, usePage, useForm, Link } from "@inertiajs/vue3"
import Multiselect from '@vueform/multiselect'
import { defineComponent, ref } from 'vue';

defineComponent({
    Multiselect
})

const page = usePage()
const dinas = page.props.dinas
const dinasDrop = ref({
    value: null,
    options: dinas
})
const roleDrop = ref({
    value: null,
    options: [
        { label: 'Produsen', value: 'produsen' },
        { label: 'Kominfo', value: 'kominfo' },
        { label: 'Admin', value: 'admin' },
    ]
})
const form = useForm({
    username: null,
    name: null,
    email: null,
    id_dinas: null,
    role: null,
    noHp: null,
    password: null,
    password_confirmation: null
})

const submit = function() {
    form.post(route('users.store'))
}
</script>
<template>
    <Head title="Tambah Pengguna Baru" />
    <GeneralLayout>
        <div>
            <form @submit.prevent="submit">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="username">Username</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="text" id="username" v-model="form.username" class="form-control">
                                <div class="text-danger text-left" v-if="form.errors.username" id="error-username">{{ form.errors.username }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <input id="idHidden" class="hiddenInput" hidden>
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="name">Nama</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="text" id="name" v-model="form.name" class="form-control">
                                <div class="text-danger text-left" v-if="form.errors.name" id="error-name">{{ form.errors.name }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="email">Email</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="email" id="email" v-model="form.email" class="form-control">
                                <div class="text-danger text-left" v-if="form.errors.email" id="error-email">{{ form.errors.email }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="iddinas">Instansi</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <Multiselect v-model="form.id_dinas" :options="dinasDrop.options" placeholder="-- Pilih Instansi --" :searchable="true" />
                                <div class="text-danger text-left" v-if="form.errors.id_dinas" id="error-id_dinas">{{ form.errors.id_dinas }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="noHp">Nomor Hp</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="tel" id="noHp" v-model="form.noHp" pattern="[0-9]{12,13}" placeholder="081234567809"
                                    class="form-control">
                                <div class="text-danger text-left" v-if="form.errors.noHp" id="error-noHp">{{ form.errors.noHp }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="role">Peran</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <Multiselect v-model="form.role" :options="roleDrop.options" placeholder="-- Pilih Peran --" :searchable="true"/>
                                <div class="text-danger text-left" v-if="form.errors.role" id="error-role">{{ form.errors.role }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
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
                                <div class="text-danger text-left" v-if="form.errors.password_confirmation" id="error-password">{{ form.errors.password_confirmation }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button id="addUsers" class="btn btn-sm bg-info-fordone"> <font-awesome-icon icon="fa-solid fa-save"/>
                                Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
            <Link :href="route('users.index')" class="btn btn-light border"><font-awesome-icon icon="fas fa-chevron-left"/> Kembali
            </Link>
        </div>
    </GeneralLayout>
</template>