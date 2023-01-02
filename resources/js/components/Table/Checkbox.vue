<template>
  <label class="flex items-center gap-2 text-sm cursor-pointer">
    <input type="checkbox"
           @change.stop="$emit('change', $event)"
           :checked="checked"
           @input.stop="input"
           class="ms-input__checkbox ms-input__checkbox--small"/>
    <span v-if="label">{{ label }}</span>
  </label>
</template>

<script>
export default {
  name: "Checkbox",
  props: {
    checked: {
      type: Boolean,
      default: false
    },
    label: {
      type: String,
      required: false
    }
  },
  setup(_, {emit}) {
    const input = ({target}) => {
      emit('input', target.checked)
    }

    return {
      input
    }
  }
}
</script>
