<script setup>
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import { createPublication } from '@/api/publications'
import LocationSelector from '@/components/common/LocationSelector.vue'
import StatusRadioGroup from '@/components/publications/StatusRadioGroup.vue'
import { extractErrorMessage, isGloballyHandled } from '@/lib/http'
import { useNotificationStore } from '@/stores/notifications'

const props = defineProps({
  defaultStatus: { type: String, default: 'passenger' },
})

const emit = defineEmits(['created', 'cancel'])

const { t } = useI18n()
const notifications = useNotificationStore()

const form = reactive({
  status: props.defaultStatus,
  departure_location_id: null,
  arrival_location_id: null,
  available_seats: 1,
  remarks: '',
  departure_date: '',
  departure_time: '',
  phone: '',
  facebook: '',
  instagram: '',
})

const errors = ref({})
const submitting = ref(false)
const todayIso = new Date().toISOString().slice(0, 10)

function fieldError(field) {
  return errors.value[field]?.[0] || ''
}

function validate() {
  const localErrors = {}

  if (!form.departure_location_id) localErrors.departure_location_id = [t('common.required')]
  if (!form.arrival_location_id) localErrors.arrival_location_id = [t('common.required')]
  if (
    form.departure_location_id &&
    form.arrival_location_id &&
    form.departure_location_id === form.arrival_location_id
  ) {
    localErrors.arrival_location_id = ['The arrival point must be different from the departure point.']
  }
  if (!form.available_seats || form.available_seats < 1 || form.available_seats > 8) {
    localErrors.available_seats = [t('common.required')]
  }
  if (!form.departure_date) localErrors.departure_date = [t('common.required')]
  if (!form.departure_time) localErrors.departure_time = [t('common.required')]
  if (!/^(\+216)?[0-9]{8}$/.test(form.phone.trim())) {
    localErrors.phone = ['Enter a valid Tunisian phone number (8 digits, optionally prefixed with +216).']
  }

  errors.value = localErrors
  return Object.keys(localErrors).length === 0
}

async function submit() {
  if (!validate()) return

  submitting.value = true
  try {
    const { data } = await createPublication(form)
    notifications.success(t('publication.create.success'))
    emit('created', data.data)
  } catch (error) {
    if (error?.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    } else if (!isGloballyHandled(error)) {
      notifications.error(extractErrorMessage(error))
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <form class="space-y-5" @submit.prevent="submit">
    <div>
      <label class="form-label">{{ t('publication.create.statusLabel') }}</label>
      <StatusRadioGroup v-model="form.status" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <LocationSelector
        v-model="form.departure_location_id"
        name="departure_location_id"
        :label="t('publication.fields.departure')"
        :error="fieldError('departure_location_id')"
        :exclude-id="form.arrival_location_id"
      />
      <LocationSelector
        v-model="form.arrival_location_id"
        name="arrival_location_id"
        :label="t('publication.fields.arrival')"
        :error="fieldError('arrival_location_id')"
        :exclude-id="form.departure_location_id"
      />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="form-label">{{ t('publication.fields.seats') }}</label>
        <input v-model.number="form.available_seats" name="available_seats" type="number" min="1" max="8" class="form-input" :class="{ '!border-passenger-400': fieldError('available_seats') }" />
        <p v-if="fieldError('available_seats')" class="form-error">{{ fieldError('available_seats') }}</p>
      </div>
      <div>
        <label class="form-label">{{ t('publication.fields.phone') }}</label>
        <input
          v-model="form.phone"
          name="phone"
          type="tel"
          :placeholder="t('publication.create.phonePlaceholder')"
          class="form-input"
          :class="{ '!border-passenger-400': fieldError('phone') }"
        />
        <p v-if="fieldError('phone')" class="form-error">{{ fieldError('phone') }}</p>
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="form-label">{{ t('publication.fields.date') }}</label>
        <input
          v-model="form.departure_date"
          name="departure_date"
          type="date"
          :min="todayIso"
          class="form-input"
          :class="{ '!border-passenger-400': fieldError('departure_date') }"
        />
        <p v-if="fieldError('departure_date')" class="form-error">{{ fieldError('departure_date') }}</p>
      </div>
      <div>
        <label class="form-label">{{ t('publication.fields.time') }}</label>
        <input
          v-model="form.departure_time"
          name="departure_time"
          type="time"
          class="form-input"
          :class="{ '!border-passenger-400': fieldError('departure_time') }"
        />
        <p v-if="fieldError('departure_time')" class="form-error">{{ fieldError('departure_time') }}</p>
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="form-label">{{ t('publication.fields.facebook') }} <span class="text-slate-400">({{ t('common.optional') }})</span></label>
        <input v-model="form.facebook" type="text" :placeholder="t('publication.create.facebookPlaceholder')" class="form-input" />
      </div>
      <div>
        <label class="form-label">{{ t('publication.fields.instagram') }} <span class="text-slate-400">({{ t('common.optional') }})</span></label>
        <input v-model="form.instagram" type="text" :placeholder="t('publication.create.instagramPlaceholder')" class="form-input" />
      </div>
    </div>

    <div>
      <label class="form-label">{{ t('publication.fields.remarks') }} <span class="text-slate-400">({{ t('common.optional') }})</span></label>
      <textarea
        v-model="form.remarks"
        rows="3"
        :placeholder="t('publication.create.remarksPlaceholder')"
        class="form-input resize-none"
      />
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
      <button type="button" class="btn-secondary" :disabled="submitting" @click="emit('cancel')">
        {{ t('publication.create.cancel') }}
      </button>
      <button type="submit" class="btn-primary" :disabled="submitting">
        <svg v-if="submitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        {{ t('publication.create.ok') }}
      </button>
    </div>
  </form>
</template>
