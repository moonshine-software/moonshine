<template>
  <component
      class="m-icon"
      :is="iconComponent"
      v-if="icon"/>
</template>

<script setup lang="ts">
import * as Icons from "@heroicons/vue/24/outline"
import {camelize} from "../../../utilities/string_helpers";
import TheSpinnerIcon from "./TheSpinnerIcon.vue";
import {computed, toRef} from "vue";

const p = defineProps<{
  icon?: string,
  reactive?: boolean
}>()

const icon = p.reactive ? toRef(p, 'icon') : {value: p.icon}
//reactive
const iconComponent = computed(() => {
  if (p.icon === 'spinner') {
    return TheSpinnerIcon
  } else {
    //@ts-ignore
    return !!icon.value ? Icons[camelize(icon.value + '-icon')] : null
  }
})
</script>

<style lang="scss">
@import "../../../sass/variables";

.m-icon {
  @apply w-6 h-6;

  &-lg {
    @apply w-10 h-10;
  }

  &-xl {
    @apply w-12 h-12;
  }

  &-md {
    @apply w-8 h-8;
  }

  &-sm {
    @apply w-4 h-4;
  }

  @each $color in $colors {
    &-#{$color} {
      @apply text-#{$color}
    }
  }
}
</style>
