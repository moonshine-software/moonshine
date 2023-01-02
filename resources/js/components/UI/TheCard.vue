<template>
  <div class="m-card">
    <header>
      <slot name="header"></slot>
    </header>

    <main>
      <slot>

      </slot>
    </main>

    <footer>
      <slot name="footer"></slot>
    </footer>
  </div>
</template>

<script setup>

</script>

<style lang="scss">
@import "../../sass/mixins";

.m-card {
  @include cardBgGradient;
  @apply bg-secondary-50 dark:bg-secondary-700 border-secondary-200/40 dark:border-secondary-700/40
  border rounded-2xl shadow-xl shadow-brand/[0.04]
  p-5 py-4 relative;
}
</style>
