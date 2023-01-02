<template>
  <TheButton
      icon="arrow-right-on-rectangle"
      v-tippy="t('ui.logout')"
             sm
      secondary
      square
      @click="handleClick" />
</template>

<script setup lang="ts">
import TheButton from "./TheButton.vue";
import {useUserStore} from "../../../store/user";
import {useI18n} from "vue-i18n";

const handleClick = () => useUserStore().resetUser(),
    {t} = useI18n()
</script>
