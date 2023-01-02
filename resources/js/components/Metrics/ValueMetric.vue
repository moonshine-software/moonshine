<template>
  <BaseMetricCard :title="title" :value="value" />
</template>

<script setup lang="ts">
import BaseMetricCard from "./BaseMetricCard.vue";

defineProps<{
  title: string,
  value: any
}>()
</script>
