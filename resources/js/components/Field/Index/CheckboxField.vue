<template>
  <boolean-field-input :field="field" />
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import BooleanFieldInput from "./BooleanField.vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "CheckboxField",
  components: {BooleanFieldInput},
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
