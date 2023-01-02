<script setup lang="ts">
import {computed, PropType, provide, reactive} from "vue";
import {ResourceForm} from "../../../entites/form";
import {RelationField} from "../../../entites/fields/base";
import DetailCardComponent from '../../ViewComponents/DetailCardComponent.vue'
const p = defineProps({
    field: {
        type: Object as PropType<RelationField>,
        required: true
    }
})
</script>

<template>
    <DetailCardComponent :resource-key='p.field?.resource'
                   view-key='crud-detail-view'
                   view-component-key='detail-card'
                   :resource-id='p.field.value.value'
    />
</template>
