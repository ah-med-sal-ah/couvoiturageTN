<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

import BaseModal from '@/components/common/BaseModal.vue'
import AppIcon from '@/components/common/AppIcon.vue'

const props = defineProps({
  publication: { type: Object, required: true },
})

defineEmits(['close'])

const { t, locale } = useI18n()

const isPassenger = computed(() => props.publication.status === 'passenger')
const statusBadgeClass = computed(() =>
  isPassenger.value ? 'bg-passenger-50 text-passenger-700 ring-1 ring-inset ring-passenger-200' : 'bg-driver-50 text-driver-700 ring-1 ring-inset ring-driver-200',
)

function locationLabel(location) {
  if (!location) return ''
  return locale.value === 'ar' ? location.name_ar : location.name_fr
}

const genderLabel = computed(() =>
  props.publication.author?.gender === 'female' ? t('auth.register.genderFemale') : t('auth.register.genderMale'),
)

const formattedDate = computed(() => {
  const date = new Date(props.publication.departure_date)
  return date.toLocaleDateString(locale.value === 'ar' ? 'ar-TN' : locale.value === 'fr' ? 'fr-TN' : 'en-GB', {
    weekday: 'short',
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
})

// null for Passenger posts - reservation availability only applies to Driver posts.
const showsReservation = computed(() => props.publication.is_unavailable !== null)

const contactLinks = computed(() => {
  const links = []
  if (props.publication.facebook) links.push({ key: 'facebook', value: props.publication.facebook })
  if (props.publication.instagram) links.push({ key: 'instagram', value: props.publication.instagram })
  return links
})
</script>

<template>
  <BaseModal :title="t('publication.details.title')" size="md" @close="$emit('close')">
    <div class="space-y-5">
      <div class="flex items-center justify-between">
        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold" :class="statusBadgeClass">
          {{ t(`publication.status.${publication.status}`) }}
        </span>
        <div class="flex items-center gap-2">
          <img v-if="publication.author?.profile_photo_url" :src="publication.author.profile_photo_url" alt="" class="h-8 w-8 rounded-full object-cover" />
          <div v-else class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-brand-800">
            <AppIcon name="profile" :size="16" />
          </div>
          <span class="text-sm text-slate-600">{{ publication.author?.full_name }}</span>
        </div>
      </div>

      <div class="rounded-xl border border-slate-200 p-4">
        <div class="flex items-start gap-3">
          <div class="flex flex-col items-center pt-1">
            <span class="h-2.5 w-2.5 rounded-full bg-slate-900" />
            <span class="my-1 h-8 w-px bg-slate-300" />
            <span class="h-2.5 w-2.5 rounded-full border-2 border-slate-900 bg-white" />
          </div>
          <div class="flex-1 space-y-4">
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ t('publication.fields.departure') }}</p>
              <p class="text-sm font-semibold text-slate-900">{{ locationLabel(publication.departure_location) }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ t('publication.fields.arrival') }}</p>
              <p class="text-sm font-semibold text-slate-900">{{ locationLabel(publication.arrival_location) }}</p>
            </div>
          </div>
        </div>
      </div>

      <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ t('publication.fields.date') }}</dt>
          <dd class="mt-0.5 font-medium text-slate-800">{{ formattedDate }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ t('publication.fields.time') }}</dt>
          <dd class="mt-0.5 font-medium text-slate-800">{{ publication.departure_time }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ t('publication.fields.seats') }}</dt>
          <dd class="mt-0.5 font-medium text-slate-800">{{ publication.available_seats }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ t('publication.fields.gender') }}</dt>
          <dd class="mt-0.5 font-medium text-slate-800">{{ genderLabel }}</dd>
        </div>
        <div v-if="showsReservation">
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ t('publication.reservation.label') }}</dt>
          <dd class="mt-0.5 flex items-center gap-1.5 font-medium" :class="publication.is_unavailable ? 'text-slate-500' : 'text-emerald-600'">
            <span class="h-1.5 w-1.5 rounded-full" :class="publication.is_unavailable ? 'bg-slate-400' : 'bg-emerald-500'" />
            {{ publication.is_unavailable ? t('publication.reservation.reserved') : t('publication.reservation.available') }}
          </dd>
        </div>
      </dl>

      <div v-if="publication.remarks" class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">
        {{ publication.remarks }}
      </div>

      <div class="border-t border-slate-100 pt-4">
        <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-400">{{ t('publication.details.contact') }}</p>
        <div class="flex flex-wrap gap-2">
          <a :href="`tel:${publication.phone}`" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
            {{ publication.phone }}
          </a>
          <span v-for="link in contactLinks" :key="link.key" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700">
            {{ t(`publication.fields.${link.key}`) }}: {{ link.value }}
          </span>
        </div>
      </div>
    </div>
  </BaseModal>
</template>
