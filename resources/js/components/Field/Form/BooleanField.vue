<template>
  <field-wrapper :field="field">
      <ul class="ms-input__radio-group"
          :class="field.attributes.class"
      >
        <li>
          <input type="radio" :id="`${field.id}_false`"
                 :value="false"
                 :name="field.name"
                 v-model="field.value">

          <label :for="`${field.id}_false`"
                 :class="{'checked' : field.value === false}">
            {{ field.falseTextLabel ?? t("ui.no") }}
          </label>
        </li>
        <li>
          <input
              type="radio"
              :id="`${field.id}_true`"
              :value="true" :name="field.name"
              v-model="field.value">
          <label
              :for="`${field.id}_true`"
              :class="{'checked' : field.value === true}">
            {{ field.trueTextLabel ?? t("ui.yes") }}
          </label>
        </li>
      </ul>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import {useI18n} from "vue-i18n";
import handlesChanges from "../../../mixins/handlesChanges";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "BooleanField",
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
