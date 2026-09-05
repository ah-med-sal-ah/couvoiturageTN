<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

/**
 * Simple, dependency-free bar chart (flex/CSS, not SVG) for a single
 * magnitude-over-time series - one hue, per the dataviz color formula for
 * this job. Labels are shown selectively (every Nth bar) once there are
 * enough days that printing all of them would collide; the exact value is
 * always available via the native title tooltip and isn't lost.
 */
const props = defineProps({
  data: {
    type: Array, // [{ date, count }]
    required: true,
  },
  emptyMessage: { type: String, default: '' },
})

const { locale } = useI18n()

const max = computed(() => Math.max(1, ...props.data.map((d) => d.count)))

// Avoid crowding the x-axis: only print a label every Nth bar once there
// are more than ~10 days of data.
const labelStep = computed(() => Math.max(1, Math.ceil(props.data.length / 10)))

function shortDate(dateStr) {
  const date = new Date(`${dateStr}T00:00:00`)
  return date.toLocaleDateString(locale.value === 'ar' ? 'ar-TN' : locale.value === 'fr' ? 'fr-TN' : 'en-GB', {
    day: '2-digit',
    month: 'short',
  })
}
</script>

<template>
  <div v-if="!data.length" class="flex h-48 items-center justify-center text-sm text-slate-400">
    {{ emptyMessage }}
  </div>

  <div v-else>
    <div class="flex h-48 items-end gap-1">
      <div
        v-for="day in data"
        :key="day.date"
        class="group relative flex flex-1 items-end self-stretch"
      >
        <div
          class="mx-auto w-full max-w-[28px] rounded-t bg-brand-600 transition-colors group-hover:bg-brand-700"
          :style="{ height: `${(day.count / max) * 100}%`, minHeight: day.count > 0 ? '3px' : '0' }"
        >
          <span class="sr-only">{{ day.date }}: {{ day.count }}</span>
        </div>
        <div
          class="pointer-events-none absolute -top-7 start-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded bg-slate-900 px-1.5 py-0.5 text-[11px] font-medium text-white group-hover:block"
        >
          {{ day.count }}
        </div>
      </div>
    </div>
    <div class="mt-1.5 flex gap-1 text-[10px] text-slate-400">
      <div v-for="(day, index) in data" :key="day.date" class="flex-1 truncate text-center">
        {{ index % labelStep === 0 ? shortDate(day.date) : '' }}
      </div>
    </div>
  </div>
</template>
