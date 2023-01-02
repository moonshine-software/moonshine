<script setup lang="ts">
import {useI18n} from "vue-i18n";
import {PageChangeEventData, PaginationShownInfoI18nData, Paginator, PaginatorLink} from "../../../entites/paginator";
import {computed, ComputedRef} from "vue";
import TheIcon from "../Icons/TheIcon.vue";
import {useAppStore} from "../../../store/app";

const appStore = useAppStore()
const p = defineProps<{ data: Paginator }>()
const {t} = useI18n();

const emit = defineEmits<{ (e: 'page:change', data: PageChangeEventData): void }>()

const selectPage = (page: PaginatorLink): void => {
  if (!page.number || page.active) {
    return;
  }

  const emitData: PageChangeEventData = {
    number: page.number
  }

  emit('page:change', emitData)
}

const prevPage = () => {
  if (p.data.current_page > 1) {
    emit('page:change', {number: p.data.current_page - 1})
  }
}

const nextPage = () => {
  if (p.data.current_page < p.data.last_page) {
    emit('page:change', {number: p.data.current_page + 1})
  }
}

const getPaginatorLinkClass = (page: PaginatorLink) => {
  return {
    'm-paginator__item--active': page.active,
    'm-paginator__item--disabled': !page.number
  }
}


// items before and after current page
const delta = computed(() => appStore.theme.isSmallDevice ? 1 : 4)

const pages: ComputedRef<PaginatorLink[]> = computed(() => {
  const last = p.data.last_page,
      current = p.data.current_page,
      left = current - delta.value,
      right = current + delta.value + 1,
      pages = [];

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= left && i < right)) {
      pages.push({number: i, active: i === current, label: String(i)});
    }
    if (last > delta.value * 2 && ((i === 2 && left > 2) || (i === last - 1 && right < last - 1))) {
      pages.push({number: null, active: false, label: '...'});
    }
  }

  return pages
})

const shownInfoData: ComputedRef<PaginationShownInfoI18nData> = computed(() => {
  return {
    from: p.data.from,
    to: p.data.to,
    total: p.data.total,
    per_page: p.data.per_page > p.data.total ? p.data.total : p.data.per_page
  }
})

</script>

<template>
  <section class="m-paginator">
    <ul class="m-paginator__items-wrapper" v-if="data.last_page > 1">
      <li
          @click="prevPage"
          class="m-paginator__item px-2"
          :class="{'m-paginator__item--disabled': data.current_page < 2}">
        <TheIcon icon="chevron-left" class="w-4 h-4"/>
      </li>

      <li
          v-for="(page, k) in pages"
          :key="`paginator-link-${page.label}-${k}`"
          class="m-paginator__item"
          :class="getPaginatorLinkClass(page)"
          @click.stop="selectPage(page)"
          v-text="page.label"
      ></li>

      <li @click="nextPage"
          class="m-paginator__item px-2"
          :class="{'m-paginator__item--disabled': data.current_page === data.last_page}">
        <TheIcon icon="chevron-right" class="w-4 h-4"/>
      </li>
    </ul>

    <p class="m-paginator__resource-info">
      {{ t('ui.pagination.shown', shownInfoData) }}
    </p>
  </section>
</template>

<style lang="scss">
.m-paginator {
  @apply text-center text-sm;

  &__items-wrapper {
    @apply flex justify-center items-center;
  }

  &__item {
    @apply select-none;
    @apply px-3 py-2;
    @apply bg-secondary-200 dark:bg-secondary-700;
    @apply cursor-pointer hover:text-brand;
    @apply transition-colors;

    &:nth-child(2) {
      @apply rounded-l-full pl-4;
    }

    &:nth-last-child(-n+2) {
      @apply rounded-r-full pr-4;
    }

    &:first-child, &:last-child {
      @apply rounded-full px-3;
    }

    &:first-child {
      @apply mr-2;
    }

    &:last-child {
      @apply ml-2;
    }

    &--active {
      @apply bg-brand dark:bg-brand text-secondary-50 shadow-md scale-[110%] rounded;
      @apply cursor-default hover:text-secondary-50;
      /*@apply rounded #{!important};
      @apply px-3 #{!important};*/
    }

    &--disabled {
      @apply hover:text-secondary-400 text-secondary-400 cursor-default;
    }
  }

  &__resource-info {
    @apply text-secondary-400 mt-2;
  }
}
</style>
