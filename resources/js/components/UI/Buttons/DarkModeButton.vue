<template>
  <TheButton
      sm
      square
      :warning="isSet"
      :brand="!isSet"
      reactive
      @click.prevent="switchMode"
      class="overflow-hidden">
    <Transition name="slide-up" mode="out-in">
      <TheIcon icon="sun" class="flex-none m-btn__icon" v-if="isSet"/>
      <TheIcon icon="moon" class="flex-none m-btn__icon" v-else/>
    </Transition>
  </TheButton>
</template>

<script setup lang="ts">

import {computed} from "vue";
import TheIcon from "../Icons/TheIcon.vue";
import TheButton from "./TheButton.vue";
import {useAppStore} from "../../../store/app";

const appStore = useAppStore()
const isSet = computed(() => appStore.isDarkModeTheme)
const switchMode = () => appStore.switchTheme()
</script>

<style>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.25s ease-out;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(30px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}
</style>
