<script setup>
import { usePage, Head, Link, useForm } from '@inertiajs/vue3';
import GeneralLayout from '@/Layouts/GeneralLayout.vue'

const page = usePage()
const user = page.props.user
const form = useForm({
    id: user.id,
})
const submit = function() {
    form.post(route('users.default'))
}
</script>
<template>

    <Head title="Reset Password" />
    <GeneralLayout>
        <div class="this-container">
            <form @submit.prevent="submit">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="username">Username</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="text" id="username" v-model="user.username" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <input id="idHidden" class="hiddenInput" hidden>
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="name">Nama</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="text" id="name" v-model="user.name" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="email">Email</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="email" id="email" v-model="user.email" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="iddinas">Instansi</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="text" id="username" v-model="user.role" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="noHp">Nomor Hp</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="tel" id="noHp" v-model="user.noHp" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 d-flex align-items-center">
                                <label class="mb-0" for="role">Peran</label>
                            </div>
                            <div class="col-7 d-flex flex-column">
                                <input type="text" id="username" v-model="user.role" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="text-right">
                            <button id="resetPassword" class="btn btn-sm btn-warning"> <font-awesome-icon icon="fa-solid fa-key"/>
                                Reset Password</button>
                        </div>
                    </div>
                </div>
            </form>
            <Link :href="route('users.index')" class="btn btn-light border"><font-awesome-icon icon="fas fa-chevron-left"/> Kembali
            </Link>
        </div>
    </GeneralLayout>
</template>
<style scoped>
.this-container {
    max-width: 50vw;
}
</style>