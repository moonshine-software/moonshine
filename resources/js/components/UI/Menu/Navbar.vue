<template>
  <nav ref="nav" class="ms-nav">

    <TheButton
        @click.stop="handleMenuClick"
        :icon="showMenu ? 'x-mark' : 'bars-3'"
        :danger="showMenu"
        :brand="!showMenu"
        square
        reactive
    />

    <router-link tabindex="-1" :to="{name: 'dashboard'}">
      <TheButton square brand icon="home"/>
    </router-link>

    <div class="ms-nav__items-wrapper">
      <router-link
          tabindex="-1"
          v-for="item in menu.navItems"
          :key="`nav-${item.id}`"
          :to="item.routerTo">
        <TheButton
            :icon="item.icon"
            square
            secondary
            class="snap-center"/>
      </router-link>
    </div>
  </nav>
</template>

<script setup lang="ts">
import {computed, ref, Ref} from "vue";
import {useRouter} from "vue-router";
import TheButton from "../Buttons/TheButton.vue";
import {useAppStore} from "../../../store/app";

const app = useAppStore(),
    router = useRouter(),
    showMenu = computed(() => app.theme.showMenu)

const nav: Ref<HTMLScriptElement | null> = ref(null),
    menu = computed(() => app.menu)

router.beforeEach(() => app.hideMenu())

const handleMenuClick = () => app.switchMenuShow()
</script>

<style lang="scss">
//todo: overflow y visible, x - scroll for items
.ms-nav {
  @apply relative flex items-center flex-row md:hidden gap-4;
  @apply py-4 px-4 dark:bg-secondary-700 bg-secondary-50 border-t
  border-secondary-300 dark:border-secondary-600 z-30;
  @apply items-center;

  &__items-wrapper {
    @apply h-full w-full flex gap-4 items-center overflow-y-scroll snap-mandatory snap-x pr-24;
    &::-webkit-scrollbar {
      @apply hidden;
    }
  }

  &__fade {
    @apply absolute h-full w-32 right-0 from-transparent to-secondary-50 bg-gradient-to-r;
    @apply dark:to-secondary-700;
  }
}

</style>
