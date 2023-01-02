<template>
  <TheAvatar :avatar="field.value" />
</template>

<script setup lang="ts">
import TheAvatar from "../../UI/TheAvatar.vue";
import {Field} from "../../../entites/fields/base";

defineProps<{field: Field}>()
</script>
