<template>
  <aside class="ms-menu" v-if="appLoaded">
    <header>
      <BrandLogo class="ms-menu__logo"/>
    </header>

    <main class="mb-auto">
      <ul class="ms-menu__grid-block">
        <MenuItem v-for="item in menu.items" :item="item"/>
      </ul>
    </main>

    <footer>
      <div class="footer border-0 justify-start">
        <DarkModeButton />
        <LogoutButton />
      </div>

      <!--      <language-selector class="mt-6"/>-->
      <div class="footer mt-2">
        <UserCard/>
      </div>
    </footer>
  </aside>
</template>

<script setup lang="ts">
import BrandLogo from "../BrandLogo.vue";
import {computed} from "vue";
import UserCard from "../UserCard.vue";
import MenuItem from "./MenuItem.vue";
import DarkModeButton from "../Buttons/DarkModeButton.vue";
import {useAppStore} from "../../../store/app";
import LogoutButton from "../Buttons/LogoutButton.vue";

const
    appStore = useAppStore(),
    menu = computed(() => appStore.menu),
    appLoaded = computed(() => appStore.loaded)

</script>

<style lang="scss">
@tailwind components;
@layer components {
  .ms-menu {
    //general
    @apply flex py-4 space-y-6 md:space-y-12 md:flex-none md:px-4 z-20
    bg-secondary-100 dark:bg-secondary-800 flex-col justify-between my-auto;

    main {
      @apply overflow-y-scroll;
    }

    &__logo {
      @apply max-h-16 md:max-h-24 mx-auto md:mx-0;
    }

    &__grid-block {
      @apply md:space-y-2 flex flex-wrap items-start justify-center md:block md:flex-none gap-8;
    }

    //mobile
    @apply w-full h-full z-10;

    //desktop
    @apply md:relative md:w-3/12 lg:w-2/12 md:h-[96vh]
    md:rounded-r-2xl md:shadow-xl md:shadow-brand/20
    md:border md:border-secondary-200/40 dark:md:border-secondary-700/40;

    .footer {
      @apply flex justify-between items-center gap-2 border-t
      border-secondary-300 dark:border-secondary-600 pt-4 px-4 md:px-0;
    }
  }

}

</style>
