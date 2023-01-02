<template>
  <main class="ms-base__layout" v-if="loaded">
    <Navbar v-if="showNavbar"/>
    <Menu v-else/>

    <section class="ms-page">

      <header>
        <h1 class="text-4xl">
          <slot name="title"></slot>
        </h1>

        <section>
          <slot name="header"></slot>
        </section>
      </header>

      <main class="space-y-6 mb-auto">
        <slot></slot>
      </main>

      <footer>
        <slot name="footer">
        <span class="text-secondary-400 dark:text-secondary-500 text-xs">
          Moonshine. 2022
        </span>
        </slot>
      </footer>

    </section>
  </main>
  <Loader v-else />

</template>

<script setup lang="ts">
import Navbar from "../components/UI/Menu/Navbar.vue";
import Menu from "../components/UI/Menu/Menu.vue";
import {useAppStore} from "../store/app";
import {useUserStore} from "../store/user";
import {computed} from "vue";
import Loader from "../Moonshine.vue";

const appStore = useAppStore()
const userStore = useUserStore()

const loaded = computed(() => appStore.loaded && userStore.authenticated)
const showNavbar = computed(() => appStore.showMenuAsNavbar)
</script>

<style>
.ms-base__layout {
  @apply flex flex-col-reverse md:flex-row h-full;
}

.ms-page {
  @apply space-y-6 px-4 md:px-8 py-6 flex flex-col justify-between flex-grow w-full md:w-9/12 lg:w-10/12;
  @apply overflow-y-scroll h-screen;
}
</style>
