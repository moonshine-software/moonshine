<template>
    <input
        v-bind="$attrs"
        class="m-inp"
        :value="modelValue"
        :placeholder="`Test placeholder`"
        @input="$emit('update:modelValue', $event.target.value)"/>
</template>

<script setup lang="ts">
defineProps<{ modelValue?: any, icon?: string }>()
</script>

<style lang="scss" scoped>
@import "../../../sass/variables";
@import "../../../sass/input_mixins";

// base input
$input: '.m-inp';

#{$input} {
  $color: 'secondary';
  $light: #{$color}-#{$light};
  $dark: #{$color}-#{$dark};
  $lightness: #{$color}-#{$lightness};
  $lighter: #{$color}-#{$lighter};
  $darkness: #{$color}-#{$darkness};
  $darken: #{$color}-#{$darken};

  @include inputBase;
  @include baseSizes;

  @apply tracking-normal;

  @apply dark:bg-#{$dark}/10 bg-#{$light}/10 border
  border-#{$lighter}/50 dark:border-#{$color}/50
  text-#{$color} dark:text-#{$lightness}
  focus:text-#{$darken} dark:focus:text-#{$lighter}
  hover:border-secondary-300 hover:dark:border-secondary-400
  focus:ring-brand focus:border-secondary-200 focus:dark:border-secondary;
  &::placeholder {
    @apply text-#{$lighter} dark:text-#{$color};
  }

  &-filled {
    @apply dark:bg-#{$dark} bg-#{$light}
  }

  &-text {
    @apply border-transparent ring-transparent #{!important};
    @apply hover:shadow-none;
  }
}

</style>
