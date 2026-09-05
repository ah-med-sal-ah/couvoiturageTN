<script setup>
import { useI18n } from 'vue-i18n'

defineProps({
  modelValue: { type: String, default: null },
})
defineEmits(['update:modelValue'])

const { t } = useI18n()

const options = [
  { value: 'passenger', labelKey: 'publication.create.choosePassenger' },
  { value: 'driver', labelKey: 'publication.create.chooseDriver' },
]

const activeClasses = {
  passenger: 'border-passenger-500 bg-passenger-50 text-passenger-700 ring-1 ring-passenger-500',
  driver: 'border-driver-500 bg-driver-50 text-driver-700 ring-1 ring-driver-500',
}

const dotClasses = {
  passenger: 'border-passenger-500 bg-passenger-500',
  driver: 'border-driver-500 bg-driver-500',
}
</script>

<template>
  <div class="grid grid-cols-2 gap-3">
    <button
      v-for="option in options"
      :key="option.value"
      type="button"
      class="flex items-center gap-2.5 rounded-lg border px-4 py-3 text-sm font-semibold transition-colors"
      :class="modelValue === option.value ? activeClasses[option.value] : 'border-slate-300 bg-white text-slate-500 hover:bg-slate-50'"
      @click="$emit('update:modelValue', option.value)"
    >
      <span
        class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full border-2"
        :class="modelValue === option.value ? dotClasses[option.value] : 'border-slate-300'"
      >
        <span v-if="modelValue === option.value" class="h-1.5 w-1.5 rounded-full bg-white" />
      </span>
      {{ t(option.labelKey) }}
    </button>
  </div>
</template>
