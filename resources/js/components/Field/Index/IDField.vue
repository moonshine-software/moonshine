<template>
  <TextField :field="field" class="font-bold"/>
</template>

<script setup lang="ts">
import TextField from "./TextField.vue";
import {Field} from "../../../entites/fields/base";

defineProps<{
  field: Field
}>()
</script>
