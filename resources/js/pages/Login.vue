<template>
  <AbsoluteCenterLayout class="px-6">

    <BrandLogo class="m-login__logo"/>
    <TheCard class="m-login__card">
      <TransitionFade mode="out-in">
        <form
            v-if="!showWelcome"
            @submit.prevent="login"
            class="space-y-4">
          <div>
            <label for="email" class="mt-2">Email</label>
            <TheInput placeholder="email@email.com" id="email" type="email" class="block w-full" v-model="email"/>
          </div>
          <div>
            <label for="password" class="mt-2">Password</label>
            <TheInput placeholder="password" type="password" id="password" class="block w-full" v-model="password"/>
          </div>

          <AsyncButton brand
                       :action="login"
                       :default="defaultButton"
                       @afterDelay:success="loginThen"
                       class="mt-2 w-full"
                       :debounce="50"/>
        </form>
        <div v-else>
          <TheAvatar class="w-28 h-28 mx-auto" :avatar="userAvatar"/>
          <p class="text-4xl text-center mt-4">Welcome, <br/> {{ userName }}</p>
        </div>

      </TransitionFade>
    </TheCard>
  </AbsoluteCenterLayout>
</template>

<script setup lang="ts">
import TheCard from "../components/UI/TheCard.vue";
import TheInput from "../components/UI/Inputs/TheInput.vue";
import AsyncButton from "../components/UI/Buttons/AsyncButton.vue";
import {computed, ref} from "vue";
import {useUserStore} from "../store/user";
import {useAppStore} from "../store/app";
import {useRoute, useRouter} from "vue-router";
import TransitionFade from "../components/Transitions/TransitionFade.vue";
import TheAvatar from "../components/UI/TheAvatar.vue";
import BrandLogo from "../components/UI/BrandLogo.vue";
import AbsoluteCenterLayout from "../layouts/AbsoluteCenterLayout.vue";

const defaultButton = {
  brand: true,
  label: 'Login'
}

const
    minWelcomeViewTime = 2000,
    email = ref(''),
    password = ref(''),
    userStore = useUserStore(),
    userName = computed(() => userStore.name),
    userAvatar = computed(() => userStore.avatar),
    appStore = useAppStore(),
    router = useRouter(),
    route = useRoute(),
    login = () => userStore.login(email.value, password.value),
    showWelcome = ref(false),

    loginThen = () => {
      showWelcome.value = true
      const goTo = route.redirectedFrom ? route.redirectedFrom : {name: 'dashboard'}
      appStore.initialSetup().then(() => setTimeout(() => router.push(goTo), minWelcomeViewTime))
    }

</script>

<style scoped>
.m-login__logo {
  @apply mx-auto w-full w-1/2 md:w-1/4 lg:w-1/6;
}

.m-login__card {
  @apply mx-auto w-full md:w-1/2 lg:w-1/3 mt-6;
}
</style>
