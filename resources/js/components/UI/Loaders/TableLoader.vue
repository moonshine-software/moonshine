<template>
<div class="table-loader"></div>
</template>

<style scoped>
.table-loader {
  @apply w-full h-2 rounded-full opacity-0 transition-opacity;
  background: linear-gradient(-75deg, transparent 30%, theme('colors.brand.100') 50%, transparent 70%) 0 0 / 200% 100%, theme('colors.brand.400');
  background-size: 200% 100%;
  background-attachment: fixed;
  animation: table-loader 1.2s ease-in-out infinite;
}

@keyframes table-loader {
  to {
    background-position: -200% 0;
  }
}
</style>
