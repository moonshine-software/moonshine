<template>
  <div>
    <button class="menu__group__label"
            :class="labelClass"
            @click.stop="group.switchCollapse()">
      <span class="flex gap-2 items-center">
        <component
            class="menu__item__icon"
            :is="icon"
            v-if="icon"/>
        <span>
          {{ group.title }}
        </span>
      </span>
      <ChevronRightIcon class="menu__group__arrow" :class="{'rotate-90': !group.collapsed}"/>
    </button>

    <TransitionFade>
      <ul class="menu__group ms-menu__grid-block" v-if="!group.collapsed">
        <MenuItem
            v-for="item in group.items"
            :key="item.id"
            :item="item"
        />
      </ul>
    </TransitionFade>
  </div>
</template>

<script setup lang="ts">
import {computed, PropType} from "vue";
import {MenuItem as MI} from "../../../entites/menu";
import {ChevronRightIcon} from "@heroicons/vue/24/solid";
import * as Icons from "@heroicons/vue/24/outline"
import MenuItem from "./MenuItem.vue";
import TransitionFade from "../../Transitions/TransitionFade.vue";

const p = defineProps({
  group: {
    type: Object as PropType<MI>,
    required: true
  },
})

const labelClass = computed(() => {
  const klass = [
    'menu__group__label'
  ]

  if (!p.group.collapsed) {
    klass.push('menu__group__label--active')
  }

  return klass
});

const icon = !!p.group.icon ? Icons[p.group.icon] : null
</script>

<style lang="scss">
@tailwind components;

@layer components {
  .menu__group {
    //@apply pl-0.5 border-l;
    &__arrow {
      @apply w-5 h-5 flex-none transition-transform;
    }

    &__label {
      @apply whitespace-nowrap cursor-pointer uppercase
      flex items-center text-secondary-500 gap-2;

      @apply mt-0 text-base w-full justify-center mb-4;

      //desktop
      @apply md:justify-between md:my-4 md:text-sm md:w-auto md:gap-6;
    }
  }
}
</style>
