<template>
  <div class="m-hidden-select__wrapper">
    <slot></slot>
    <select
        v-bind="$attrs"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        class="m-hidden-select"
    >
      <option
          v-for="opt in options"
          :key="`m-h-s-${opt.label ?? opt}`"
          :value="opt.value ?? opt"
      >
        {{ opt.label ?? opt }}
      </option>
    </select>
  </div>
</template>

<script lang="ts" setup>
type SelectOption = { value: any, label: any }

defineProps<{
  modelValue: any,
  options: SelectOption[]|any[]
}>()
</script>
<script lang="ts">
export default {inheritAttrs: false}
</script>

<style>
.m-hidden-select__wrapper {
  @apply relative w-max;
}

.m-hidden-select {
  @apply absolute top-0 w-full h-full opacity-0 cursor-pointer;
}
</style>
