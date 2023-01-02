<template>
  <text-field
      :field="field"
      class="text-ellipsis overflow-hidden max-w-xs"/>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import TextField from "./TextField.vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "Textarea",
  components: {TextField},
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
