<template>
    <AuthPageLayout>
        <template #title>{{ title }}</template>

        <component v-if='Object.keys(view.class).length'
                   :is='view.class.getComponent()'
                   :view='view'
                   :resource='resource'
        />
    </AuthPageLayout>
</template>

<script setup lang='ts'>
import AuthPageLayout from '../layouts/AuthPageLayout.vue'
import { computed } from 'vue'
import { usePageStore } from '../store/page'
import CrudIndexView from '../views/CrudIndexView.vue'

const
    pageStore = usePageStore(),
    resource = computed(() => pageStore.resource),
    title = computed(() => pageStore.resource.title),
    view = computed(() => pageStore.view)
</script>
