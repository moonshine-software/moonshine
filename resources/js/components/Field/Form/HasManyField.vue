<script setup lang='ts'>
import TableComponent from '../../ViewComponents/TableComponent.vue'
import { computed, PropType, provide, reactive } from 'vue'
import { Resource } from '../../../entites/resource/base'
import { RelationField } from '../../../entites/fields/base'

const p = defineProps({
    field: {
        type: Object as PropType<RelationField>,
        required: true,
    },
})

</script>

<template>
    <TableComponent :resource-key='field.resource'
                    view-key='crud-index-view'
                    view-component-key='table'>

    </TableComponent>
</template>
