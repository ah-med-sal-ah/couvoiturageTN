<script setup>
import { computed, onMounted, onUnmounted, ref, useId, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import { useLocationsStore } from '@/stores/locations'

const props = defineProps({
  modelValue: { type: [Number, String, null], default: null },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  error: { type: String, default: '' },
  excludeId: { type: [Number, String, null], default: null },
  name: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const { locale, t } = useI18n()
const store = useLocationsStore()
const inputId = useId()

const query = ref('')
const isOpen = ref(false)
const rootEl = ref(null)

onMounted(() => {
  store.ensureLoaded()
  document.addEventListener('click', onClickOutside)
})

onUnmounted(() => document.removeEventListener('click', onClickOutside))

function onClickOutside(event) {
  if (rootEl.value && !rootEl.value.contains(event.target)) {
    isOpen.value = false
  }
}

function labelFor(location) {
  return locale.value === 'ar' ? location.name_ar : location.name_fr
}

const selectedLocation = computed(() => store.locations.find((l) => l.id === props.modelValue) || null)

watch(
  selectedLocation,
  (loc) => {
    query.value = loc ? labelFor(loc) : ''
  },
  { immediate: true },
)

const filtered = computed(() => {
  const q = query.value.trim().toLowerCase()
  return store.locations
    .filter((location) => location.id !== props.excludeId)
    .filter((location) => {
      if (!q) return true
      return (
        location.name_fr.toLowerCase().includes(q) ||
        location.name_ar.includes(q) ||
        (location.governorate_fr || '').toLowerCase().includes(q)
      )
    })
    .slice(0, 40)
})

function select(location) {
  emit('update:modelValue', location.id)
  query.value = labelFor(location)
  isOpen.value = false
}

function onInput() {
  isOpen.value = true
  if (props.modelValue && query.value !== labelFor(selectedLocation.value)) {
    emit('update:modelValue', null)
  }
}

function onFocus() {
  isOpen.value = true
}
</script>

<template>
  <div ref="rootEl" class="relative">
    <label v-if="label" :for="inputId" class="form-label">{{ label }}</label>
    <div class="relative">
      <input
        :id="inputId"
        v-model="query"
        :name="name"
        type="text"
        class="form-input pe-9"
        :class="{ '!border-passenger-400': error }"
        :placeholder="placeholder || t('common.searchLocation')"
        autocomplete="off"
        @input="onInput"
        @focus="onFocus"
      />
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute end-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
      </svg>
    </div>
    <p v-if="error" class="form-error">{{ error }}</p>

    <div
      v-if="isOpen"
      class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white py-1 shadow-card"
    >
      <p v-if="store.loading" class="px-3 py-2 text-sm text-slate-400">{{ t('common.loading') }}</p>
      <p v-else-if="!filtered.length" class="px-3 py-2 text-sm text-slate-400">{{ t('common.noResults') }}</p>
      <button
        v-for="location in filtered"
        :key="location.id"
        type="button"
        class="flex w-full items-center justify-between px-3 py-2 text-start text-sm hover:bg-slate-50"
        :class="location.id === modelValue ? 'bg-brand-50 text-brand-800' : 'text-slate-700'"
        @click="select(location)"
      >
        <span>{{ locale === 'ar' ? location.name_ar : location.name_fr }}</span>
        <span class="text-xs text-slate-400">{{ location.governorate_fr }}</span>
      </button>
    </div>
  </div>
</template>
