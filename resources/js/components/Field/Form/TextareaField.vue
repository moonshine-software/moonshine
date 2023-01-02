<template>
  <field-wrapper :field="field">
    <textarea
        v-model="field.value"
        v-bind="field.attributes"
        :id="field.id"
        :name="field.name"
        class="ms-input ms-input__text"
        :class="field.class"></textarea>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "TextareaField",
  components: {FieldWrapper},
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
