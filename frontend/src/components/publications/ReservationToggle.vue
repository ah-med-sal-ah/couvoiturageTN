<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

import { updateReservation } from '@/api/publications'
import { extractErrorMessage } from '@/lib/http'
import { useNotificationStore } from '@/stores/notifications'

const props = defineProps({
  publicationId: { type: [Number, String], required: true },
  modelValue: { type: Boolean, required: true },
})

const emit = defineEmits(['update:modelValue'])

const { t } = useI18n()
const notifications = useNotificationStore()
const pending = ref(false)

async function toggle() {
  // Ignore repeated clicks while a request is already in flight.
  if (pending.value) return

  const next = !props.modelValue
  pending.value = true

  try {
    const { data } = await updateReservation(props.publicationId, next)
    emit('update:modelValue', data.data.reservation_enabled)
  } catch (error) {
    // Nothing to roll back - the toggle only reflects state confirmed by
    // the backend, so a failed request simply leaves it as it was.
    notifications.error(extractErrorMessage(error, t('publication.reservation.updateError')))
  } finally {
    pending.value = false
  }
}
</script>

<template>
  <div class="flex items-center justify-between gap-3">
    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
      {{ t('publication.reservation.label') }}
    </span>
    <button
      type="button"
      role="switch"
      :aria-checked="modelValue"
      :aria-busy="pending"
      class="inline-flex h-7 min-w-[76px] items-center justify-center rounded-full px-3 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1"
      :class="[
        pending ? 'cursor-wait opacity-70' : 'cursor-pointer',
        modelValue
          ? 'bg-emerald-600 text-white focus:ring-emerald-300'
          : 'bg-slate-200 text-slate-600 focus:ring-slate-300',
      ]"
      :disabled="pending"
      @click="toggle"
    >
      {{ pending ? t('publication.reservation.updating') : modelValue ? t('publication.reservation.on') : t('publication.reservation.off') }}
    </button>
  </div>
</template>
