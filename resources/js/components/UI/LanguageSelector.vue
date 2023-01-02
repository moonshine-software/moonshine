<template>
  <select v-model="locale" class="form-select text-xs rounded-2xl pr-7 pl-2 py-1">
    <option
      v-for="loc in availableLocales"
      :value="loc">
      {{ loc }}
    </option>
  </select>
</template>

<script>
import {useI18n} from "vue-i18n";

export default {
  name: "LanguageSelector",
  setup(){
    const {locale, availableLocales} = useI18n()
    //todo: save to local storage and reload page.
    return {locale, availableLocales}
  }
}
</script>
