<template>
  <TransitionFade>
    <Teleport to="body">
      <div
          v-if="show"
          :class="{'m-modal__wrapper--center': position === 'center'}"
          class="m-modal__wrapper"
          @keydown.esc="closeModal"
          @click.self="closeModal">

        <TheCard class="m-modal" v-bind="$attrs">
          <slot></slot>
        </TheCard>

      </div>
    </Teleport>
  </TransitionFade>
</template>

<script lang="ts">
export default {
  inheritAttrs: false
}
</script>

<script setup lang="ts">
import TransitionFade from "../../Transitions/TransitionFade.vue";
import TheCard from "../TheCard.vue";

const p = defineProps<{
  show?: boolean,
  position?: 'top' | 'center'
}>()

const emit = defineEmits<{
  (e: 'update:show', value: boolean): void
}>()

const closeModal = () => {
  emit('update:show', false)
};

</script>

<style lang="scss">
.m-modal__wrapper {
  @apply w-screen h-screen absolute top-0 left-0 z-50
  bg-secondary-200/90 dark:bg-secondary-700/90 p-4;

  &--center {
    @apply flex flex-col justify-center;
  }
}

.m-modal {
  @apply md:w-1/2 mx-auto;

}

.m-modal__action-bar {
  @apply flex justify-end items-center
}
</style>
