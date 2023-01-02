<template>
  <TextField :field="field" />
</template>

<script setup lang="ts">
import {Field} from "../../../entites/fields/base";
import TextField from "./TextField.vue";

defineProps<{field: Field}>()
</script>
