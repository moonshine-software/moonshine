<template>
  <notifications :duration="6000">
    <template #body="props">
      <div class="m-notify" :class="classes[props.item.type]">
        <div class="m-notify__icon-wrapper">
          <TheIcon class="m-notify__icon" :icon="getIcon(props.item.type)"/>
        </div>
        <div class="m-notify__body">
          <p class="m-notify__title">
            {{ props.item.title }}
          </p>
          <p class="m-notify__message" v-html="props.item.text"/>
        </div>
      </div>
    </template>
  </notifications>
</template>

<script setup lang="ts">
import TheIcon from "./Icons/TheIcon.vue";

const getIcon = (type: string) => {
  switch (type) {
    case 'warn':
    case 'error':
      return 'exclamation'
    case 'success':
      return 'check-circle'
    default:
        return 'information-circle'
  }
}

const classes = {
  warn: 'm-notify--warn',
  success: 'm-notify--success',
  error: 'm-notify--error'
}
</script>

<style lang="scss">
.m-notify {
  @apply rounded-2xl flex gap-4 items-stretch relative max-w-xs md:max-w-sm my-2 mx-2
  bg-secondary-200 text-secondary-600 overflow-hidden shadow-secondary-400/30;
}

.m-notify__icon-wrapper {
  @apply bg-secondary-300 flex px-4 py-2;
}

.m-notify__icon {
  @apply my-auto;
}

.m-notify__body {
  @apply py-2 pr-4;
}

.m-notify__title {
  @apply font-bold;
}

.m-notify--warn {
  @apply bg-warning-400 text-warning-900;
  .m-notify__icon-wrapper { @apply bg-warning-500; }
}

.m-notify--success {
  @apply bg-success-400 text-success-50;
  .m-notify__icon-wrapper { @apply bg-success-500; }
}

.m-notify--error {
  @apply bg-danger-400 text-danger-50;
  .m-notify__icon-wrapper { @apply bg-danger-500; }
}

</style>
