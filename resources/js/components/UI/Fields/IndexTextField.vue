<template>
  <div
      v-bind="field.attributes"
      v-text="field.value">
  </div>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import FieldWrapper from "../../FieldWrapper.vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "IndexTextField",
  components: {FieldWrapper},
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
