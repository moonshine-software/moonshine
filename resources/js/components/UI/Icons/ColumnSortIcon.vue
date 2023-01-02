<template>
  <ChevronDownIcon class="w-6 h-6 inline-block" v-if="direction === 'ASC'"/>
  <ChevronDownIcon class="w-6 h-6 rotate-180 inline-block" v-else/>
</template>

<script setup lang="ts">
import {ChevronDownIcon} from "@heroicons/vue/20/solid";
import {TableSortPayload} from "../../../entites/table/base";

defineProps<{ direction: TableSortPayload['direction'] }>()
</script>
