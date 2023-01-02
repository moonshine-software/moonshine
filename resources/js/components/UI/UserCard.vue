<template>
  <div class="flex items-center gap-4">
    <TheAvatar :avatar="avatar"/>
    <div>
      <div>{{ name }}</div>
      <div class="flex gap-1 items-center">
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useI18n} from "vue-i18n";
import TheAvatar from "./TheAvatar.vue";
import {useUserStore} from "../../store/user";
import {computed} from "vue";
import TheIcon from "./Icons/TheIcon.vue";

const
    userStore = useUserStore(),
    name = computed(() => userStore.name),
    avatar = computed(() => userStore.avatar),
    {t} = useI18n()
</script>
