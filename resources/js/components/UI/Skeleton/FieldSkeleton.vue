<template>
<div class="skeleton"></div>
</template>

<script>
export default {
  name: "FieldSkeleton"
}
</script>

<style lang="scss" scoped>
.skeleton {
  @apply min-w-[40px] w-full h-6 rounded-2xl;
  background: linear-gradient(-75deg, transparent 30%, theme('colors.secondary.300') 50%, transparent 70%) 0 0 / 200% 100%, theme('colors.secondary.200');
  animation: skeleton-loading 1.2s ease-in-out infinite;

  .dark & {
    background: linear-gradient(-75deg, transparent 30%, theme('colors.secondary.500') 50%, transparent 70%) 0 0 / 200% 100%, theme('colors.secondary.700');
  }
}

@keyframes skeleton-loading {
  to {
    background-position: -200% 0;
  }
}
</style>
