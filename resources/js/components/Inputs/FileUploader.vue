<template>
  <section>
    <section :class="{'error': hasValidationErrors && !emitNotValid}" class="ms-input ms-input__file-uploader" @click="simulateClick">
      <div v-if="!file && !fileLabel" class="h-full w-full">
        {{ t("ui.drag_drop_file") }}
      </div>

      <div v-else class="ms-input__file-uploader__preview__wrapper">

        <img v-if="previewUrl" :alt="fileLabel" :src="previewUrl" class="ms-input__file-uploader__preview"/>

        <div class="ms-input__file-uploader__text-wrapper">
          <span>{{ fileLabel || file.name }}<br/>
            <i v-if="file">{{ Math.round(file.size / 1024) }} кб</i>
          </span>
          <div v-if="file || fileLabel" class="text-right">
            <BackwardIcon v-if="uploading" class="ms-input__file-uploader__spinner"/>
            <XCircleIcon class="ms-input__file-uploader__delete" @click.stop="clearFile" v-else />
          </div>
        </div>

      </div>

      <input :id="name"
             ref="fileInput"
             :accept="accepts"
             :disabled="!enabledInput"
             :name="name"
             class="hidden"
             type="file"
             @change="handleFileUpload"/>
    </section>
    <small v-if="hasValidationErrors" class="text-danger-500 block">
      <span v-for="error in validationErrors">
        {{ t(error.key, error.params) }}
      </span>
    </small>
  </section>
</template>

<script lang="ts">
import {computed, defineComponent, Ref, ref, watch} from "vue";
import {useI18n} from "vue-i18n";
import {XCircleIcon, BackwardIcon} from "@heroicons/vue/24/outline";

export default defineComponent({
  name: "FileUploader",
  components: {
    XCircleIcon, BackwardIcon
  },
  props: {
    fileLabel: {
      type: String,
      default: null
    },
    name: {
      type: String,
      default: 'file'
    },
    id: {
      type: String,
      default: 'file_uploader'
    },
    previewUrl: {
      type: String,
      default: null
    },
    uploading: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    accepts: {
      type: String,
      required: false
    },
    maxSize: {
      type: Number,
      default: 0
    },
    emitNotValid: {
      type: Boolean,
      default: false
    },
    value: {
      required: true
    }
  },
  setup(p, {emit}) {
    const file: Ref<File | string | null> = ref(null)
    const fileUploaded: Ref<boolean> = ref(false)
    const fileInput: Ref<HTMLInputElement | null> = ref(null)
    const {t} = useI18n()

    watch(() => p.value, (newValue: string | null) => {
      file.value = newValue
    })

    const enabledInput = computed(() => !p.disabled && !file.value && !p.uploading && !p.fileLabel)
    const validationErrors = computed(() => {
      const result: Object[] = []
      if (file.value && typeof file.value !== "string") {
        if (p.maxSize && file.value?.size > p.maxSize * 1024)
          result.push({key: "ui.validation.file_size", params: {maxSize: p.maxSize}})

        if (p.accepts?.indexOf(file.value.type) === -1)
          result.push({key: "ui.validation.unsupported_file_type", params: {}})
      }

      return result
    })
    const hasValidationErrors = computed(() => {
      return validationErrors.value.length > 0
    })

    const handleFileUpload = () => {
      file.value = fileInput.value.files[0]
      emit('shine:fileUploaded')
      emit('shine:fileValidation', hasValidationErrors.value)
      if (hasValidationErrors.value && !p.emitNotValid) {
        return
      }
      emit('shine:input', file.value)
    }

    const simulateClick = () => {
      if (enabledInput.value) fileInput.value?.click()
    }

    const clearFile = () => {
      file.value = null
      if(fileInput.value) fileInput.value.files = null;

      emit('shine:input', file.value)
      emit('shine:fileCleared', file.value)
    }

    return {
      t,
      file,
      fileInput,
      enabledInput,
      validationErrors,
      hasValidationErrors,
      fileUploaded,
      handleFileUpload,
      simulateClick,
      clearFile
    }
  },
})
</script>
