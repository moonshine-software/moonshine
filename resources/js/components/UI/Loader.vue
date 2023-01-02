<template>
  <main class="m-loader-wrapper" v-show="debounce">
    <section class="py-6 mx-auto">
      <div class="m-loader">
        <svg viewBox="0 0 80 80">
          <circle cx="40" cy="40" r="32"></circle>
        </svg>
      </div>

      <div class="m-loader triangle">
        <svg viewBox="0 0 86 80">
          <polygon points="43 8 79 72 7 72"></polygon>
        </svg>
      </div>

      <div class="m-loader">
        <svg viewBox="0 0 80 80">
          <rect x="8" y="8" width="64" height="64"></rect>
        </svg>
      </div>
    </section>
  </main>
</template>

<script lang="ts" setup>
//designed by: https://dribbble.com/shots/5878367-Loaders

import {ref} from "vue";

const debounce = ref(false)
setTimeout(() => debounce.value = true, 200)
</script>

<style scoped lang="scss">
.m-loader-wrapper {
  @apply flex justify-center w-full h-full flex-col;
}

.m-loader {
  --path: theme('colors.secondary.300');

  .dark & {
    --path: theme('colors.secondary.600');
  }

  --dot: theme('colors.brand.500');
  --duration: 3s;
  width: 44px;
  height: 44px;
  position: relative;

  &:before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    position: absolute;
    display: block;
    background: var(--dot);
    top: 37px;
    left: 19px;
    transform: translate(-18px, -18px);
    animation: dotRect var(--duration) cubic-bezier(0.785, 0.135, 0.15, 0.86) infinite;
  }

  svg {
    display: block;
    width: 100%;
    height: 100%;

    rect,
    polygon,
    circle {
      fill: none;
      stroke: var(--path);
      stroke-width: 10px;
      stroke-linejoin: round;
      stroke-linecap: round;
    }

    polygon {
      stroke-dasharray: 145 (221 - 145) 145 (221 - 145);
      stroke-dashoffset: 0;
      animation: pathTriangle var(--duration) cubic-bezier(0.785, 0.135, 0.15, 0.86) infinite;
    }

    rect {
      stroke-dasharray: calc((256 / 4 * 3)) calc((256 / 4)) calc((256 / 4 * 3)) calc((256 / 4));
      stroke-dashoffset: 0;
      animation: pathRect 3s cubic-bezier(0.785, 0.135, 0.15, 0.86) infinite;
    }

    circle {
      stroke-dasharray: calc((200 / 4 * 3)) calc((200 / 4)) calc((200 / 4 * 3)) calc((200 / 4));
      stroke-dashoffset: 75;
      animation: pathCircle var(--duration) cubic-bezier(0.785, 0.135, 0.15, 0.86) infinite;
    }
  }

  &.triangle {
    width: 48px;

    &:before {
      left: 21px;
      transform: translate(-10px, -18px);
      animation: dotTriangle var(--duration) cubic-bezier(0.785, 0.135, 0.15, 0.86) infinite;
    }
  }
}

@keyframes pathTriangle {
  33% {
    stroke-dashoffset: 74;
  }
  66% {
    stroke-dashoffset: 147;
  }
  100% {
    stroke-dashoffset: 221;
  }
}

@keyframes dotTriangle {
  33% {
    transform: translate(0, 0);
  }
  66% {
    transform: translate(10px, -18px);
  }
  100% {
    transform: translate(-10px, -18px);
  }
}

@keyframes pathRect {
  25% {
    stroke-dashoffset: 64;
  }
  50% {
    stroke-dashoffset: 128;
  }
  75% {
    stroke-dashoffset: 192;
  }
  100% {
    stroke-dashoffset: 256;
  }
}

@keyframes dotRect {
  25% {
    transform: translate(0, 0);
  }
  50% {
    transform: translate(18px, -18px);
  }
  75% {
    transform: translate(0, -36px);
  }
  100% {
    transform: translate(-18px, -18px);
  }
}

@keyframes pathCircle {
  25% {
    stroke-dashoffset: 125;
  }
  50% {
    stroke-dashoffset: 175;
  }
  75% {
    stroke-dashoffset: 225;
  }
  100% {
    stroke-dashoffset: 275;
  }
}

.m-loader {
  display: inline-block;
  margin: 0 16px;
}
</style>
