<template>
  <img class="m-avatar" v-bind="$attrs" :src="avatar" v-if="avatar" loading="lazy" />
  <div class="m-avatar__empty" v-bind="$attrs" v-else>
    <TheIcon icon="user" class="h-full w-full mx-auto"/>
  </div>
</template>

<script setup lang="ts">
import TheIcon from "./Icons/TheIcon.vue";

defineProps<{
  avatar?: string|null
}>()
</script>

<style scoped lang="scss">
.m-avatar {
  @apply w-12 h-12 rounded-full ring-2 ring-brand-500 bg-secondary-400;
}

.m-avatar__empty {
  @extend .m-avatar;
  @apply bg-brand-600 text-white p-3;
}
</style>
