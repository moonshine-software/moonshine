<template>
  <div class="relative flex items-center">
    <TheIcon icon="magnifying-glass" class="text-secondary-400 absolute left-2"/>
    <TheInput
        class="pl-9 sm"
        id="shine-search"
        name="shine-search"
        v-bind="$attrs"
        autocomplete="off"
        :placeholder="t('ui.search')"
        :model-value="modelValue"
        @input="handleInput($event)"
    >
    </TheInput>
  </div>
</template>

<script setup lang="ts">
import {useI18n} from "vue-i18n";
import TheInput from "./Inputs/TheInput.vue";
import TheIcon from "./Icons/TheIcon.vue";
import _debounce from "lodash/debounce";

const emit = defineEmits<{
  (e: 'update:modelValue', value: InputEvent): void
}>()
const props = withDefaults(defineProps<{
  modelValue: string,
  debounce?: number,
}>(), {debounce: 500})

const {t} = useI18n()

const handleInput = _debounce((e: InputEvent) => {
    emit('update:modelValue', e.target.value)
}, props.debounce)

</script>
