<template>
  <TheIcon
      v-if="field.value === true"
      icon="check-circle"
      :class="field.attributes.class"
      class="m-icon-success"/>
  <TheIcon
      v-else
      icon="x-circle"
      :class="field.attributes.class"
      class="m-icon-danger"/>
</template>

<script setup lang="ts">
import {Field} from "../../../entites/fields/base";
import TheIcon from "../../UI/Icons/TheIcon.vue";

defineProps<{
  field: Field
}>()
</script>
