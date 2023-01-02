<template>
    <CardBlockLayout>

        <template #title>{{ form.title }}</template>

        <template #default>
            <component
                :is="field.getViewComponent()"
                v-for="field in form.nonRelationFields"
                :key="`field-${field.id ?? field.name}`"
                :field="field"
            />
        </template>
    </CardBlockLayout>

    <component
        :is="field.getViewComponent()"
        v-for="field in form.relationFields"
        :key="`rel-field-${field.id ?? field.name}`"
        :field="field"/>

</template>

<script setup lang="ts">
import CardBlockLayout from "../../layouts/CardBlockLayout.vue";
import {useFormStore} from "../../store/form_component";
import {PrimaryKey} from "../../entites/primary_key";

const p = defineProps<{
    resourceKey: string,
    viewKey: string,
    viewComponentKey: string,
    resourceId: PrimaryKey
}>()

const form = useFormStore(`${p.resourceKey}-${p.resourceId}`)
form.fetch(p.resourceKey, p.viewKey, p.viewComponentKey, p.resourceId)
</script>
