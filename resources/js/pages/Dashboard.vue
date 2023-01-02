<template>
  <AuthPageLayout>
    <template #header>
      <BlockTitle class="text-3xl text-brand">
        {{ appName }}
      </BlockTitle>
    </template>

    <section v-for="block in dashboard.blocks" class="grid xs:grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4"
             v-if="loaded">
      <ValueMetric
          v-for="metric in block"
          :key="metric.id"
          :value="metric.value"
          :title="metric.label"/>
    </section>
    <Loader v-else/>

  </AuthPageLayout>
</template>

<script setup lang="ts">
import {useDashboardStore} from "../store/dashboard";
import {computed} from "vue";
import ValueMetric from "../components/Metrics/ValueMetric.vue";
import {useAppStore} from "../store/app";
import BlockTitle from "../components/UI/BlockTitle.vue";
import Loader from "../components/UI/Loader.vue";
import AuthPageLayout from "../layouts/AuthPageLayout.vue";
import {useViewStore} from "../store/view";
import TableComponent from '../components/ViewComponents/TableComponent.vue'

const
    dashboard = useDashboardStore(),
    loaded = computed(() => dashboard.loaded),
    appName = useAppStore().app?.name

dashboard.fetch().then(() => useViewStore().loaded = true)

</script>
