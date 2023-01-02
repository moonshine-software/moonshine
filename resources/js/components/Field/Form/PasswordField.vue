<template>
  <field-wrapper :field="field">
    <div class="flex items-center relative">
      <EyeIcon
          class="ms-input__password__icon"
          :class="{'text-brand-500': showPassword}"
          @click.stop="switchShow"
      />
      <input
          v-model="field.value.value"
          @input="handleChanges"
          v-bind="field.attributes"
          :type="type"
          :id="field.id"
          :name="field.name"
          class="ms-input ms-input__password"
          :class="field.class">
    </div>
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType, ref} from "vue";
import handlesChanges from "../../../mixins/handlesChanges";
import {EyeIcon} from "@heroicons/vue/24/outline";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "PasswordField",
  components: {FieldWrapper, EyeIcon},
  mixins: [handlesChanges],
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  },
  setup() {
    const showPassword = ref(false)
    const type = ref('password')

    const switchShow = () => {
      showPassword.value = !showPassword.value
      if(showPassword.value){
        type.value = 'text'
      } else {
        type.value = 'password'
      }
    }

    return {
      type,
      switchShow,
      showPassword
    }
  }
})
</script>
