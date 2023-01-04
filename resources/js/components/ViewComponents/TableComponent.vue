<script setup lang="ts">
import Paginator from "@/components/UI/Pagination/Paginator.vue";
import SearchInput from "../UI/SearchInput.vue";
import ColumnsFilter from "./../Table/ColumnsFilter.vue";
import TransitionFade from "../Transitions/TransitionFade.vue";
import {computed, Ref, ref} from "vue";
import {useTableStore} from "../../store/table_component";
import BlockTitle from "../UI/BlockTitle.vue";
import TheButton from "../UI/Buttons/TheButton.vue";
import TheHiddenSelect from "../UI/Inputs/TheHiddenSelect.vue";
import {useI18n} from "vue-i18n";
import ActionsCell from "./../Table/ActionsCell.vue";
import TheCard from "../UI/TheCard.vue"
import TableLoader from "../UI/Loaders/TableLoader.vue";
import ColumnSortIcon from "../UI/Icons/ColumnSortIcon.vue";
import {PageChangeEventData} from "../../entites/paginator";

const props = defineProps<{
    resourceKey: string,
    viewKey: string,
    viewComponentKey: string
}>()

type TableSize = 'xs' | 'sm' | 'base' | 'lg'

const
    table = useTableStore(props.resourceKey),
    selectedSize: Ref<TableSize> = ref('sm'),
    {t} = useI18n(),
    sizes = [
      {
        value: 'xs',
        label: t('ui.extra-small')
      },
      {
        value: 'sm',
        label: t('ui.small')
      },
      {
        value: 'base',
        label: t('ui.normal')
      },
      {
        value: 'lg',
        label: t('ui.big')
      },
    ],
    showAsCards = ref(false),
    fetch = (page: number = 1) => table.fetch(props.resourceKey, props.viewKey, props.viewComponentKey, page),
    sort = (columnKey: string) => {
      table.switchSort(columnKey)
      fetch(table.currentPage)
    },
    wrapperComponent = computed(() => showAsCards.value ? 'section' : TheCard),
    changePage = (e: PageChangeEventData) => fetch(e.number),
    search = computed({
      get: () => table.search,
      set: (newVal) => {
        table.search = newVal
        fetch(1)
      }
    })

    fetch()
</script>


<template>
  <div class="space-y-4">
    <!--  TABLE HEADER  -->
    <header class="flex justify-between items-start">
      <BlockTitle>{{ table.title }}</BlockTitle>
    </header>
    <!-- END TABLE HEADER  -->

    <!--  TABLE ACTIONS  -->
    <section class="flex justify-between items-center">
      <SearchInput v-model="search"/>
      <div class="flex gap-2 justify-end items-center w-full">
        <!--  TABLE STYLE  -->
        <TheButton v-if="showAsCards" icon="view-columns" @click="showAsCards = false" square sm secondary/>
        <TheButton v-else icon="squares-2X2" @click="showAsCards = true" square sm secondary/>

        <TheHiddenSelect :options="sizes" v-model="selectedSize">
          <TheButton icon="adjustments-vertical" square sm secondary/>
        </TheHiddenSelect>
        <!--  END TABLE STYLE  -->

        <ColumnsFilter
            :columns="table.visibleColumns"
            v-if="!!table.columns"/>
      </div>
    </section>
    <!--  END TABLE ACTIONS  -->

    <!--  TABLE BODY  -->
    <component :is="wrapperComponent">
      <TransitionFade mode="out-in">
        <div :class="{'m-table__wrapper': !showAsCards}" :key="table.paginator.current_page" v-if="table.loaded">
          <table :class="[`m-table-${selectedSize}`, {'m-card-table': showAsCards, 'm-table': !showAsCards}]">

            <!--  THEAD  -->
            <thead v-if="!showAsCards">
            <tr>
              <!-- TODO: Row selects  -->
              <th
                  @click="sort(column.key)"
                  v-for="column in table.columns"
                  :key="`th-${column.key}`">
                <span> {{ column.label }} </span>
                <ColumnSortIcon
                    v-if="column.sortable"
                    :direction="column.sortDirection"/>
              </th>
              <th>
                {{ t('ui.actions') }}
              </th>
            </tr>
            </thead>
            <!--  END THEAD  -->

            <!--  TBODY  -->
            <tbody>
            <tr
                v-for="row in table.rows"
                :key="`row-${row.id}`">
              <td
                  v-for="field in row.getVisibleFields(table.visibleFields)"
                  :key="`cell-${row.id}-${field.key}`">
                <component
                    :is="showAsCards ? field.getViewComponent() : field.getIndexComponent()"
                    :field="field"/>
              </td>

              <ActionsCell :resource-id="row.id"/>
            </tr>
            </tbody>
            <!--  END TBODY  -->

          </table>
        </div>
      </TransitionFade>

      <TableLoader :class="{'opacity-100': table.fetching}"/>
      <Paginator
          class="mt-4"
          :data="table.paginator"
          @page:change="changePage"
          v-if="!!table.paginator"/>

    </component>
    <!--  END TABLE BODY  -->

  </div>
</template>

<style lang="scss" scoped>
@import "../../sass/variables";

.m-table__action-bar {
  @apply flex items-center gap-2 justify-end;
}

.m-table__wrapper {
  @apply overflow-x-scroll;
}

.m-table {
  @apply relative table-auto w-full whitespace-nowrap
  text-sm leading-4 tracking-wider font-normal text-left;

  th, td {
    @apply py-3 px-4 font-normal;
  }

  thead {
    @apply relative uppercase text-secondary-#{$lightness} border-b;
    th {
      @apply cursor-default;
    }
  }

  tbody {
    tr {
      @apply hover:bg-secondary-300/30 dark:hover:bg-secondary-900/40 transition-colors
      border-b border-secondary-200/70 dark:border-secondary-700/70;
    }
  }

  &-xs {
    @apply text-xs;
    th, td {
      @apply py-1 px-2;
    }
  }

  &-sm {
    @apply text-sm;
    th, td {
      @apply py-2 px-3;
    }
  }

  &-base {
    @apply text-base;
    th, td {
      @apply py-3 px-4;
    }
  }

  &-lg {
    @apply text-lg;
    th, td {
      @apply py-4 px-5;
    }
  }

}

.m-card-table {
  tbody {
    @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 #{!important};
  }

  tr {
    @apply bg-secondary-50 dark:bg-secondary-700 border-secondary-100 dark:border-secondary-700;
    @apply border-2 rounded-2xl shadow-xl;
    @apply p-5 py-4;
    @apply flex flex-col justify-between;
  }

  td {
    @apply block;
  }
}
</style>
