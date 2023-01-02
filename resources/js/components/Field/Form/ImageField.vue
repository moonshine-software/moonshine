<template>
  <field-wrapper :field="field">
    <FileUploader
        :file-label="fileLabel ?? url"
        :preview-url="preview ?? url"
        :accepts="field.accept"
        :name="field.name"
        :id="field.id"
        :class="field.class"
        :value="file"
        @shine:file-cleared="clearFile"
    />
  </field-wrapper>
</template>

<script lang="ts">

import FieldWrapper from "../../FieldWrapper.vue";
import {defineComponent, PropType, ref, Ref} from "vue";
import handlesChanges from "../../../mixins/handlesChanges";
import FileUploader from "../../Inputs/FileUploader.vue";
import {Field} from "../../../entites/fields/base";

export default defineComponent({
  name: "ImageField",
  components: {FileUploader, FieldWrapper},
  mixins: [handlesChanges],
  props: {
    field: {
      type: Object as PropType<Field>,
      required: true
    }
  },
  setup() {
    const file: Ref<File|null> = ref(null)
    const preview: Ref<string|null|undefined> = ref(field.preview)
    const url: Ref<string|null|undefined> = ref(field.url)
    const fileLabel: Ref<string|null|undefined> = ref(field.image_label)

    const clearFile = () => {
      file.value = null
      preview.value = null
      url.value = null
      fileLabel.value = null
    }

    return {
      file,
      preview,
      url,
      fileLabel,
      clearFile
    }
  }
})
</script>
