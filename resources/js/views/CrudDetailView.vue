<template>
    <div v-for='component in view.components'>
        <component :is='component.getComponent()'
                   :resource-key='resource.uri'
                   :view-key='view.uri'
                   :view-component-key='component.uri'
                   :resourceId='resource.id'
        />
    </div>
</template>

<script lang='ts'>
import { defineComponent, PropType } from 'vue'
import { View } from '../entites/view'
import { Resource } from '../entites/resource/base'

export default defineComponent({
    props: {
        resource: {
            type: Object as PropType<Resource>,
            required: true,
        },
        view: {
            type: Object as PropType<View>,
            required: true,
        },
    },
})
</script>
