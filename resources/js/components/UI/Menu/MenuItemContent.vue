<template>
  <div class="ms-menu__item__content">
    <span v-if="badge"
          class="ms-menu__item__badge">
      {{ badge }}
    </span>
    <TheIcon :icon="icon"/>
    <span class="ms-menu__item__text">{{ title }}</span>
  </div>
</template>

<script setup lang="ts">
import TheIcon from "../Icons/TheIcon.vue";

defineProps({
  icon: String,
  title: String,
  badge: [Number, String]
})
</script>

<style lang="scss">
.ms-menu__item__content {
  @apply flex items-center gap-2 relative;
  //mobile
  @apply flex-col rounded-3xl w-24 h-24 bg-secondary-300 dark:bg-secondary-900 justify-center;

  //desktop
  @apply md:flex-row md:border-none md:justify-start md:w-auto md:h-auto md:rounded-none md:bg-transparent md:dark:bg-transparent;
}

.ms-menu__item__text {
  @apply w-full text-ellipsis truncate text-center px-4 text-xs;

  // desktop
  @apply md:text-sm md:px-0 md:text-left;
}

.ms-menu__item__badge {
  @apply px-2 py-1 text-secondary-50 bg-danger-500 text-xs rounded-full;
  @apply absolute -right-2 -top-2;

  //desktop
  @apply md:right-0 md:top-0;
}
</style>
