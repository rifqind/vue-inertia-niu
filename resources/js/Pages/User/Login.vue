<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";

const form = useForm({
  username: "",
  password: "",
  _token: null,
});

const submit = async function () {
  const response = await axios.get(route("token"));
  form._token = response.data;
  if (form.processing) return;
  form.post(route("login"));
};
</script>
<template>
  <Head title="Login" />
  <div class="container vh-100">
    <div class="d-flex flex-column justify-content-center align-items-center h-100">
      <div class="card shadow login-card w-100" id="card-login">
        <div class="row no-gutters">
          <!-- Image Section -->
          <div class="col-md-5 d-none d-md-block">
            <img
              src="../../../images/login-try.jpg"
              id="login-image"
              alt="login"
              class="login-card-img"
            />
          </div>
          <!-- Form Section -->
          <div class="col-md-7 col-12 d-flex align-items-center">
            <div class="card-body">
              <div class="brand-wrapper text-center">
                <img
                  src="../../../images/Logo Tryhard.png"
                  id="logo-login"
                  alt="logo-login"
                />
              </div>
              <form @submit.prevent="submit" id="formUser">
                <div class="form-group">
                  <div class="row mx-2 mb-2">
                    <input
                      v-model="form.username"
                      type="text"
                      id="username"
                      name="username"
                      class="form-control form-login"
                      placeholder="Username"
                    />
                  </div>
                  <div class="row mx-2 mb-2">
                    <input
                      v-model="form.password"
                      type="password"
                      id="password"
                      name="password"
                      class="form-control form-login"
                      placeholder="Password"
                    />
                    <div
                      class="text-danger text-left"
                      v-if="form.errors.email"
                      id="error"
                    >
                      {{ form.errors.email }}
                    </div>
                  </div>
                </div>
                <div class="mx-2 text-center">
                  <button id="login" class="mb-2 btn bg-success-fordone form-login">
                    <font-awesome-icon icon="fa-solid fa-right-to-bracket" /> Masuk
                  </button>
                  <Link
                    id="home"
                    class="btn bg-info-fordone form-login"
                    :href="route('/')"
                  >
                    <font-awesome-icon icon="fa-solid fa-rotate-left" /> Kembali ke
                    Beranda
                  </Link>
                </div>
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
  background-color: #1d845b;
  color: whitesmoke;
}

.login-card .card-body {
  padding: 20px;
}

#card-login {
  width: 100%;
  max-width: 50rem;
  border-radius: 20px;
}

#login-image {
  width: 100%;
  border-radius: 20px 0 0 20px;
}

.brand-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
}

#logo-login {
  width: 100px;
  height: 100px;
}

.form-login {
  width: 100%;
  max-width: 20rem;
  margin: 0 auto;
}

@media (max-width: 768px) {
  .login-card .card-body {
    padding: 15px;
  }

  #card-login {
    width: 100%;
  }

  #login-image {
    display: none;
  }

  .form-login {
    width: 100%;
  }
}
</style>
