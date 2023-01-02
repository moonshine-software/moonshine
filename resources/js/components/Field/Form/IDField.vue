<template>
  <field-wrapper :field="field">
    <div
        v-bind="field.attributes">
      {{ field.value }}
    </div>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "IDField",
  components: {FieldWrapper},
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
