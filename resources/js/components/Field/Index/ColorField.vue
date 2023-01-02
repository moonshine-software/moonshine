<template>
  <div
      v-bind="field.attributes"
      class="ms-field__color__wrapper"
      :class="field.class">
    <span class="ms-field__color__color-box--index" :style="{backgroundColor: field.value}"></span>
    <span>{{ field.value }}</span>
  </div>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "ColorField",
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
