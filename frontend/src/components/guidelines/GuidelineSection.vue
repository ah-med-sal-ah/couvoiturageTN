<script setup>
defineProps({
  title: { type: String, required: true },
})
</script>

<template>
  <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
    <h2 class="mb-3 text-base font-semibold text-slate-900">{{ title }}</h2>
    <div class="space-y-3 text-sm leading-relaxed text-slate-600">
      <slot />
    </div>
  </section>
</template>
