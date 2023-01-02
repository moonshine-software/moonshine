<template>
<AbsoluteCenterLayout>
  <TheCard class="px-20 py-10">
    <h1 class="text-8xl">404</h1>
    <p class="text-2xl">Page not found</p>
    <router-link to="/" tabindex="-1">
      <TheButton class="mx-auto mt-4" sm>Dashboard</TheButton>
    </router-link>
  </TheCard>
</AbsoluteCenterLayout>
</template>

<script>
import PageLayout from "../layouts/PageLayout.vue";
import TheCard from "../components/UI/TheCard.vue";
import AbsoluteCenterLayout from "../layouts/AbsoluteCenterLayout.vue";
import TheButton from "../components/UI/Buttons/TheButton.vue";
export default {
  name: "404",
  components: {TheButton, AbsoluteCenterLayout, TheCard, PageLayout}
}
</script>

<style scoped>

</style>
