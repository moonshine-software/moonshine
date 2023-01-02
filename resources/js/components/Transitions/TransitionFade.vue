<template>
<Transition :mode="mode">
  <slot></slot>
</Transition>
</template>

<script setup lang="ts">
defineProps({
  mode: {
    type: String,
  }
})
</script>

<style scoped>
.v-enter-active, .v-leave-active {   transition: opacity 0.1s ease; }  .v-enter-from, .v-leave-to {   opacity: 0; }
</style>
