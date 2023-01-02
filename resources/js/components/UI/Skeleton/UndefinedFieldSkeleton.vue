<template>
  <div class="bg-danger-500/10 text-danger ring-1 ring-danger rounded-full py-2 px-4 text-center">
    Component {{ field.component }} not exists
  </div>
</template>

<script setup lang="ts">
import {Field} from "../../../entites/fields/base";

defineProps<{ field: Field }>()
</script>
