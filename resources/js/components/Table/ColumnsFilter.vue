<template>
  <TheDropdown>
    <template #trigger>
      <TheButton icon="view-columns" secondary square brand sm/>
    </template>

    <TheCheckbox
        v-for="column in columns"
        :value="column.label"
        :checked="column.visible"
        @change="column.switchVisible()">
      {{ column.label }}
    </TheCheckbox>

  </TheDropdown>
</template>

<script setup lang="ts">
import {useI18n} from "vue-i18n";
import {TableColumn} from "../../entites/table/column";
import TheDropdown from "../UI/TheDropdown.vue";
import TheButton from "../UI/Buttons/TheButton.vue";
import TheCheckbox from "../UI/Inputs/TheCheckbox.vue";

defineProps<{
  columns: TableColumn[]
}>()
const {t} = useI18n()
</script>
