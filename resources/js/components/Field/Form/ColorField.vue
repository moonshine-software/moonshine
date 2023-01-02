<template>
  <field-wrapper :field="field">
    <div class="ms-input__color__wrapper">
      <label class="ms-input__color__selector" :style="{backgroundColor: field.value.value}">
        <input
            v-model="field.value.value"
            type="color"
            :id="`color-${field.id}`"
            class="w-0 h-0 opacity-0"
            @input="handleChanges"
        >
      </label>

      <TheInput
          v-model="field.value.value"
          v-bind="field.attributes"
          type="text"
          :id="field.id"
          :name="field.name"
          @input="handleChanges"
          class="ms-input__color"
          :class="field.class"/>
    </div>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import handlesChanges from "../../../mixins/handlesChanges";
import {Field} from "../../../entites/fields/base";
import TheInput from "../../UI/Inputs/TheInput.vue";

export default defineComponent({
  name: "ColorField",
  components: {TheInput, FieldWrapper},
  mixins: [handlesChanges],
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>

<style lang="scss">
.ms-input__color {
  @apply pl-14;

  &__wrapper {
    @apply flex items-center relative;
  }

  &__selector {
    @apply h-10 w-10 absolute rounded-lg left-1.5 cursor-pointer;
  }
}
</style>
