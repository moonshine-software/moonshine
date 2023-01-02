<template>
  <TextField :field="field"/>
</template>

<script setup lang="ts">
import TextField from "./TextField.vue";
import {Field} from "../../../entites/fields/base";

defineProps<{
  field: Field
}>()
</script>
