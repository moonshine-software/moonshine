<template>
  <field-wrapper :field="field">
    <CheckCircleIcon
        class="ms-icon ms-icon--green"
        v-if="field.value === true"/>
    <XCircleIcon
        class="ms-icon ms-icon--red"
        v-else/>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType} from "vue";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/vue/24/outline";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "BooleanField",
  components: {FieldWrapper, CheckCircleIcon, XCircleIcon},
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  },
})
</script>
