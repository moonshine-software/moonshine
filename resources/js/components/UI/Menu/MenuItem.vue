<template>
  <li class="ms-menu__item" :class="{'basis-full': item.isGroup}">
    <MenuGroup v-if="item.isGroup" :group="item">
      <MenuItemContent :icon="item.icon" :title="item.title" :badge="item.notification"/>
    </MenuGroup>

    <a v-else-if="item.isUrl" :href="item.url" :target="item.target">
      <MenuItemContent :icon="item.icon" :title="item.title"/>
    </a>

    <router-link v-else-if="item.isResourceRoute" :to="item.routerTo" active-class="ms-menu__item--active">
      <MenuItemContent :icon="item.icon" :title="item.title" :badge="item.notification"/>
    </router-link>

    <MenuItemContent v-else :icon="item.icon" :title="item.title" :badge="item.notification"/>
  </li>
</template>

<script setup lang="ts">
import {PropType} from "vue";
import {MenuItem} from "../../../entites/menu";
import MenuGroup from "./MenuGroup.vue";
import MenuItemContent from "./MenuItemContent.vue";

const p = defineProps({
  item: {
    type: Object as PropType<MenuItem>,
    required: true
  }
})
</script>

<style lang="scss">
@tailwind components;

@layer components {

  .ms-menu__item {
    @apply py-2 cursor-pointer select-none whitespace-nowrap overflow-ellipsis;
    @apply transition-colors hover:text-brand-500 text-secondary-700 dark:text-secondary-400;

    &--active {
      @apply text-brand-500 cursor-default #{!important};
    }

    &__icon {
      @apply w-4 h-4 flex-none;
    }
  }
}
</style>
