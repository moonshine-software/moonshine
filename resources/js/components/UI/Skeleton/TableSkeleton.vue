<template>

  <CardBlockLayout v-show="display">
    <template #title>
      <FieldSkeleton class="w-1/3"/>
    </template>

    <table class="moonshine-skeleton-table">
      <tbody>
      <tr v-for="i in trCount" :key="i">
        <td v-for="t in tdCount" :key="t">
          <FieldSkeleton/>
        </td>
      </tr>
      </tbody>
    </table>

    <template #footer>
      <FieldSkeleton class="w-1/2 mx-auto"/>
    </template>
  </CardBlockLayout>
</template>

<script setup>
import FieldSkeleton from "./FieldSkeleton.vue";
import CardBlockLayout from "../../../layouts/CardBlockLayout.vue";
import {ref} from "vue";

const trCount = 6
const tdCount = 1

// show debouncing
const display = ref(false)
setTimeout(() => display.value = true, 500)

</script>

<style lang="scss">
.moonshine-skeleton-table {
  @apply w-full;

  tr {
    @apply h-12 p-2 flex gap-2 w-full;
  }

  td {
    @apply w-full h-full rounded-xl;
  }
}
</style>
