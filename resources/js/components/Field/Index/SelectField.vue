<template>
    <text-field :field="field"/>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import TextField from "./TextField.vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "SelectField",
  components: {TextField},
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  }
})
</script>
