<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import LocationSelector from '@/components/common/LocationSelector.vue'

const props = defineProps({
  modelValue: { type: Object, required: true },
})

const emit = defineEmits(['apply', 'clear'])

const { t } = useI18n()

const departureId = ref(props.modelValue.departure_location_id)
const arrivalId = ref(props.modelValue.arrival_location_id)

watch(
  () => props.modelValue,
  (value) => {
    departureId.value = value.departure_location_id
    arrivalId.value = value.arrival_location_id
  },
)

function apply() {
  emit('apply', { departure_location_id: departureId.value, arrival_location_id: arrivalId.value })
}

function clear() {
  departureId.value = null
  arrivalId.value = null
  emit('clear')
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-card">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
      <LocationSelector v-model="departureId" :label="t('publication.filter.departure')" :exclude-id="arrivalId" />
      <LocationSelector v-model="arrivalId" :label="t('publication.filter.arrival')" :exclude-id="departureId" />
      <div class="flex gap-2 lg:col-span-2">
        <button type="button" class="btn-primary flex-1" @click="apply">{{ t('publication.filter.apply') }}</button>
        <button type="button" class="btn-secondary flex-1" @click="clear">{{ t('publication.filter.clear') }}</button>
      </div>
    </div>
  </div>
</template>
