<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    username: '',
    password: ''
})

const submit = function () {
    form.post(route('login'))
}

</script>
<template>
    <Head title="Login" />
    <div class="container vh-100">
        <div class="flex-column d-flex justify-content-center align-items-center h-100">
            <div class="card shadow login-card" id="card-login">
                <div class="row">
                    <div class="col-md-5">
                        <img src="../../../images/login-try.jpg" id="login-image" alt="login" class="login-card-img">
                    </div>
                    <div class="col-md-7 d-flex align-items-center">
                        <div class="card-body">
                            <div class="brand-wrapper text-center">
                                <img src="../../../images/Logo Tryhard.png" class="" id="logo-login" alt="logo-login">
                            </div>
                            <form @submit.prevent="submit" id="formUser">
                                <div class="form-group">
                                    <div class="row mx-2 mb-2">
                                        <input v-model="form.username" type="username" id="username" name="username"
                                            class="form-control form-login" placeholder="Username">
                                    </div>
                                    <div class="row mx-2 mb-2">
                                        <input v-model="form.password" type="password" id="password" name="password"
                                            class="form-control form-login" placeholder="Password">
                                    </div>
                                </div>
                                <div class="mx-2">
                                    <!-- <button id="login" class="btn bg-success-fordone form-login"><i
                                            class="fa-solid fa-right-to-bracket"></i> Masuk</button> -->
                                    <button id="login" class="mb-2 btn bg-success-fordone form-login"><i
                                            class="fa-solid fa-right-to-bracket"></i> Masuk</button>
                                    <Link id="home" class="btn bg-info-fordone form-login" :href="route('/')"><i
                                        class="fa-solid fa-rotate-left"></i> Kembali ke Beranda</Link>
                                </div>
                                <!-- <div class="mx-2">
                                    <Link id="home" class="btn bg-info-fordone form-login" :href="route('/')"><i
                                            class="fa-solid fa-right-to-bracket"></i> Masuk</Link>
                                </div> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.bg-info-fordone {
    background-color: #3d3b8e;
    color: whitesmoke;
}

.bg-success-fordone {
    background-color: #1D845B;
    color: whitesmoke;
}

.login-card .card-body {
    padding: 85px 60px 60px;
    padding-top: 10px;
    padding-right: 60px;
    padding-bottom: 60px;
    padding-left: 60px;
}

.login-card-description {
    font-size: 25px;
    color: #000;
    font-weight: normal;
    margin-bottom: 23px;
}

.cube {
    position: absolute;
    top: 80vh;
    left: 45vw;
    width: 10px;
    height: 10px;
    border: solid 1px #d7d4e4;
    transform-origin: top left;
    transform: scale(0) rotate(0deg) translate(-50%, -50%);
    animation: cube 12s ease-in forwards infinite;
}

.cube:nth-child(2n) {
    border-color: #fff;
}

.cube:nth-child(2) {
    animation-delay: 2s;
    left: 25vw;
    top: 40vh;
}

.cube:nth-child(3) {
    animation-delay: 4s;
    left: 75vw;
    top: 50vh;
}

.cube:nth-child(4) {
    animation-delay: 6s;
    left: 90vw;
    top: 10vh;
}

.cube:nth-child(5) {
    animation-delay: 8s;
    left: 10vw;
    top: 85vh;
}

.cube:nth-child(6) {
    animation-delay: 10s;
    left: 50vw;
    top: 10vh;
}

@keyframes cube {
    from {
        transform: scale(0) rotate(0deg) translate(-50%, -50%);
        opacity: 1;
    }

    to {
        transform: scale(20) rotate(960deg) translate(-50%, -50%);
        opacity: 0;
    }
}

#card-login {
    width: 50rem;
    border-radius: 20px;
}

#login-image {
    max-width: 100%;
    max-height: 100%;
    border-radius: 20px;
}

#logo-login {
    width: 100px;
    height: 100px;
}

.form-login {
    width: 20rem;
}

.bg {
    animation: slide 3s ease-in-out infinite alternate;
    background-image: linear-gradient(-60deg, #6c3 50%, #09f 50%);
    bottom: 0;
    left: -50%;
    opacity: 0.5;
    position: fixed;
    right: -50%;
    top: 0;
    z-index: 0.1;
}

.bg2 {
    animation-direction: alternate-reverse;
    animation-duration: 4s;
}

.bg3 {
    animation-duration: 5s;
}

@keyframes slide {
    0% {
        transform: translateX(-25%);
    }

    100% {
        transform: translateX(25%);
    }
}
</style>