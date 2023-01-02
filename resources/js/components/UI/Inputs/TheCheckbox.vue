<template>
  <label class="m-checkbox__label">
    <input type="checkbox" class="m-checkbox" v-bind="attrs">
    <span><slot></slot></span>
  </label>
</template>

<script lang="ts">
export default {
  inheritAttrs: false
}
</script>

<script setup lang="ts">
import {useAttrs} from "vue";

defineProps<{modelValue?: boolean}>()
const attrs = useAttrs();
</script>

<style lang="scss" scoped>
@import "../../../sass/variables";

// checkbox
$checkbox: '.m-checkbox';

#{$checkbox} {
  @apply p-2 checked:bg-brand bg-secondary-#{$light}
  border-secondary-#{$lightness}
  rounded-md focus:ring-brand
  dark:focus:ring-offset-secondary-#{$darken}
  checked:hover:bg-brand-#{$darkness} focus:checked:bg-brand
  transition-colors;

  $sizes: 'xs', 'sm', 'md', 'lg', 'xl';
  $step: 1;
  $i: 0;
  @each $size in $sizes {
    &-#{$size} { @apply p-#{$i}; $i: $i + $step;}
  }
}

#{$checkbox}__label {
  @apply w-max flex gap-2 justify-start items-center;
}
</style>
