<template>
  <div @click="show = true" v-bind="$attrs">
    <slot name="trigger"></slot>
  </div>
  <TheModal v-model:show="show" position="center" class="m-delete-conf-modal">
    <div class="m-delete-conf-modal__icon">
      <TheIcon icon="trash" class="w-10 h-10" :class="{'m-delete-conf-modal__icon--loading': loading}"/>
    </div>
    <p class="m-delete-conf-modal__text">
      Are you sure to delete this item?
    </p>
    <div class="flex gap-2">
      <TheButton
          @click="show = false"
          secondary
          class="w-full">
        Cancel
      </TheButton>
      <AsyncButton
          :default="{
            label: 'Delete',
            danger: true,
            icon: 'trash'
            }"
          :action="onConfirm"
          @action:start="loading = true"
          @after-delay:success="confirmDelete"
      />
    </div>
  </TheModal>
</template>

<script setup lang="ts">
import TheModal from "./TheModal.vue";
import {ref} from "vue";
import TheIcon from "../Icons/TheIcon.vue";
import TheButton from "../Buttons/TheButton.vue";
import AsyncButton from "../Buttons/AsyncButton.vue";

const show = ref(false)
const loading = ref(false)

const emit = defineEmits<{ (e: 'delete'): void }>()
const p = defineProps<{
  closeOnDelete?: boolean,
  //todo: change any ?
  onConfirm: () => Promise<any>
}>()


const confirmDelete = () => {
  emit('delete')
  loading.value = false
  show.value = false
  //todo: action ?
}

</script>

<style lang="scss">

.m-delete-conf-modal {
  @apply max-w-lg w-max px-6 py-6;
}

.m-delete-conf-modal__text {
  @apply mt-6 mb-8 mx-auto text-center
}

.m-delete-conf-modal__icon {
  @apply p-2 rounded-2xl bg-danger/10 w-max text-danger mx-auto ring-2 ring-danger overflow-hidden;

  &--loading {
    @apply transition-transform;
    animation: loading 1.5s ease-in-out infinite;
  }

  @keyframes loading {
    0% {
      transform: translateY(0%);
    }
    50% {
      transform: translateY(200%) scale(50%);
    }
    50.1% {
      transform: translateY(-200%) scale(200%);
    }
  }
}

</style>
