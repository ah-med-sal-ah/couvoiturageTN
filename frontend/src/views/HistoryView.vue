<script setup>
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import PublicationDetails from '@/components/publications/PublicationDetails.vue'
import PublicationList from '@/components/publications/PublicationList.vue'
import { usePublications } from '@/composables/usePublications'

const { t } = useI18n()
const { publications, loading, loadingMore, error, hasMore, status, load, loadMore, setStatus, patchLocal } = usePublications({
  mine: true,
})

const selected = ref(null)

const tabs = [
  { value: null, labelKey: 'history.tabs.all' },
  { value: 'driver', labelKey: 'history.tabs.driver' },
  { value: 'passenger', labelKey: 'history.tabs.passenger' },
]

const emptyMessageKey = {
  null: 'history.empty',
  driver: 'history.emptyDriver',
  passenger: 'history.emptyPassenger',
}

onMounted(load)
</script>

<template>
  <div class="space-y-4">
    <p class="-mt-3 text-sm text-slate-500">{{ t('history.myPosts') }}</p>

    <div class="inline-flex rounded-lg border border-slate-200 bg-white p-1">
      <button
        v-for="tab in tabs"
        :key="tab.labelKey"
        type="button"
        class="rounded-md px-4 py-1.5 text-sm font-medium transition-colors"
        :class="status === tab.value ? 'bg-brand-700 text-white' : 'text-slate-600 hover:bg-slate-50'"
        @click="setStatus(tab.value)"
      >
        {{ t(tab.labelKey) }}
      </button>
    </div>

    <PublicationList
      :publications="publications"
      :loading="loading"
      :loading-more="loadingMore"
      :error="error"
      :has-more="hasMore"
      :empty-message="t(emptyMessageKey[status ?? 'null'])"
      owner-controls
      @retry="load()"
      @load-more="loadMore"
      @select="selected = $event"
      @reservation-updated="patchLocal"
    />

    <PublicationDetails v-if="selected" :publication="selected" @close="selected = null" />
  </div>
</template>
