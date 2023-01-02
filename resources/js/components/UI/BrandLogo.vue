<template>
  <img :src="logo ?? Logo" alt="Moonshine" >
</template>

<script setup>
import {computed} from "vue";
import Logo from '../../assets/moonshine-full-big.svg'
import {useAppStore} from "../../store/app";

const
    app = useAppStore(),
    logo = computed(() => app.theme.logo)
</script>
