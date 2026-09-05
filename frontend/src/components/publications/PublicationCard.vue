<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

import ReservationToggle from '@/components/publications/ReservationToggle.vue'

const props = defineProps({
  publication: { type: Object, required: true },
  // When true (History), a Driver post shows an interactive reservation
  // toggle for its owner instead of the passive "Reserved" indicator other
  // users see on Home/Passenger/Driver.
  ownerControls: { type: Boolean, default: false },
})

const emit = defineEmits(['click', 'reservation-updated'])

const { t, locale } = useI18n()

const isPassenger = computed(() => props.publication.status === 'passenger')
const isDriver = computed(() => props.publication.status === 'driver')

// The backend's effective state: true once the departure deadline has
// passed OR the owner has switched reservation on - whichever comes
// first. Shown the same way everywhere a Driver post appears, including
// to the owner in History, so they can see their own post has gone
// unavailable even if they never touched the toggle.
const isUnavailable = computed(() => isDriver.value && props.publication.is_unavailable === true)

// The owner turned reservation on manually vs. it lapsed on its own -
// only the latter needs an extra caption, since the toggle already says
// "ON" for the former.
const isUnavailableFromDeadlineOnly = computed(
  () => isUnavailable.value && props.publication.reservation_enabled !== true,
)

const statusBadgeClass = computed(() => {
  if (isUnavailable.value) {
    return 'bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200'
  }
  return isPassenger.value
    ? 'bg-passenger-50 text-passenger-700 ring-1 ring-inset ring-passenger-200'
    : 'bg-driver-50 text-driver-700 ring-1 ring-inset ring-driver-200'
})

const accentBarClass = computed(() => {
  if (isUnavailable.value) return 'bg-slate-300'
  return isPassenger.value ? 'bg-passenger-500' : 'bg-driver-500'
})

function locationLabel(location) {
  if (!location) return ''
  return locale.value === 'ar' ? location.name_ar : location.name_fr
}

const genderLabel = computed(() =>
  props.publication.author?.gender === 'female' ? t('auth.register.genderFemale') : t('auth.register.genderMale'),
)

const relativeDate = computed(() => {
  const date = new Date(props.publication.departure_date)
  return date.toLocaleDateString(locale.value === 'ar' ? 'ar-TN' : locale.value === 'fr' ? 'fr-TN' : 'en-GB', {
    day: '2-digit',
    month: 'short',
  })
})

function onReservationChange(newValue) {
  emit('reservation-updated', { reservation_enabled: newValue, is_unavailable: newValue || props.publication.is_unavailable })
}
</script>

<template>
  <div
    class="group relative flex w-full flex-col overflow-hidden rounded-xl border bg-white shadow-card transition-shadow hover:shadow-md"
    :class="isUnavailable ? 'border-slate-200 bg-slate-50/60' : 'border-slate-200'"
  >
    <span class="absolute inset-y-0 start-0 w-1" :class="accentBarClass" />

    <button type="button" class="flex flex-col text-start" @click="emit('click')">
      <div class="flex items-center justify-between gap-2 px-4 pt-4">
        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusBadgeClass">
          {{ t(`publication.status.${publication.status}`) }}
        </span>
        <span v-if="isUnavailable" class="text-xs font-medium text-slate-400">
          {{ t('publication.reservation.reserved') }}
        </span>
        <span v-else-if="publication.is_own" class="text-xs font-medium text-slate-400">{{ t('publication.own') }}</span>
      </div>

      <div class="flex items-center gap-3 px-4 pb-3 pt-3" :class="isUnavailable ? 'opacity-70' : ''">
        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-semibold text-slate-900">{{ locationLabel(publication.departure_location) }}</p>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0 text-slate-300 rtl:rotate-180">
          <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
        </svg>
        <div class="min-w-0 flex-1 text-end">
          <p class="truncate text-sm font-semibold text-slate-900">{{ locationLabel(publication.arrival_location) }}</p>
        </div>
      </div>

      <div class="flex items-center justify-between gap-3 border-t border-slate-100 px-4 py-3 text-xs text-slate-500">
        <span class="flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
            <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" />
          </svg>
          {{ genderLabel }}
        </span>
        <span class="font-medium text-slate-700">{{ t('publication.card.seats', publication.available_seats) }}</span>
        <span>{{ relativeDate }}</span>
      </div>
    </button>

    <div v-if="ownerControls && isDriver" class="border-t border-slate-100 px-4 py-3" @click.stop>
      <ReservationToggle
        :publication-id="publication.id"
        :model-value="publication.reservation_enabled === true"
        @update:model-value="onReservationChange"
      />
      <p v-if="isUnavailableFromDeadlineOnly" class="mt-2 text-xs text-slate-400">
        {{ t('publication.reservation.deadlinePassed') }}
      </p>
    </div>
  </div>
</template>
