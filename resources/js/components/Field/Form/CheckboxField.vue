<template>
  <field-wrapper :field="field">
    <input
        v-model="field.value.value"
        @change="handleChanges"
        v-bind="field.attributes"
        type="checkbox"
        :id="field.id"
        :name="field.name"
        class="ms-input__checkbox"
        :class="field.class">
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import handlesChanges from "../../../mixins/handlesChanges";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "CheckboxField",
  components: {FieldWrapper},
  mixins: [handlesChanges],
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
