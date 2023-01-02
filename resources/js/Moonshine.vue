<script setup lang="ts">
import Loader from "./components/UI/Loader.vue";
import TransitionFade from "./components/Transitions/TransitionFade.vue";
import TheNotification from "./components/UI/TheNotification.vue";
import {useAppStore} from "./store/app";
import {computed} from "vue";
import {useUserStore} from "./store/user";

const appStore = useAppStore(),
    userStore = useUserStore(),
    appLoaded = computed(() => appStore.loaded)

appStore.initialSetup()
userStore.check()
</script>

<template>
  <TheNotification/>
  <router-view v-slot="{ Component, route }">
    <TransitionFade mode="out-in">
      <component :is="Component" v-if="appLoaded"/>
      <Loader v-else/>
    </TransitionFade>
  </router-view>
</template>
