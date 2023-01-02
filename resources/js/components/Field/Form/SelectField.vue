<template>
  <field-wrapper :field="field">
    <select
        v-model="field.value.value"
        @change="handleChanges"
        v-bind="field.attributes"
        :id="field.id"
        :name="field.name"
        class="ms-input ms-input__text form-select"
        :class="field.class">
      <option :value="null" :disabled="!field.nullable" :selected="!field.value">
        {{ t("ui.select_placeholder") }}
      </option>
      <option
          v-for="(item, i) in field.items"
          :key="i + item.value"
          :value="item.value"
      >{{ item.label }}</option>
    </select>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import handlesChanges from "../../../mixins/handlesChanges";
import {useI18n} from "vue-i18n";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "SelectField",
  components: {FieldWrapper},
  mixins: [handlesChanges],
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  },
  setup() {
    const {t} = useI18n()

    return {
      t
    }
  }
})
</script>
