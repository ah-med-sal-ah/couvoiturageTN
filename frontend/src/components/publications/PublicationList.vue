<script setup>
import { useI18n } from 'vue-i18n'

import PublicationCard from '@/components/publications/PublicationCard.vue'

defineProps({
  publications: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  loadingMore: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hasMore: { type: Boolean, default: false },
  emptyMessage: { type: String, required: true },
  // Renders the owner-only reservation toggle on Driver cards (History only).
  ownerControls: { type: Boolean, default: false },
})

const emit = defineEmits(['retry', 'load-more', 'select', 'reservation-updated'])

const { t } = useI18n()
</script>

<template>
  <div>
    <div v-if="loading" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <div v-for="i in 6" :key="i" class="h-32 animate-pulse rounded-xl bg-slate-100" />
    </div>

    <div v-else-if="error" class="rounded-xl border border-passenger-200 bg-passenger-50 p-6 text-center text-sm text-passenger-700">
      {{ error }}
      <button type="button" class="mt-3 block w-full font-semibold underline" @click="emit('retry')">
        {{ t('common.retry') }}
      </button>
    </div>

    <div v-else-if="!publications.length" class="rounded-xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500">
      {{ emptyMessage }}
    </div>

    <template v-else>
      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <PublicationCard
          v-for="publication in publications"
          :key="publication.id"
          :publication="publication"
          :owner-controls="ownerControls"
          @click="emit('select', publication)"
          @reservation-updated="(patch) => emit('reservation-updated', publication.id, patch)"
        />
      </div>

      <div v-if="hasMore" class="pt-2 text-center">
        <button type="button" class="btn-secondary" :disabled="loadingMore" @click="emit('load-more')">
          {{ loadingMore ? t('common.loading') : t('publication.loadMore') }}
        </button>
      </div>
    </template>
  </div>
</template>
