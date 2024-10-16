<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed, onMounted, onUnmounted, ref } from "vue";
import NavLinkSidebar from "@/Components/NavLinkSidebar.vue";
import NavLinkParentSidebar from "@/Components/NavLinkParentSidebar.vue";

const page = usePage();
const role = page.props.auth.user.role;
const currentRoute = page.props.route;
const menuOpenTabel = ref(false);
const menuOpenMetadata = ref(false);
const menuOpenPengguna = ref(false);
const menuOpenProdusen = ref(false);
const menuOpenMaster = ref(false);

const toggleMenuOpen = function (x) {
  if (x === "tbl") menuOpenTabel.value = !menuOpenTabel.value;
  if (x === "meta") menuOpenMetadata.value = !menuOpenMetadata.value;
  if (x === "user") menuOpenPengguna.value = !menuOpenPengguna.value;
  if (x === "prod") menuOpenProdusen.value = !menuOpenProdusen.value;
  if (x === "master") menuOpenMaster.value = !menuOpenMaster.value;
  // menuOpen.value = !menuOpen.value;
};
// const isMobile = ref(false);
// const isTablet = ref(false);
// const updateDeviceType = () => {
//   isMobile.value = window.matchMedia("(max-width: 767px)").matches;
//   isTablet.value = window.matchMedia(
//     "(min-width: 768px) and (max-width: 1024px)"
//   ).matches;
// };
// onMounted(() => {
//   updateDeviceType();
//   window.addEventListener("resize", updateDeviceType);
// });

// onUnmounted(() => {
//   window.removeEventListener("resize", updateDeviceType);
// });
// const deviceType = computed(() => {
//   if (isMobile.value) return "Mobile";
//   if (isTablet.value) return "Tablet";
//   return "Desktop";
// });
</script>

<template>
  <aside class="main-sidebar sidebar-light-success elevation-4">
    <!-- Brand Logo -->
    <div class="brand-link">
      <img
        src="../../images/favicon2.ico"
        id="icon-sidebar"
        alt="Nothing"
        class="brand-image img-circle elevation-3"
      />
      <span class="brand-text font-weight-bold">For D One</span>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul
          class="nav nav-pills nav-sidebar flex-column"
          data-widget="treeview"
          role="menu"
          data-accordion="false"
        >
          <NavLinkSidebar
            :nav-icon="'fas fa-tachometer-alt'"
            :current-route="currentRoute == 'home.dashboard'"
            :href="route('home.dashboard')"
          >
            Dashboard
          </NavLinkSidebar>
          <NavLinkSidebar
            :currentRoute="currentRoute == 'home.monitoring'"
            :role="role == 'admin'"
            :href="route('home.monitoring')"
            :navIcon="'fa-solid fa-display'"
          >
            Monitoring
          </NavLinkSidebar>
          <NavLinkParentSidebar
            :navIcon="'fa-solid fa-building'"
            :menuOpen="
              menuOpenTabel ||
              currentRoute == 'tabel.index' ||
              currentRoute == 'tabel.create' ||
              currentRoute == 'tabel.deletedList' ||
              currentRoute == 'tabel.entri'
            "
            :toggleMenuOpen="toggleMenuOpen"
            :params="'tbl'"
          >
            <template v-slot:label> Kelola Tabel</template>

            <template v-slot:content>
              <NavLinkSidebar
                :href="route('tabel.index')"
                :currentRoute="currentRoute == 'tabel.index'"
                :navIcon="'fa-solid fa-list-ol'"
              >
                Daftar Tabel</NavLinkSidebar
              >
              <NavLinkSidebar
                :href="route('tabel.create')"
                :currentRoute="currentRoute == 'tabel.create'"
                :role="role == 'admin'"
                :navIcon="'fa-solid fa-plus'"
              >
                Tambah Tabel
              </NavLinkSidebar>
              <NavLinkSidebar
                :role="role == 'admin' || role == 'kominfo'"
                :href="route('tabel.deletedList')"
                :currentRoute="currentRoute == 'tabel.deletedList'"
                :navIcon="'fa-solid fa-recycle'"
              >
                Recycle Bin</NavLinkSidebar
              >
            </template>
          </NavLinkParentSidebar>
          <NavLinkParentSidebar
            :navIcon="'fa-solid fa-building'"
            :menuOpen="
              menuOpenMetadata ||
              currentRoute == 'metavar.index' ||
              currentRoute == 'metavar.lists'
            "
            :toggleMenuOpen="toggleMenuOpen"
            :params="'meta'"
          >
            <template v-slot:label> Kelola Metadata</template>

            <template v-slot:content>
              <NavLinkSidebar
                :navIcon="'fa-solid fa-list-ol'"
                :href="route('metavar.index')"
                :currentRoute="
                  currentRoute == 'metavar.index' || currentRoute == 'metavar.lists'
                "
              >
                Variabel</NavLinkSidebar
              >
            </template>
          </NavLinkParentSidebar>
          <NavLinkParentSidebar
            :role="role == 'admin'"
            :navIcon="'fa-solid fa-users'"
            :menuOpen="
              currentRoute == 'users.index' ||
              currentRoute == 'users.create' ||
              menuOpenPengguna
            "
            :toggleMenuOpen="toggleMenuOpen"
            :params="'user'"
          >
            <template v-slot:label> Kelola Pengguna</template>
            <template v-slot:content>
              <NavLinkSidebar
                :href="route('users.index')"
                :navIcon="'fa-solid fa-list-ol'"
                :currentRoute="currentRoute == 'users.index'"
              >
                Daftar Pengguna</NavLinkSidebar
              >
              <NavLinkSidebar
                :href="route('users.create')"
                :navIcon="'fa-solid fa-plus'"
                :currentRoute="currentRoute == 'users.create'"
              >
                Tambah Pengguna</NavLinkSidebar
              >
            </template>
          </NavLinkParentSidebar>
          <NavLinkParentSidebar
            :role="role == 'admin'"
            :navIcon="'fa-solid fa-building'"
            :menuOpen="
              currentRoute == 'dinas.index' ||
              currentRoute == 'dinas.create' ||
              menuOpenProdusen
            "
            :toggleMenuOpen="toggleMenuOpen"
            :params="'prod'"
          >
            <template v-slot:label> Kelola Produsen</template>

            <template v-slot:content>
              <NavLinkSidebar
                :href="route('dinas.index')"
                :navIcon="'fa-solid fa-list-ol'"
                :currentRoute="currentRoute == 'dinas.index'"
              >
                Daftar Produsen</NavLinkSidebar
              >
              <NavLinkSidebar
                :href="route('dinas.create')"
                :navIcon="'fa-solid fa-plus'"
                :currentRoute="currentRoute == 'dinas.create'"
              >
                Tambah Produsen</NavLinkSidebar
              >
            </template>
          </NavLinkParentSidebar>
          <NavLinkParentSidebar
            :role="role == 'admin' || role == 'kominfo'"
            :navIcon="'fa-solid fa-building'"
            :toggleMenuOpen="toggleMenuOpen"
            :params="'master'"
            :menuOpen="
              currentRoute == 'tabel.master' ||
              currentRoute == 'subject.index' ||
              currentRoute == 'column_group.index' ||
              currentRoute == 'columns.index' ||
              currentRoute == 'row_group.index' ||
              currentRoute == 'rows.index' ||
              currentRoute == 'tabel.kategori' ||
              menuOpenMaster
            "
          >
            <template v-slot:label> Kelola Master</template>

            <template v-slot:content>
              <NavLinkSidebar
                :href="route('tabel.master')"
                :currentRoute="currentRoute == 'tabel.master'"
                :navIcon="'fas fa-table'"
              >
                Tabel</NavLinkSidebar
              >
              <NavLinkSidebar
                :href="route('tabel.kategori')"
                :currentRoute="currentRoute == 'tabel.kategori'"
                :navIcon="'fas fa-table'"
              >
                Kategori Tabel</NavLinkSidebar
              >
              <NavLinkSidebar
                :role="role == 'admin'"
                :href="route('subject.index')"
                :navIcon="'fa-solid fa-tags'"
                :currentRoute="currentRoute == 'subject.index'"
              >
                Subjek
              </NavLinkSidebar>
              <NavLinkSidebar
                :href="route('column_group.index')"
                :navIcon="'fa-solid fas fa-bars'"
                :currentRoute="currentRoute == 'column_group.index'"
              >
                Kelompok Kolom</NavLinkSidebar
              >
              <NavLinkSidebar
                :href="route('columns.index')"
                :navIcon="'fa-solid fas fa-bars'"
                :currentRoute="currentRoute == 'columns.index'"
              >
                Kolom</NavLinkSidebar
              >
              <NavLinkSidebar
                :href="route('row_group.index')"
                :currentRoute="currentRoute == 'row_group.index'"
                :navIcon="'fa-solid fas fa-th-list'"
              >
                Kelompok Baris</NavLinkSidebar
              >
              <NavLinkSidebar
                :href="route('rows.index')"
                :currentRoute="currentRoute == 'rows.index'"
                :navIcon="'fa-solid fas fa-th-list'"
              >
                Baris</NavLinkSidebar
              >
            </template>
          </NavLinkParentSidebar>
          <NavLinkSidebar
            :navIcon="'fas fa-user'"
            :href="route('users.edit')"
            :currentRoute="currentRoute == 'users.edit'"
          >
            Edit Profile</NavLinkSidebar
          >
          <NavLinkSidebar
            :role="role == 'admin' || role == 'kominfo'"
            :navIcon="'fa-solid fa-list'"
            :href="route('home-api.create')"
            :currentRoute="currentRoute == 'home-api.create'"
          >
            Daftar API
          </NavLinkSidebar>
          <NavLinkSidebar
            :role="true"
            :navIcon="'fa-solid fa-home'"
            :href="route('/')"
            :currentRoute="currentRoute == '/'"
          >
            Kembali ke Beranda
          </NavLinkSidebar>

          <br />
          <hr />
          <font-awesome-icon
            data-widget="pushmenu"
            icon="fa-solid fa-circle-chevron-left"
            class="edit-pen back-pen"
          />
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
</template>

<style scoped>
div.nav-link {
  cursor: pointer;
}
.back-pen {
  color: #3d3b8e;
}
.sidebar-dark-success .nav-sidebar > .nav-item > .nav-link.active,
.sidebar-light-success .nav-sidebar > .nav-item > .nav-link.active {
  background-color: #3d3b8e;
  color: #fff;
}

body:not(.layout-fixed) .main-sidebar {
  position: fixed !important;
}
</style>
