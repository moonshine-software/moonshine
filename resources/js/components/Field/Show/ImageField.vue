<template>
  <field-wrapper :field="field">

    <img
        v-if="field.preview ?? field.url"
        class="max-w-md rounded-md"
        v-bind="field.attributes"
        :src="field.preview ?? field.url"
        :alt="field.image_label"
        :class="field.class"/>
    <p class="mt-2">{{ field.image_label }}</p>
    <small class="text-secondary-500 block">{{ field.url }}</small>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "ImageField",
  components: {FieldWrapper},
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
