<template>
  <TheButton
      @click.prevent="runAction"
      v-bind="state"
      reactive
      :class="$attrs.class">
    {{ state.label }}
  </TheButton>
</template>

<script setup lang="ts">
import TheButton from "./TheButton.vue";
import {ref} from "vue";
import {TheButtonProps} from "../../../types/theButtonProps";

const emit = defineEmits<{
  (e: 'action:start'): void,
  (e: 'action:success', value: any): void,
  (e: 'afterDelay:success', value: any): void,
  (e: 'afterDelay:error', value: any): void,
  (e: 'action:error', value: any): void
}>()

const props = withDefaults(defineProps<{
      default: TheButtonProps & { label: string },
      action: () => Promise<any>,
      successDelay?: number,
      errorDelay?: number,
      debounce?: number
    }>(),
    {successDelay: 1000, errorDelay: 1500, debounce: 50})

const buttonStates = {
  default: props.default,
  loading: {
    secondary: true,
    icon: 'spinner',
    label: 'Wait...'
  },
  success: {
    success: true,
    icon: 'check',
    label: 'Success'
  },
  error: {
    danger: true,
    icon: 'x-circle',
    label: 'Error'
  }
}
const state = ref(buttonStates['default']);
const started = ref(false)

const thenAction = (ctx: any, type: 'error' | 'success') => {
  emit('action:' + type, ctx)
  const delay = type === 'error' ? props.errorDelay : props.successDelay

  state.value = buttonStates[type]

  setTimeout(() => {
    emit('afterDelay:' + type, ctx)
    started.value = false
    state.value = buttonStates['default']
  }, delay);
}

const runAction = () => {
  if (started.value) return

  started.value = true
  emit('action:start')
  debounceWaitingState()

  props.action()
      .then(ctx => thenAction(ctx, 'success'))
      .catch(ctx => thenAction(ctx, 'error'))
}

const debounceWaitingState = () => setTimeout(() => {
  if (started.value === true)
    state.value = buttonStates['loading']
}, props.debounce)

</script>
