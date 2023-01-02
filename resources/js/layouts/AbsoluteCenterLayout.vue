<template>
  <div class="m-abs-center-page__wrapper">

    <main class="m-abs-center-page">
      <slot></slot>
    </main>

    <footer class="m-abs-center-page__footer">
      <slot name="footer">
        Moonshine. 2022
      </slot>
    </footer>

  </div>
</template>

<script setup>
</script>

<style scoped>
.m-abs-center-page__wrapper {
  @apply w-full h-full flex flex-col items-center justify-center;
}

.m-abs-center-page {
  @apply h-full w-full grid place-content-center;
}

.m-abs-center-page__footer {
  @apply mt-auto w-full text-center py-4;
}

</style>
