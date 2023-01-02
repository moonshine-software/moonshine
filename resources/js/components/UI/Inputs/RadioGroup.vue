<template>
  <div class="flex gap-2 items-center">
    <div v-for="opt in options" :key="getLabel(opt)">
      <label :for="getLabel(opt)" :class="{'checked': modelValue === getValue(opt)}">{{getLabel(opt)}}</label>
      <input :id="getLabel(opt)" :name="`radio-${name}`" type="radio" :value="getValue(opt)" @change="$emit('update:modelValue', getValue(opt))">
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  modelValue: any,
  name: string,
  options: {value: any, label?: string}[] | any[]
}>()

function getLabel(opt: any) {
  return  (opt.label ?? opt.value ?? opt)
}
function getValue(opt: any) {
  return opt.value ?? opt
}
</script>

<style scoped lang="scss">
label {
  @apply select-none p-2 rounded border text-sm cursor-pointer;
  &.checked {
    @apply text-brand-500;
  }
}
input {
  @apply hidden;
}
</style>
