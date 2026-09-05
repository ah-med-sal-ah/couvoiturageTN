<script setup>
import { computed } from 'vue'

/**
 * Minimal, dependency-free donut chart (an SVG circle stroked per segment
 * via stroke-dasharray). Built by hand rather than pulling in a charting
 * library for two segments - see the dataviz guidance this project
 * follows: fixed categorical colors assigned by entity (passenger/driver
 * keep their app-wide red/blue everywhere, including here), a small
 * surface-color gap between segments instead of a border, a legend with
 * direct value + percentage labels since there are only two series, and a
 * native <title> per segment as the hover affordance.
 */
const props = defineProps({
  segments: {
    type: Array, // [{ label, value, percentage, color }]
    required: true,
  },
  size: { type: Number, default: 168 },
  thickness: { type: Number, default: 26 },
  emptyMessage: { type: String, default: '' },
  centerLabel: { type: String, default: '' },
})

const total = computed(() => props.segments.reduce((sum, s) => sum + s.value, 0))
const radius = computed(() => (props.size - props.thickness) / 2)
const circumference = computed(() => 2 * Math.PI * radius.value)
const center = computed(() => props.size / 2)
const gap = 4

const arcs = computed(() => {
  if (total.value <= 0) return []

  let cumulative = 0
  return props.segments.map((segment) => {
    const rawLength = (segment.value / total.value) * circumference.value
    const visibleLength = Math.max(rawLength - gap, 0)
    const arc = {
      ...segment,
      dasharray: `${visibleLength} ${circumference.value - visibleLength}`,
      dashoffset: -cumulative,
    }
    cumulative += rawLength
    return arc
  })
})
</script>

<template>
  <div v-if="total <= 0" class="flex h-40 items-center justify-center text-sm text-slate-400">
    {{ emptyMessage }}
  </div>

  <div v-else class="flex flex-col items-center gap-5 sm:flex-row sm:items-center">
    <svg :width="size" :height="size" :viewBox="`0 0 ${size} ${size}`" role="img">
      <circle
        :cx="center"
        :cy="center"
        :r="radius"
        fill="none"
        class="stroke-slate-100"
        :stroke-width="thickness"
      />
      <g :transform="`rotate(-90 ${center} ${center})`">
        <circle
          v-for="arc in arcs"
          :key="arc.label"
          :cx="center"
          :cy="center"
          :r="radius"
          fill="none"
          :stroke="arc.color"
          :stroke-width="thickness"
          :stroke-dasharray="arc.dasharray"
          :stroke-dashoffset="arc.dashoffset"
          stroke-linecap="butt"
        >
          <title>{{ arc.label }}: {{ arc.value }} ({{ arc.percentage }}%)</title>
        </circle>
      </g>
      <text
        v-if="centerLabel"
        :x="center"
        :y="center - 4"
        text-anchor="middle"
        class="fill-slate-900 text-xl font-semibold"
      >
        {{ total }}
      </text>
      <text
        v-if="centerLabel"
        :x="center"
        :y="center + 16"
        text-anchor="middle"
        class="fill-slate-400 text-[10px] uppercase tracking-wide"
      >
        {{ centerLabel }}
      </text>
    </svg>

    <ul class="w-full space-y-2 sm:w-auto">
      <li v-for="segment in segments" :key="segment.label" class="flex items-center gap-2.5 text-sm">
        <span class="h-2.5 w-2.5 shrink-0 rounded-full" :style="{ backgroundColor: segment.color }" />
        <span class="flex-1 text-slate-600">{{ segment.label }}</span>
        <span class="font-semibold text-slate-900">{{ segment.value }}</span>
        <span class="w-12 text-end text-xs text-slate-400">{{ segment.percentage }}%</span>
      </li>
    </ul>
  </div>
</template>
