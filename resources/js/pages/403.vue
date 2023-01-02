<template>
<AbsoluteCenterLayout>
  <TheCard class="px-20 py-10">
    <h1 class="text-8xl text-center w-full">403</h1>
    <p class="text-2xl">You have not permissions</p>
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
