<template>
<TheCard class="relative">
  <template #header>
    <div class="text-gray-500 z-10">{{ title }}</div>
  </template>

  <div class="w-full text-left text-5xl text-brand z-10 mt-8">{{ value }}</div>
  <div class="absolute right-0 bottom-0 text-8xl text-brand/5 z-10 whitespace-nowrap select-none font-light">{{ value }}</div>
</TheCard>
</template>

<script setup lang="ts">
import TheCard from "../UI/TheCard.vue";

defineProps<{
  title: string,
  value: any
}>()
</script>

<style scoped>

</style>
