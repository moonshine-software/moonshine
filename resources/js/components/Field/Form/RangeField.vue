<template>
  <field-wrapper :field="field">
    <div class="ms-input__range__wrapper">
      <input
          v-model="field.value.value"
          v-bind="field.attributes"
          type="range"
          :id="field.id"
          :name="field.name"
          class="ms-input ms-input__range"
          @change="handleChanges"
          :class="field.class">
      <div>{{ field.value.value }}</div>
    </div>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import handlesChanges from "../../../mixins/handlesChanges";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "RangeField",
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
