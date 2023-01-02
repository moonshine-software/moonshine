<template>
  <FieldWrapper :field="field">
    <TheInput
        class="w-full"
        v-model="field.value"
        v-bind="field.attributes"
        :id="field.id"
        :name="field.name"/>
  </FieldWrapper>
</template>

<script setup lang="ts">
import FieldWrapper from "../../FieldWrapper.vue";
import {PropType} from "vue";
import {Field} from "../../../entites/fields/base";
import TheInput from "../../UI/Inputs/TheInput.vue";

defineProps({
  field: {
    type: Object as PropType<Field>,
    required: true
  }
})
</script>
