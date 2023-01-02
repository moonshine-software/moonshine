<template>
  <field-wrapper :field="field">
    <div
        v-bind="field.attributes"
        :id="field.id"
        class="ms-field__color__wrapper">
      <span class="ms-field__color__color-box--view" :style="{backgroundColor: field.value}"></span>
      <span>{{ field.value }}</span>
    </div>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "ColorField",
  components: {FieldWrapper},
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
