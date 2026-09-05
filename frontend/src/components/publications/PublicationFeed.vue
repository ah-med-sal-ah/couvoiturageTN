<script setup>
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import PublicationDetails from '@/components/publications/PublicationDetails.vue'
import PublicationFilter from '@/components/publications/PublicationFilter.vue'
import PublicationList from '@/components/publications/PublicationList.vue'
import { usePublications } from '@/composables/usePublications'

const props = defineProps({
  // null = Home (all statuses), 'passenger' or 'driver' to scope the feed.
  status: { type: String, default: null },
})

const { t } = useI18n()
const { publications, loading, loadingMore, error, hasMore, isFiltered, filters, load, loadMore, applyFilters, clearFilters, patchLocal } =
  usePublications({ status: props.status })

const selected = ref(null)

onMounted(load)
</script>

<template>
  <div class="space-y-4">
    <PublicationFilter :model-value="filters" @apply="applyFilters" @clear="clearFilters" />

    <PublicationList
      :publications="publications"
      :loading="loading"
      :loading-more="loadingMore"
      :error="error"
      :has-more="hasMore"
      :empty-message="isFiltered ? t('publication.emptyFiltered') : t('publication.empty')"
      @retry="load()"
      @load-more="loadMore"
      @select="selected = $event"
      @reservation-updated="patchLocal"
    />

    <PublicationDetails v-if="selected" :publication="selected" @close="selected = null" />
  </div>
</template>
