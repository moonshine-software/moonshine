<template>
  <div class="ms-field__wrapper">
    <label class="ms-field__label" :for="field.id" v-once>
      <span>{{ field.label }}</span>
      <span class="text-danger-400" v-if="field.attributes.required">*</span>
      <QuestionMarkCircleIcon
          v-if="field.tippy"
          v-tippy="field.tippy"
          class="ms-icon"/>
    </label>
    <div class="ms-input__wrapper">
      <slot></slot>
      <small v-if="field.hint" class="ms-field__hint" v-once>{{ field.hint }}</small>
    </div>
  </div>
</template>

<script setup lang="ts">
import {QuestionMarkCircleIcon} from "@heroicons/vue/24/solid";
import {IField} from "../entites/fields/base";

defineProps<{
  field: IField,
  compact?: boolean,
  showRequired?: boolean
}>()
</script>
