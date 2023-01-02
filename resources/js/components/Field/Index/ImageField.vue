<template>
    <div
        class="w-10 h-10 rounded bg-center bg-cover"
        v-bind="field.attributes"
        :style="{backgroundImage: `url(${field.value ?? field.url})`}"
        :class="field.class">
    </div>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "ImageField",
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
