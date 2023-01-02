<template>
  <button class="m-btn" :class="calculatedClass">
    <TheIcon
        v-if="icon"
        :icon="icon"
        :reactive="reactive"
        class="flex-none m-btn__icon"/>
    <span class="" v-if="!!$slots.default">
      <slot></slot>
    </span>
  </button>
</template>

<script setup lang="ts">
import TheIcon from "../Icons/TheIcon.vue";
import {computed, ComputedRef, isRef, Ref, toRefs} from "vue";

const props = defineProps<{
  icon?: string,
  xs?: boolean,
  sm?: boolean,
  md?: boolean,
  lg?: boolean,
  inset?: boolean,
  square?: boolean,
  brand?: boolean,
  secondary?: boolean,
  success?: boolean,
  warning?: boolean,
  danger?: boolean,
  outlined?: boolean,
  text?: boolean,
  reactive?: boolean
}>()
//todo: remove reactive. Move reactive props to parent

const getClassFromProps = (props: Record<string, boolean | string | Ref<boolean | string>>) => {
  const exc: string[] = ['reactive', 'icon']

  const keys = Object.keys(props).filter(p => {
    return isRef(props[p]) ? props[p].value : props[p]
        && !exc.includes(p)
  })
  return keys.length ? `m-btn-` + keys.join(' m-btn-') : ''
}

let calculatedClass: string | ComputedRef<string> = getClassFromProps(props)

if (props.reactive) {
  const p = toRefs(props)
  calculatedClass = computed(() => getClassFromProps(p))
}

</script>

<style lang="scss" scoped>
@import "../../../sass/variables";
@import "../../../sass/input_mixins";

$btn: '.m-btn';
#{$btn} {
  $square: #{$btn}-square;

  @include inputBase;
  @include baseSizes;
  @include stateFillColors('brand');
  @include outline('brand', $btn);
  @include text('brand', $btn);
  @apply flex gap-2 items-center justify-center;
  @apply select-none hover:shadow-lg disabled:shadow-none;

  &#{$square} {
    @apply p-2.5;
  }

  &-xs#{$square} {
    & #{$btn}__icon {
      @apply w-4 h-4;
    }

    @apply p-1 rounded-xl;
  }

  &-sm {
    & #{$btn}__icon {
      @apply w-6 h-6;
    }

    &#{$square} {
      @apply p-1.5;
    }

    @apply px-2 py-1.5 rounded-xl;
  }

  &-md#{$square} {
    & #{$btn}__icon {
      @apply w-7 h-7;
    }

    @apply p-3;
  }

  &-lg#{$square} {
    & #{$btn}__icon {
      @apply w-8 h-8;
    }

    @apply p-4;
  }

  &-xl#{$square} {
    & #{$btn}__icon {
      @apply w-9 h-9;
    }

    @apply p-5;
  }

  //colors
  @each $color in $colors {
    &-#{$color} {
      @include stateFillColors($color);
      @include outline($color, $btn);
      @include text($color, $btn)
    }
  }

  //them color
  &-theme {
    @include stateFillColors('secondary');
    @apply dark:bg-secondary-#{$lighter} dark:text-secondary-#{$darken}
    dark:border-secondary-#{$lighter} dark:ring-secondary-#{$lightness}
    dark:hover:bg-secondary-#{$lightness} dark:hover:border-secondary-#{$lightness}
  }
}

</style>
