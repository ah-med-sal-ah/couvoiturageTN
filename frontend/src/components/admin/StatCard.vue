<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: [Number, String], required: true },
  caption: { type: String, default: '' },
})
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-card">
    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ label }}</p>
    <p class="mt-1.5 text-2xl font-semibold text-slate-900">{{ value }}</p>
    <p v-if="caption" class="mt-0.5 text-xs text-slate-400">{{ caption }}</p>
  </div>
</template>
