<template>
  <router-link
      :to="{name: 'show', params: {resourceName: field.resource, resourceId: field.value.value} }"
      class="underline text-brand"
      v-text="text"/>
</template>

<script setup lang="ts">
import {RelationField} from "../../../entites/fields/base";

const p = defineProps<{
  field: RelationField
}>()

const text = p.field.formatted_value ?? '-'
</script>
