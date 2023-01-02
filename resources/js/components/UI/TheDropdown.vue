<template>
  <div class="m-dropdown" @keydown.esc="closeModal">
    <Teleport to="body">
      <div class="m-dropdown__close-area"
           v-if="modalOpened"
           @keydown.esc="closeModal"
           @click="closeModal">
      </div>
    </Teleport>

    <div @click.prevent="openModal">
      <slot name="trigger"></slot>
    </div>

    <TransitionFade>
      <TheCard v-if="modalOpened" class="m-dropdown__modal">
        <slot></slot>
      </TheCard>
    </TransitionFade>
  </div>
</template>

<script setup lang="ts">
import {ref, Ref} from "vue";
import TransitionFade from "../Transitions/TransitionFade.vue";
import TheCard from "./TheCard.vue";

const modalOpened: Ref<boolean> = ref(false)
const closeModal = () => modalOpened.value = false;
const openModal = () => modalOpened.value = true;

</script>

<style lang="scss">
.m-dropdown {
  @apply relative;

  &__close-area {
    @apply opacity-0 w-screen h-full absolute top-0 left-0;
  }

  &__modal {
    @apply absolute right-0 mt-2 z-20 top-full;
  }
}
</style>
