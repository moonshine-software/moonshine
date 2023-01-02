<template>
  <section>

    <header>
      <h2 class="text-2xl">
        <slot name="title"></slot>
      </h2>
      <div>
        <slot name="header"></slot>
      </div>
    </header>

    <TheCard class="mt-2">
      <main class="space-y-4">
        <slot></slot>
      </main>

      <footer class="mt-4">
        <slot name="footer"></slot>
      </footer>
    </TheCard>

  </section>
</template>

<script setup>
import TheCard from "../components/UI/TheCard.vue";
</script>
