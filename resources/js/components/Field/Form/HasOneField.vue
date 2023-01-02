<script setup lang='ts'>
import { PropType} from 'vue'
import { RelationField } from '../../../entites/fields/base'
import FormComponent from '../../ViewComponents/FormComponent.vue'

const p = defineProps({
    field: {
        type: Object as PropType<RelationField>,
        required: true,
    },
})
</script>

<template>
    <FormComponent :resource-key='p.field?.resource'
                   view-key='crud-form-view'
                   view-component-key='form'
                   :resource-id='p.field.value.value'
    />
</template>
